<?php

declare(strict_types=1);

namespace App\Services;

use App\DTOs\PurchaseCouponDTO;
use App\Exceptions\CouponUnavailableException;
use App\Exceptions\InsufficientBalanceException;
use App\Models\CouponTransaction;
use App\Repositories\Contracts\CouponRepositoryInterface;
use App\Repositories\Contracts\WalletRepositoryInterface;
use App\ValueObjects\Money;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

final readonly class CouponService
{
    public function __construct(
        private CouponRepositoryInterface $couponRepository,
        private WalletRepositoryInterface $walletRepository,
        private NotificationService $notificationService
    ) {}

    public function purchaseCoupon(PurchaseCouponDTO $dto): CouponTransaction
    {
        // Find and validate coupon
        $coupon = $this->couponRepository->findAvailableById($dto->couponId);
        throw_unless($coupon, new CouponUnavailableException('Coupon not available'));

        // Check wallet balance
        $wallet = $this->walletRepository->findByUserId($dto->userId);
        $amount = new Money($coupon->selling_price);

        throw_unless($wallet->hasSufficientBalance($amount), new InsufficientBalanceException());

        // Process transaction within database transaction
        return DB::transaction(function () use ($wallet, $coupon, $dto, $amount) {
            // Deduct from wallet
            $wallet->deductAmount($amount);

            // Decrement coupon stock
            $this->couponRepository->decrementStock($coupon->id);

            // Create transaction record
            $transaction = CouponTransaction::query()->create([
                'user_id' => $dto->userId,
                'coupon_id' => $coupon->id,
                'transaction_id' => 'TXN-'.Str::random(12),
                'amount' => $coupon->selling_price,
                'delivery_methods' => json_encode($dto->deliveryMethods),
                'status' => 'success',
                'coupon_delivered_at' => now(),
            ]);

            // Send notifications
            $this->notificationService->sendCouponDelivery($transaction, $dto->deliveryMethods);

            return $transaction;
        });
    }

    public function getAvailableCoupons(int $operatorId, int $planTypeId)
    {
        return $this->couponRepository->findAvailableCoupons($operatorId, $planTypeId);
    }
}
