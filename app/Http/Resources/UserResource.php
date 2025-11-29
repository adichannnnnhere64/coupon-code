<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Repositories\Contracts\WalletRepositoryInterface;
use Illuminate\Http\Resources\Json\JsonResource;

final class UserResource extends JsonResource
{
    public function toArray($request)
    {
        // Get or create wallet
        $wallet = $this->resource->wallet;

        if (! $wallet) {
            $walletRepo = app(WalletRepositoryInterface::class);
            $wallet = $walletRepo->createForUser($this->resource->id);
        }

        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'status' => $this->status,
            'wallet_balance' => $wallet->balance ?? 0,
            'email_verified' => ! is_null($this->email_verified_at),
            'phone_verified' => ! is_null($this->phone_verified_at),
            'created_at' => $this->created_at?->toISOString(),
            'wallet' => [
                'balance' => $wallet->balance ?? 0,
                'formatted' => '$'.number_format((float) ($wallet->balance ?? 0), 2),
                'currency' => 'USD',
            ],
        ];
    }
}
