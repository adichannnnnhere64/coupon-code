<?php

declare(strict_types=1);

// app/Contracts/PaymentGatewayInterface.php

namespace App\Contracts\Services;

use App\DTOs\PaymentConfirmationDTO;
use App\DTOs\PaymentIntentDTO;

interface PaymentGatewayInterface
{
    public function createPaymentIntent(PaymentIntentDTO $paymentIntent): array;

    public function confirmPayment(string $paymentIntentId): PaymentConfirmationDTO;

    public function refundPayment(string $paymentIntentId, ?float $amount = null): array;

    public function getPaymentStatus(string $paymentIntentId): string;

    public function supportsWebhooks(): bool;

    public function handleWebhook(array $payload): void;

    public function getName(): string;
}
