<?php

declare(strict_types=1);

// app/Http/Resources/CouponResource.php

namespace App\Http\Resources;

use App\ValueObjects\Money;
use Illuminate\Http\Resources\Json\JsonResource;

final class CouponResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            /* 'denomination' => (float) $this->denomination, */
            /* 'selling_price' => (float) $this->selling_price, */

            'selling_price' => new Money(
                amount: $this->selling_price,
                currency: $this->operator->country->currency // dynamic!
            )->jsonSerialize(),

            'denomination' => new Money(
                amount: $this->denomination,
                currency: $this->operator->country->currency
            )->jsonSerialize(),

            'coupon_code' => $this->coupon_code,
            'validity_days' => $this->validity_days,
            'stock_quantity' => $this->when($request->user()?->isAdmin(), $this->stock_quantity),
            'is_available' => $this->isAvailable(),
            'is_low_stock' => $this->isLowStock(),
            'operator' => [
                'id' => $this->operator->id,
                'name' => $this->operator->name,
                'code' => $this->operator->code,
                'logo_url' => $this->operator->logo_url,
                'country' => [
                    'id' => $this->operator->country->id,
                    'name' => $this->operator->country->name,
                    'code' => $this->operator->country->code,
                    'currency' => $this->operator->country->currency,
                ],
            ],
            'plan_type' => [
                'id' => $this->planType->id,
                'name' => $this->planType->name,
                'description' => $this->planType->description,
            ],
            'created_at' => $this->created_at->toISOString(),
            'updated_at' => $this->updated_at->toISOString(),
        ];
    }
}
