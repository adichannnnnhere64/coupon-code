<?php

declare(strict_types=1);

// tests/Feature/CouponPaymentMethodsTest.php
use App\Models\Coupon;
use App\Models\PaymentMethod;
use Illuminate\Support\Facades\DB;

beforeEach(function (): void {
    /* $this->paymentMethods = PaymentMethod::factory()->count(4)->create(); */
});

it('can get available payment methods for coupon', function (): void {

    PaymentMethod::factory()->wallet()->active()->create();
    PaymentMethod::factory()->stripe()->active()->create();

    $coupon = Coupon::factory()
        ->withPaymentMethods(['wallet', 'stripe'])
        ->create();

    $availableMethods = $coupon->availablePaymentMethods;

    expect($availableMethods)->toHaveCount(2);
    expect($availableMethods->pluck('code')->toArray())
        ->toContain('wallet', 'stripe');
});

it('can check if coupon supports payment method', function (): void {

    PaymentMethod::factory()->wallet()->active()->create();
    PaymentMethod::factory()->stripe()->inactive()->create();

    $coupon = Coupon::factory()
        ->withPaymentMethods(['wallet'])
        ->create();

    expect($coupon->supportsPaymentMethod('wallet'))->toBeTrue();
    expect($coupon->supportsPaymentMethod('stripe'))->toBeFalse();
});

it('only returns active payment methods', function (): void {

    DB::table('coupon_payment_method')->delete();
    PaymentMethod::query()->delete();
    $inactiveMethod = PaymentMethod::factory()->stripe()->inactive()->create();
    $coupon = Coupon::factory()->create();

    $coupon->paymentMethods()->attach($inactiveMethod->id);

    expect($coupon->availablePaymentMethods)->toHaveCount(0);
});
