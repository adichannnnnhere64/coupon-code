<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Models\Coupon;

interface CouponRepositoryInterface
{
    public function findAvailableCoupons(int $operatorId, int $planTypeId);

    public function findAvailableById(int $id): ?Coupon;

    public function decrementStock(int $couponId): int;

    public function getLowStockCoupons();
}
