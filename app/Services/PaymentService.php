<?php

declare(strict_types=1);

// app/Services/PaymentService.php

namespace App\Services;

use App\Contracts\Services\PaymentGatewayInterface;
use App\DTOs\PaymentConfirmationDTO;
use App\DTOs\PaymentIntentDTO;
use App\Models\Coupon;
use App\Services\PaymentGateways\PayPalGateway;
use App\Services\PaymentGateways\StripeGateway;
use App\Services\PaymentGateways\WalletGateway;
use Exception;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;

final class PaymentService
{
    private PaymentGatewayInterface $gateway;

    public function __construct(?string $gateway = null)
    {
        $this->setGateway($gateway ?? config('services.default_payment_gateway', 'stripe'));
    }

    public function setGateway(string $gateway): void
    {
        $this->gateway = app($this->getGatewayClass($gateway));
    }

    public function getGateway(): PaymentGatewayInterface
    {
        return $this->gateway;
    }

    public function createPayment(PaymentIntentDTO $paymentIntent): array
    {
        try {
            return $this->gateway->createPaymentIntent($paymentIntent);
        } catch (Exception $e) {
            Log::error("Payment creation failed for {$this->gateway->getName()}: ".$e->getMessage());
            throw $e;
        }
    }

    public function confirmPayment(string $paymentIntentId): PaymentConfirmationDTO
    {
        return $this->gateway->confirmPayment($paymentIntentId);
    }

    public function refundPayment(string $paymentIntentId, ?float $amount = null): array
    {
        return $this->gateway->refundPayment($paymentIntentId, $amount);
    }

    public function getPaymentStatus(string $paymentIntentId): string
    {
        return $this->gateway->getPaymentStatus($paymentIntentId);
    }

    public function handleWebhook(string $gateway, array $payload): void
    {
        $gatewayInstance = app($this->getGatewayClass($gateway));
        $gatewayInstance->handleWebhook($payload);
    }

    public function getSupportedGateways(): array
    {
        return [
            'stripe' => 'Stripe',
            'paypal' => 'PayPal',
            'wallet' => 'Wallet Balance',
        ];
    }

    public function getAvailablePaymentMethodsForCoupon(Coupon $coupon, ?int $userId = null): array
    {
        $availableMethods = [];

        foreach ($coupon->availablePaymentMethods as $paymentMethod) {
            $methodData = [
                'id' => $paymentMethod->id,
                'code' => $paymentMethod->code,
                'name' => $paymentMethod->display_name,
                'description' => $paymentMethod->description,
                'sort_order' => $paymentMethod->pivot->sort_order,
            ];

            // Add wallet-specific information
            if ($paymentMethod->isWallet() && $userId) {
                $wallet = Wallet::where('user_id', $userId)->first();
                $methodData['wallet_balance'] = $wallet ? (float) $wallet->balance : 0.00;
                $methodData['has_sufficient_balance'] = $wallet && $wallet->balance >= $coupon->selling_price;
            }

            $availableMethods[] = $methodData;
        }

        // Sort by sort_order
        usort($availableMethods, fn (array $a, array $b): int => $a['sort_order'] <=> $b['sort_order']);

        return $availableMethods;
    }

    private function getGatewayClass(string $gateway): string
    {
        return match ($gateway) {
            'stripe' => StripeGateway::class,
            'paypal' => PayPalGateway::class,
            'wallet' => WalletGateway::class,
            default => throw new InvalidArgumentException("Unsupported payment gateway: {$gateway}")
        };
    }
}
