<?php

declare(strict_types=1);

// tests/Unit/Models/PaymentMethodTest.php
use App\Models\Coupon;
use App\Models\PaymentMethod;

it('can create payment method', function (): void {
    $paymentMethod = PaymentMethod::factory()->wallet()->create();

    expect($paymentMethod->code)->toBe('wallet');
    expect($paymentMethod->is_active)->toBeTrue();
});

it('can create coupon with payment methods', function (): void {

    PaymentMethod::factory()->wallet()->create();
    PaymentMethod::factory()->stripe()->create();

    $coupon = Coupon::factory()
        ->withPaymentMethods(['wallet', 'stripe'])
        ->create();

    expect($coupon->paymentMethods)->toHaveCount(2);
    expect($coupon->paymentMethods->pluck('code')->toArray())
        ->toContain('wallet', 'stripe');
});

it('can check if payment method is wallet', function (): void {
    $walletMethod = PaymentMethod::factory()->wallet()->create();
    $stripeMethod = PaymentMethod::factory()->stripe()->create();

    expect($walletMethod->isWallet())->toBeTrue();
    expect($stripeMethod->isWallet())->toBeFalse();
});

it('can check if payment method is gateway', function (): void {
    $walletMethod = PaymentMethod::factory()->wallet()->create();
    $stripeMethod = PaymentMethod::factory()->stripe()->create();
    $paypalMethod = PaymentMethod::factory()->paypal()->create();

    expect($walletMethod->isGateway())->toBeFalse();
    expect($stripeMethod->isGateway())->toBeTrue();
    expect($paypalMethod->isGateway())->toBeTrue();
});
