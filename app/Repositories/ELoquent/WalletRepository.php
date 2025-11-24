<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Models\Wallet;
use App\Models\WalletTransaction;
use App\Repositories\Contracts\WalletRepositoryInterface;

final class WalletRepository implements WalletRepositoryInterface
{
    public function findByUserId(int $userId): ?Wallet
    {
        return Wallet::query()->where('user_id', $userId)->first();
    }

    public function createForUser(int $userId): Wallet
    {
        return Wallet::query()->create([
            'user_id' => $userId,
            'balance' => 0.00,
        ]);
    }

    public function updateBalance(int $userId, float $newBalance): bool
    {
        return Wallet::query()->where('user_id', $userId)
            ->update(['balance' => $newBalance]);
    }

    public function addTransaction(int $walletId, array $transactionData): WalletTransaction
    {
        return WalletTransaction::query()->create(array_merge($transactionData, [
            'wallet_id' => $walletId,
        ]));
    }
}
