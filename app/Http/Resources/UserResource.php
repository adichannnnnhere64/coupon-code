<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

final class UserResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'status' => $this->status,
            'wallet_balance' => $this->whenLoaded('wallet', $this->wallet->balance ?? 0),
            'email_verified' => ! is_null($this->email_verified_at),
            'phone_verified' => ! is_null($this->phone_verified_at),
            'created_at' => $this->created_at->toISOString(),
        ];
    }
}
