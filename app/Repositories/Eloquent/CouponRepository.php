<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Models\Coupon;
use App\Repositories\Contracts\CouponRepositoryInterface;
use Illuminate\Support\Collection;

final class CouponRepository implements CouponRepositoryInterface
{
    /**
     * @return Collection <int, Coupon>
     * */
    public function findAvailableCoupons(int $operatorId, int $planTypeId): Collection
    {
        return Coupon::query()->where('operator_id', $operatorId)
            ->where('plan_type_id', $planTypeId)
            ->where('is_active', true)
            ->where('stock_quantity', '>', 0)
            ->get();
    }

    public function findAvailableById(int $id): ?Coupon
    {
        return Coupon::query()->where('id', $id)
            ->where('is_active', true)
            ->where('stock_quantity', '>', 0)
            ->first();
    }

    public function decrementStock(int $couponId): int
    {
        return Coupon::query()->where('id', $couponId)
            ->where('stock_quantity', '>', 0)
            ->decrement('stock_quantity');
    }

    /**
     * @return Collection<int, Coupon>
     * */
    public function getLowStockCoupons(): Collection
    {
        return Coupon::query()->whereRaw('stock_quantity <= low_stock_threshold')
            ->where('is_active', true)
            ->get();
    }
}
