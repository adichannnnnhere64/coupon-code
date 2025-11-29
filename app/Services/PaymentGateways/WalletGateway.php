<?php

declare(strict_types=1);

// app/Services/PaymentGateways/WalletGateway.php

namespace App\Services\PaymentGateways;

use App\Contracts\Services\PaymentGatewayInterface;
use App\DTOs\PaymentConfirmationDTO;
use App\DTOs\PaymentIntentDTO;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use App\ValueObjects\Money;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

final class WalletGateway implements PaymentGatewayInterface
{
    public function createPaymentIntent(PaymentIntentDTO $paymentIntent): array
    {
        // For wallet payments, we immediately check if the user has sufficient balance
        $userId = $paymentIntent->metadata['user_id'] ?? null;

        throw_unless($userId, Exception::class, 'User ID is required for wallet payments');

        $wallet = Wallet::query()->where('user_id', $userId)->first();

        throw_unless($wallet, Exception::class, 'Wallet not found for user');

        $amount = new Money($paymentIntent->amount);

        throw_unless($wallet->hasSufficientBalance($amount), Exception::class, 'Insufficient wallet balance');

        // Generate a unique payment intent ID for wallet
        $paymentIntentId = 'wallet_'.Str::random(12);

        return [
            'payment_intent_id' => $paymentIntentId,
            'status' => 'requires_capture', // Similar to other gateways
            'amount' => $paymentIntent->amount,
            'currency' => $paymentIntent->currency,
            'wallet_balance' => (float) $wallet->balance,
        ];
    }

    public function confirmPayment(string $paymentIntentId): PaymentConfirmationDTO
    {
        try {
            // Extract user ID from metadata or find another way to get it
            // For now, we'll assume the user is authenticated and we can get it from auth

            $userId = auth()->id();
            $wallet = Wallet::query()->where('user_id', $userId)->firstOrFail();

            // In a real scenario, you'd have stored the payment intent details
            // For simplicity, we'll deduct the amount immediately

            $amount = 0.00; // You'd get this from your stored payment intent
            $moneyAmount = new Money($amount);

            DB::transaction(function () use ($wallet, $moneyAmount, $paymentIntentId): void {
                // Deduct from wallet
                $wallet->deductAmount($moneyAmount);

                // Record wallet transaction
                WalletTransaction::query()->create([
                    'wallet_id' => $wallet->id,
                    'type' => 'debit',
                    'amount' => $moneyAmount->getAmount(),
                    'balance_after' => $wallet->balance,
                    'description' => 'Coupon purchase via wallet',
                    'reference_id' => $paymentIntentId,
                    'metadata' => ['purpose' => 'coupon_purchase'],
                ]);
            });

            return new PaymentConfirmationDTO(
                paymentIntentId: $paymentIntentId,
                status: 'succeeded',
                metadata: [
                    'wallet_transaction_id' => $wallet->id,
                    'final_balance' => (float) $wallet->balance,
                ]
            );

        } catch (Exception $e) {
            Log::error('Wallet payment failed: '.$e->getMessage());

            return new PaymentConfirmationDTO(
                paymentIntentId: $paymentIntentId,
                status: 'failed',
                errorMessage: $e->getMessage()
            );
        }
    }

    public function refundPayment(string $paymentIntentId, ?float $amount = null): array
    {
        try {
            $userId = auth()->id();
            $wallet = Wallet::query()->where('user_id', $userId)->firstOrFail();

            // Find the original transaction
            $originalTransaction = WalletTransaction::query()->where('reference_id', $paymentIntentId)
                ->where('type', 'debit')
                ->firstOrFail();

            $refundAmount = $amount ?? $originalTransaction->amount;
            $moneyAmount = new Money($refundAmount);

            DB::transaction(function () use ($wallet, $moneyAmount, $paymentIntentId, $originalTransaction): void {
                // Add back to wallet
                $wallet->addAmount($moneyAmount);

                // Record refund transaction
                WalletTransaction::query()->create([
                    'wallet_id' => $wallet->id,
                    'type' => 'credit',
                    'amount' => $moneyAmount->getAmount(),
                    'balance_after' => $wallet->balance,
                    'description' => 'Refund for coupon purchase',
                    'reference_id' => 'refund_'.$paymentIntentId,
                    'metadata' => [
                        'original_transaction_id' => $originalTransaction->id,
                        'purpose' => 'refund',
                    ],
                ]);
            });

            return [
                'refund_id' => 'refund_'.$paymentIntentId,
                'status' => 'succeeded',
                'amount' => $refundAmount,
            ];

        } catch (Exception $e) {
            Log::error('Wallet refund failed: '.$e->getMessage());
            throw new Exception('Refund failed: '.$e->getMessage(), $e->getCode(), $e);
        }
    }

    public function getPaymentStatus(string $paymentIntentId): string
    {
        // Check if the payment was completed by looking for the transaction
        $transaction = WalletTransaction::query()->where('reference_id', $paymentIntentId)
            ->where('type', 'debit')
            ->first();

        return $transaction ? 'succeeded' : 'requires_payment_method';
    }

    public function supportsWebhooks(): bool
    {
        return false; // Wallet doesn't need webhooks
    }

    public function handleWebhook(array $payload): void
    {
        // Not applicable for wallet
    }

    public function getName(): string
    {
        return 'wallet';
    }
}
