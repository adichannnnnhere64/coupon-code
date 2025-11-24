<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

final class CouponTransactionResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'transaction_id' => $this->transaction_id,
            'amount' => $this->amount,
            'status' => $this->status,
            'coupon_code' => $this->coupon->coupon_code,
            'operator' => $this->coupon->operator->name,
            'denomination' => $this->coupon->denomination,
            'delivery_methods' => $this->delivery_methods,
            'purchased_at' => $this->created_at->toISOString(),
            'delivered_at' => $this->coupon_delivered_at?->toISOString(),
        ];
    }
}
