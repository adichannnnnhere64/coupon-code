<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

final class CouponResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'denomination' => $this->denomination,
            'selling_price' => $this->selling_price,
            'operator' => $this->operator->name,
            'plan_type' => $this->planType->name,
            'validity_days' => $this->validity_days,
            'is_available' => $this->isAvailable(),
            'stock_quantity' => $this->when($request->user()->isAdmin(), $this->stock_quantity),
            'is_low_stock' => $this->isLowStock(),
        ];
    }
}
