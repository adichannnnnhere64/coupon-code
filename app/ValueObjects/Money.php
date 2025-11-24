<?php

declare(strict_types=1);

namespace App\ValueObjects;

use InvalidArgumentException;

final readonly class Money
{
    public function __construct(
        private float $amount,
        private string $currency = 'INR'
    ) {
        throw_if($amount < 0, new InvalidArgumentException('Money amount cannot be negative'));
    }

    public function getAmount(): float
    {
        return $this->amount;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }

    public function add(self $other): self
    {
        throw_if($this->currency !== $other->currency, new InvalidArgumentException('Currencies must match'));

        return new self($this->amount + $other->amount, $this->currency);
    }

    public function subtract(self $other): self
    {
        throw_if($this->currency !== $other->currency, new InvalidArgumentException('Currencies must match'));

        return new self($this->amount - $other->amount, $this->currency);
    }

    public function equals(self $other): bool
    {
        return $this->amount === $other->amount && $this->currency === $other->currency;
    }
}
