<?php

declare(strict_types=1);

// app/Services/PaymentGateways/PayPalGateway.php

namespace App\Services\PaymentGateways;

use App\Contracts\Services\PaymentGatewayInterface;
use App\DTOs\PaymentConfirmationDTO;
use App\DTOs\PaymentIntentDTO;
use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

final class PayPalGateway implements PaymentGatewayInterface
{
    private readonly string $clientId;

    private readonly string $clientSecret;

    private readonly string $baseUrl;

    private ?string $accessToken = null;

    public function __construct()
    {
        $this->clientId = config('services.paypal.client_id');
        $this->clientSecret = config('services.paypal.client_secret');
        $this->baseUrl = config('services.paypal.sandbox') ?
            'https://api-m.sandbox.paypal.com' : 'https://api-m.paypal.com';

        $this->authenticate();
    }

    public function createPaymentIntent(PaymentIntentDTO $paymentIntent): array
    {
        try {
            $response = Http::withToken($this->accessToken)
                ->post("{$this->baseUrl}/v2/checkout/orders", [
                    'intent' => 'CAPTURE',
                    'purchase_units' => [
                        [
                            'amount' => [
                                'currency_code' => mb_strtoupper($paymentIntent->currency),
                                'value' => number_format($paymentIntent->amount, 2, '.', ''),
                            ],
                            'description' => $paymentIntent->description,
                        ],
                    ],
                    'payment_source' => [
                        'paypal' => [
                            'experience_context' => [
                                'payment_method_preference' => 'IMMEDIATE_PAYMENT_REQUIRED',
                                'brand_name' => config('app.name'),
                                'locale' => 'en-US',
                                'landing_page' => 'LOGIN',
                                'user_action' => 'PAY_NOW',
                                'return_url' => $paymentIntent->returnUrl ?? route('payment.success'),
                                'cancel_url' => $paymentIntent->cancelUrl ?? route('payment.cancel'),
                            ],
                        ],
                    ],
                ]);

            if ($response->successful()) {
                $order = $response->json();

                return [
                    'payment_intent_id' => $order['id'],
                    'status' => $order['status'],
                    'approve_url' => collect($order['links'])->firstWhere('rel', 'approve')['href'],
                    'amount' => $paymentIntent->amount,
                    'currency' => $paymentIntent->currency,
                ];
            }
            throw new Exception('PayPal order creation failed: '.$response->body());
        } catch (Exception $e) {
            Log::error('PayPal payment intent creation failed: '.$e->getMessage());
            throw new Exception('Failed to create payment intent: '.$e->getMessage(), $e->getCode(), $e);
        }
    }

    public function confirmPayment(string $paymentIntentId): PaymentConfirmationDTO
    {
        try {
            // Capture the payment
            $response = Http::withToken($this->accessToken)
                ->post("{$this->baseUrl}/v2/checkout/orders/{$paymentIntentId}/capture");

            if ($response->successful()) {
                $capture = $response->json();

                return new PaymentConfirmationDTO(
                    paymentIntentId: $paymentIntentId,
                    status: $capture['status'],
                    metadata: $capture
                );
            }

            return new PaymentConfirmationDTO(
                paymentIntentId: $paymentIntentId,
                status: 'failed',
                errorMessage: $response->body()
            );

        } catch (Exception $e) {
            Log::error('PayPal payment confirmation failed: '.$e->getMessage());

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
            // First, get the capture ID from the order
            $orderResponse = Http::withToken($this->accessToken)
                ->get("{$this->baseUrl}/v2/checkout/orders/{$paymentIntentId}");

            throw_unless($orderResponse->successful(), Exception::class, 'Failed to retrieve order details');

            $order = $orderResponse->json();
            $captureId = $order['purchase_units'][0]['payments']['captures'][0]['id'] ?? null;

            throw_unless($captureId, Exception::class, 'No capture found for refund');

            $refundData = [];
            if ($amount) {
                $refundData['amount'] = [
                    'value' => number_format($amount, 2, '.', ''),
                    'currency_code' => $order['purchase_units'][0]['amount']['currency_code'],
                ];
            }

            $response = Http::withToken($this->accessToken)
                ->post("{$this->baseUrl}/v2/payments/captures/{$captureId}/refund", $refundData);

            if ($response->successful()) {
                $refund = $response->json();

                return [
                    'refund_id' => $refund['id'],
                    'status' => $refund['status'],
                    'amount' => $refund['amount']['value'],
                ];
            }
            throw new Exception('Refund failed: '.$response->body());
        } catch (Exception $e) {
            Log::error('PayPal refund failed: '.$e->getMessage());
            throw new Exception('Refund failed: '.$e->getMessage(), $e->getCode(), $e);
        }
    }

    public function getPaymentStatus(string $paymentIntentId): string
    {
        try {
            $response = Http::withToken($this->accessToken)
                ->get("{$this->baseUrl}/v2/checkout/orders/{$paymentIntentId}");

            if ($response->successful()) {
                $order = $response->json();

                return $order['status'];
            }

            return 'unknown';
        } catch (Exception $e) {
            Log::error('PayPal get payment status failed: '.$e->getMessage());

            return 'unknown';
        }
    }

    public function supportsWebhooks(): bool
    {
        return true;
    }

    public function handleWebhook(array $payload): void
    {
        $eventType = $payload['event_type'] ?? null;

        switch ($eventType) {
            case 'PAYMENT.CAPTURE.COMPLETED':
                $this->handlePaymentCompleted($payload);
                break;
            case 'PAYMENT.CAPTURE.DENIED':
                $this->handlePaymentDenied($payload);
                break;
            case 'PAYMENT.CAPTURE.REFUNDED':
                $this->handleRefundCompleted($payload);
                break;
        }
    }

    public function getName(): string
    {
        return 'paypal';
    }

    private function authenticate(): void
    {
        try {
            $response = Http::withBasicAuth($this->clientId, $this->clientSecret)
                ->asForm()
                ->post("{$this->baseUrl}/v1/oauth2/token", [
                    'grant_type' => 'client_credentials',
                ]);

            if ($response->successful()) {
                $this->accessToken = $response->json('access_token');
            } else {
                throw new Exception('PayPal authentication failed');
            }
        } catch (Exception $e) {
            Log::error('PayPal authentication failed: '.$e->getMessage());
            throw new Exception('PayPal authentication failed', $e->getCode(), $e);
        }
    }

    private function handlePaymentCompleted(array $payload): void
    {
        $resource = $payload['resource'];
        Log::info('PayPal payment completed', ['capture_id' => $resource['id']]);
    }

    private function handlePaymentDenied(array $payload): void
    {
        $resource = $payload['resource'];
        Log::error('PayPal payment denied', [
            'capture_id' => $resource['id'],
            'reason' => $resource['status_details']['reason'] ?? 'Unknown reason',
        ]);
    }

    private function handleRefundCompleted(array $payload): void
    {
        $resource = $payload['resource'];
        Log::info('PayPal refund completed', ['refund_id' => $resource['id']]);
    }
}
