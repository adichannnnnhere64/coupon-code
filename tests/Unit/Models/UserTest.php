<?php

declare(strict_types=1);

use App\Models\CouponTransaction;
use App\Models\User;
use App\Models\Wallet;

test('to array', function (): void {
    $user = User::factory()->create()->refresh();

    expect(array_keys($user->toArray()))
        ->toBe([
            'id',
            'name',
            'email',
            'email_verified_at',
            'phone',
            'status',
            'phone_verified_at',
            'created_at',
            'updated_at',
        ]);
});

it('can create a user', function (): void {
    $user = User::factory()->create([
        'name' => 'John Doe',
        'email' => 'john@example.com',
    ]);

    expect($user->name)->toBe('John Doe');
    expect($user->email)->toBe('john@example.com');
});

it('has wallet relationship', function (): void {
    $user = User::factory()->hasWallet()->create();

    expect($user->wallet)->toBeInstanceOf(Wallet::class);
});

it('has coupon transactions relationship', function (): void {
    $user = User::factory()->has(CouponTransaction::factory()->count(3))->create();

    expect($user->couponTransactions)->toHaveCount(3);
});

it('can check if user is active', function (): void {
    $activeUser = User::factory()->active()->create();
    $pendingUser = User::factory()->pending()->create();

    expect($activeUser->isActive())->toBeTrue();
    expect($pendingUser->isActive())->toBeFalse();
});

it('can verify email and phone', function (): void {
    $user = User::factory()->create([
        'email_verified_at' => null,
        'phone_verified_at' => null,
    ]);

    expect($user->email_verified_at)->toBeNull();
    expect($user->phone_verified_at)->toBeNull();

    $user->markEmailAsVerified();
    $user->markPhoneAsVerified();

    expect($user->email_verified_at)->not->toBeNull();
    expect($user->phone_verified_at)->not->toBeNull();
});
