<?php

declare(strict_types=1);

use App\ValueObjects\Money;

it('can create money object', function (): void {
    $money = new Money(100.50, 'INR');

    expect($money->getAmount())->toBe(100.50);
    expect($money->getCurrency())->toBe('INR');
});

it('throws exception for negative amount', function (): void {
    new Money(-100.00);
})->throws(InvalidArgumentException::class);

it('can add money', function (): void {
    $money1 = new Money(100.00);
    $money2 = new Money(50.00);

    $result = $money1->add($money2);

    expect($result->getAmount())->toBe(150.00);
});

it('can subtract money', function (): void {
    $money1 = new Money(100.00);
    $money2 = new Money(30.00);

    $result = $money1->subtract($money2);

    expect($result->getAmount())->toBe(70.00);
});

it('throws exception when currencies dont match', function (): void {
    $money1 = new Money(100.00, 'INR');
    $money2 = new Money(50.00, 'USD');

    $money1->add($money2);
})->throws(InvalidArgumentException::class);

it('can check equality', function (): void {
    $money1 = new Money(100.00, 'INR');
    $money2 = new Money(100.00, 'INR');
    $money3 = new Money(100.00, 'USD');
    $money4 = new Money(50.00, 'INR');

    expect($money1->equals($money2))->toBeTrue();
    expect($money1->equals($money3))->toBeFalse();
    expect($money1->equals($money4))->toBeFalse();
});
