<?php

declare(strict_types=1);

// app/Services/PaymentGateways/StripeGateway.php

namespace App\Services\PaymentGateways;

use App\Contracts\Services\PaymentGatewayInterface;
use App\DTOs\PaymentConfirmationDTO;
use App\DTOs\PaymentIntentDTO;
use Exception;
use Illuminate\Support\Facades\Log;
use Stripe\Exception\ApiErrorException;
use Stripe\StripeClient;

final readonly class StripeGateway implements PaymentGatewayInterface
{
    private StripeClient $stripe;

    public function __construct()
    {
        $this->stripe = new StripeClient(config('services.stripe.secret_key'));
    }

    public function createPaymentIntent(PaymentIntentDTO $paymentIntent): array
    {
        try {
            $intent = $this->stripe->paymentIntents->create([
                'amount' => (int) ($paymentIntent->amount * 100), // Convert to cents
                'currency' => mb_strtolower($paymentIntent->currency),
                'description' => $paymentIntent->description,
                'metadata' => $paymentIntent->metadata,
                'receipt_email' => $paymentIntent->customerEmail,
                'automatic_payment_methods' => [
                    'enabled' => true,
                ],
            ]);

            return [
                'payment_intent_id' => $intent->id,
                'client_secret' => $intent->client_secret,
                'status' => $intent->status,
                'amount' => $intent->amount / 100,
                'currency' => $intent->currency,
            ];

        } catch (ApiErrorException $e) {
            Log::error('Stripe payment intent creation failed: '.$e->getMessage());
            throw new Exception('Failed to create payment intent: '.$e->getMessage(), $e->getCode(), $e);
        }
    }

    public function confirmPayment(string $paymentIntentId): PaymentConfirmationDTO
    {
        try {
            $intent = $this->stripe->paymentIntents->retrieve($paymentIntentId);

            return new PaymentConfirmationDTO(
                paymentIntentId: $intent->id,
                status: $intent->status,
                clientSecret: $intent->client_secret,
                metadata: $intent->metadata->toArray()
            );

        } catch (ApiErrorException $e) {
            Log::error('Stripe payment confirmation failed: '.$e->getMessage());

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
            $params = ['payment_intent' => $paymentIntentId];
            if ($amount) {
                $params['amount'] = (int) ($amount * 100);
            }

            $refund = $this->stripe->refunds->create($params);

            return [
                'refund_id' => $refund->id,
                'status' => $refund->status,
                'amount' => $refund->amount / 100,
            ];

        } catch (ApiErrorException $e) {
            Log::error('Stripe refund failed: '.$e->getMessage());
            throw new Exception('Refund failed: '.$e->getMessage(), $e->getCode(), $e);
        }
    }

    public function getPaymentStatus(string $paymentIntentId): string
    {
        try {
            $intent = $this->stripe->paymentIntents->retrieve($paymentIntentId);

            return $intent->status;
        } catch (ApiErrorException $e) {
            Log::error('Stripe get payment status failed: '.$e->getMessage());

            return 'unknown';
        }
    }

    public function supportsWebhooks(): bool
    {
        return true;
    }

    public function handleWebhook(array $payload): void
    {
        $event = $payload['type'] ?? null;

        switch ($event) {
            case 'payment_intent.succeeded':
                $this->handlePaymentSucceeded($payload);
                break;
            case 'payment_intent.payment_failed':
                $this->handlePaymentFailed($payload);
                break;
            case 'charge.refunded':
                $this->handleRefund($payload);
                break;
        }
    }

    public function getName(): string
    {
        return 'stripe';
    }

    private function handlePaymentSucceeded(array $payload): void
    {
        $paymentIntent = $payload['data']['object'];

        // Update your application logic here
        Log::info('Stripe payment succeeded', ['payment_intent' => $paymentIntent['id']]);
    }

    private function handlePaymentFailed(array $payload): void
    {
        $paymentIntent = $payload['data']['object'];

        // Update your application logic here
        Log::error('Stripe payment failed', [
            'payment_intent' => $paymentIntent['id'],
            'error' => $paymentIntent['last_payment_error'] ?? 'Unknown error',
        ]);
    }

    private function handleRefund(array $payload): void
    {
        $charge = $payload['data']['object'];

        // Update your application logic here
        Log::info('Stripe refund processed', ['charge_id' => $charge['id']]);
    }
}
