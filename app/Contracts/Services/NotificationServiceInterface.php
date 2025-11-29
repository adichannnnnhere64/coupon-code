<?php

declare(strict_types=1);

namespace App\Contracts\Services;

use App\Models\Coupon;
use App\Models\CouponTransaction;
use App\ValueObjects\Money;

interface NotificationServiceInterface
{
    public function sendCouponDelivery(CouponTransaction $transaction): void;
    /* public function sendCouponDelivery(CouponTransaction $transaction, array $deliveryMethods): void; */

    public function sendWalletCreditNotification(int $userId, Money $amount): void;

    public function sendLowStockAlert(Coupon $coupon): void;
}
