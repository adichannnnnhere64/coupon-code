<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Models\Wallet;
use App\Models\WalletTransaction;

interface WalletRepositoryInterface
{
    public function findByUserId(int $userId): ?Wallet;

    public function createForUser(int $userId): Wallet;

    public function updateBalance(int $userId, float $newBalance): bool;

    public function addTransaction(int $walletId, array $transactionData): WalletTransaction;
}
