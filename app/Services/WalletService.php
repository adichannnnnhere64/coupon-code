<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Wallet;
use App\Repositories\Contracts\WalletRepositoryInterface;
use App\ValueObjects\Money;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

final readonly class WalletService
{
    public function __construct(
        private WalletRepositoryInterface $walletRepository,
        private NotificationService $notificationService
    ) {}

    public function getWalletBalance(int $userId): float
    {
        $wallet = $this->walletRepository->findByUserId($userId);

        return $wallet instanceof Wallet ? $wallet->balance : 0.00;
    }

    public function addBalance(int $userId, Money $amount, string $description = ''): WalletTransaction
    {
        return DB::transaction(function () use ($userId, $amount, $description): \App\Models\WalletTransaction {
            $wallet = $this->walletRepository->findByUserId($userId);

            if (! $wallet instanceof Wallet) {
                $wallet = $this->walletRepository->createForUser($userId);
            }

            $oldBalance = $wallet->balance;
            $newBalance = $oldBalance + $amount->getAmount();

            $this->walletRepository->updateBalance($userId, $newBalance);

            $transaction = $this->walletRepository->addTransaction($wallet->id, [
                'type' => 'credit',
                'amount' => $amount->getAmount(),
                'balance_after' => $newBalance,
                'description' => $description !== '' && $description !== '0' ? $description : 'Wallet top-up',
                'reference_id' => 'REF_'.Str::random(12),
            ]);

            // Notify user
            $this->notificationService->sendWalletCreditNotification($userId, $amount);

            return $transaction;
        });
    }

    public function getTransactionHistory(int $userId, array $filters = [])
    {
        $wallet = $this->walletRepository->findByUserId($userId);

        if (! $wallet instanceof Wallet) {
            return collect();
        }

        $query = $wallet->transactions();

        if (isset($filters['type'])) {
            $query->where('type', $filters['type']);
        }

        if (isset($filters['start_date']) && isset($filters['end_date'])) {
            $query->whereBetween('created_at', [$filters['start_date'], $filters['end_date']]);
        }

        return $query->orderBy('created_at', 'desc')->get();
    }
}
