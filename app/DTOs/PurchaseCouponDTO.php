<?php

declare(strict_types=1);

namespace App\DTOs;

// app/DTOs/PurchaseCouponDTO.php
// Add payment method to the DTO
final readonly class PurchaseCouponDTO
{
    public function __construct(
        public int $userId,
        public int $couponId,
        public array $deliveryMethods,
        public string $paymentMethod // wallet, stripe, paypal
    ) {}

    public static function fromRequest(array $data): self
    {
        return new self(
            userId: auth()->id(),
            couponId: $data['coupon_id'],
            deliveryMethods: $data['delivery_methods'],
            paymentMethod: $data['payment_method']
        );
    }
}
