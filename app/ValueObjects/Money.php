<?php

declare(strict_types=1);

namespace App\ValueObjects;

use InvalidArgumentException;
use NumberFormatter;

final readonly class Money
{
    public function __construct(
        private float|string $amount,
        private string $currency = 'INR'
    ) {
        throw_if($amount < 0, InvalidArgumentException::class, 'Money amount cannot be negative');
    }

    public function getAmount(): float|string
    {
        return $this->amount;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }

    public function add(self $other): self
    {
        throw_if($this->currency !== $other->currency, InvalidArgumentException::class, 'Currencies must match');

        return new self($this->amount + $other->amount, $this->currency);
    }

    public function subtract(self $other): self
    {
        throw_if($this->currency !== $other->currency, InvalidArgumentException::class, 'Currencies must match');

        return new self($this->amount - $other->amount, $this->currency);
    }

    public function equals(self $other): bool
    {
        return $this->amount === $other->amount && $this->currency === $other->currency;
    }

    public function format(): string
    {
        $fmt = new NumberFormatter('en_US', NumberFormatter::CURRENCY);

        return $fmt->formatCurrency((float) $this->amount, $this->currency);
    }

    public function jsonSerialize(): array
    {
        return [
            'amount' => $this->amount,
            'currency' => $this->currency,
            'formatted' => $this->format(),
        ];
    }
}
