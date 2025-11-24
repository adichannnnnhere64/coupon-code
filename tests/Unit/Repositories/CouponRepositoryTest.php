<?php

declare(strict_types=1);

use App\Models\Coupon;
use App\Models\Operator;
use App\Models\PlanType;
use App\Repositories\Eloquent\CouponRepository;

beforeEach(function (): void {
    $this->repository = new CouponRepository();
});

it('can find available coupons by operator and plan type', function (): void {
    $operator = Operator::factory()->create();
    $planType = PlanType::factory()->create();

    Coupon::factory()->count(3)->forOperator($operator)->forPlanType($planType)->inStock()->active()->create();
    Coupon::factory()->count(2)->forOperator($operator)->forPlanType($planType)->outOfStock()->active()->create();

    $availableCoupons = $this->repository->findAvailableCoupons($operator->id, $planType->id);

    expect($availableCoupons)->toHaveCount(3);
});

it('can find available coupon by id', function (): void {
    $availableCoupon = Coupon::factory()->inStock()->active()->create();
    $unavailableCoupon = Coupon::factory()->outOfStock()->active()->create();

    $foundCoupon = $this->repository->findAvailableById($availableCoupon->id);
    $notFoundCoupon = $this->repository->findAvailableById($unavailableCoupon->id);

    expect($foundCoupon->id)->toBe($availableCoupon->id);
    expect($notFoundCoupon)->toBeNull();
});

it('can decrement stock', function (): void {
    $coupon = Coupon::factory()->create(['stock_quantity' => 10]);

    $result = $this->repository->decrementStock($coupon->id);

    expect($result)->toBeTrue();
    expect($coupon->fresh()->stock_quantity)->toBe(9);
});

it('can get low stock coupons', function (): void {
    Coupon::factory()->count(3)->create([
        'stock_quantity' => 5,
        'low_stock_threshold' => 10,
        'is_active' => true,
    ]);
    Coupon::factory()->count(2)->create([
        'stock_quantity' => 15,
        'low_stock_threshold' => 10,
        'is_active' => true,
    ]);

    $lowStockCoupons = $this->repository->getLowStockCoupons();

    expect($lowStockCoupons)->toHaveCount(3);
});
