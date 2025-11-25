<?php

declare(strict_types=1);

use App\Exceptions\InsufficientBalanceException;
use App\Models\Wallet;
use App\ValueObjects\Money;

it('can create a wallet', function (): void {
    $wallet = Wallet::factory()->create(['balance' => 1000.00]);

    expect((float) $wallet->balance)->toBe(1000.00);
});

it('has user relationship', function (): void {
    $wallet = Wallet::factory()->create(); // Remove withUser() call

    expect($wallet->user)->not->toBeNull();
});

it('can check sufficient balance', function (): void {
    $wallet = Wallet::factory()->create(['balance' => 500.00]);
    $sufficientAmount = new Money(300.00);
    $insufficientAmount = new Money(600.00);

    expect($wallet->hasSufficientBalance($sufficientAmount))->toBeTrue();
    expect($wallet->hasSufficientBalance($insufficientAmount))->toBeFalse();
});

it('can deduct amount', function (): void {
    $wallet = Wallet::factory()->create(['balance' => 500.00]);
    $amount = new Money(200.00);

    $wallet->deductAmount($amount);

    expect((float) $wallet->balance)->toBe(300.00);
});

it('throws exception when deducting insufficient balance', function (): void {
    $wallet = Wallet::factory()->create(['balance' => 100.00]);
    $amount = new Money(200.00);

    $wallet->deductAmount($amount);
})->throws(InsufficientBalanceException::class);

it('can add amount', function (): void {
    $wallet = Wallet::factory()->create(['balance' => 500.00]);
    $amount = new Money(200.00);

    $wallet->addAmount($amount);

    expect((float) $wallet->balance)->toBe(700.00);
});
