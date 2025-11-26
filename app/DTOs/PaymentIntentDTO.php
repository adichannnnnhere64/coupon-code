<?php

declare(strict_types=1);

// app/DTOs/PaymentIntentDTO.php

namespace App\DTOs;

final readonly class PaymentIntentDTO
{
    public function __construct(
        public float $amount,
        public string $currency,
        public string $description,
        public array $metadata = [],
        public ?string $customerEmail = null,
        public ?string $returnUrl = null,
        public ?string $cancelUrl = null
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            amount: $data['amount'],
            currency: $data['currency'] ?? 'USD',
            description: $data['description'],
            metadata: $data['metadata'] ?? [],
            customerEmail: $data['customer_email'] ?? null,
            returnUrl: $data['return_url'] ?? null,
            cancelUrl: $data['cancel_url'] ?? null
        );
    }
}
