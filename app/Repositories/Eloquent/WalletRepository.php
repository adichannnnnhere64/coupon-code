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
        return (bool) Wallet::query()->where('user_id', $userId)
            ->update(['balance' => $newBalance]);
    }

    /** @param array<string, mixed> $transactionData */
    public function addTransaction(int $walletId, array $transactionData): WalletTransaction
    {

        /** @var array<string, mixed> $data */
        $data = array_merge($transactionData, [
            'wallet_id' => $walletId,
        ]);

        /** @var WalletTransaction */
        $walletTransaction = WalletTransaction::query()->create($data);

        return $walletTransaction;
    }
}
