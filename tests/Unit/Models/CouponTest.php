<?php

declare(strict_types=1);

use App\Exceptions\CouponUnavailableException;
use App\Models\Coupon;
use App\Models\Operator;
use App\Models\PlanType;

it('can create a coupon', function (): void {
    $coupon = Coupon::factory()->create([
        'denomination' => 100.00,
        'selling_price' => 105.00,
    ]);

    expect($coupon->denomination)->toBe(100.00);
    expect($coupon->selling_price)->toBe(105.00);
});

it('has operator relationship', function (): void {
    $coupon = Coupon::factory()->create();

    expect($coupon->operator)->toBeInstanceOf(Operator::class);
});

it('has plan type relationship', function (): void {
    $coupon = Coupon::factory()->create();

    expect($coupon->planType)->toBeInstanceOf(PlanType::class);
});

it('can check availability', function (): void {
    $availableCoupon = Coupon::factory()->inStock()->active()->create();
    $outOfStockCoupon = Coupon::factory()->outOfStock()->active()->create();
    $inactiveCoupon = Coupon::factory()->inStock()->inactive()->create();

    expect($availableCoupon->isAvailable())->toBeTrue();
    expect($outOfStockCoupon->isAvailable())->toBeFalse();
    expect($inactiveCoupon->isAvailable())->toBeFalse();
});

it('can check low stock', function (): void {
    $lowStockCoupon = Coupon::factory()->create([
        'stock_quantity' => 5,
        'low_stock_threshold' => 10,
    ]);
    $adequateStockCoupon = Coupon::factory()->create([
        'stock_quantity' => 15,
        'low_stock_threshold' => 10,
    ]);

    expect($lowStockCoupon->isLowStock())->toBeTrue();
    expect($adequateStockCoupon->isLowStock())->toBeFalse();
});

it('can decrement stock', function (): void {
    $coupon = Coupon::factory()->create(['stock_quantity' => 10]);

    $coupon->decrementStock();

    expect($coupon->fresh()->stock_quantity)->toBe(9);
});

it('throws exception when decrementing zero stock', function (): void {
    $coupon = Coupon::factory()->create(['stock_quantity' => 0]);

    $coupon->decrementStock();
})->throws(CouponUnavailableException::class, 'Coupon out of stock');
