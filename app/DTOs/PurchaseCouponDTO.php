<?php

declare(strict_types=1);

namespace App\DTOs;

final readonly class PurchaseCouponDTO
{
    public function __construct(
        public int $userId,
        public int $couponId,
        public array $deliveryMethods
    ) {}

    public static function fromRequest(array $data): self
    {
        return new self(
            userId: auth()->id(),
            couponId: $data['coupon_id'],
            deliveryMethods: $data['delivery_methods']
        );
    }
}
