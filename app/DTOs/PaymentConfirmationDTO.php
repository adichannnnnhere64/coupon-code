<?php

declare(strict_types=1);

// app/DTOs/PaymentConfirmationDTO.php

namespace App\DTOs;

final readonly class PaymentConfirmationDTO
{
    public function __construct(
        public string $paymentIntentId,
        public string $status,
        public ?string $clientSecret = null,
        public ?string $redirectUrl = null,
        public ?array $metadata = null,
        public ?string $errorMessage = null
    ) {}
}
