<?php

declare(strict_types=1);

namespace App\ValueObjects;

use InvalidArgumentException;

final readonly class PhoneNumber
{
    public function __construct(private string $number)
    {
        throw_unless(preg_match('/^\+?[1-9]\d{1,14}$/', $number), new InvalidArgumentException('Invalid phone number format'));
    }

    public function toString(): string
    {
        return $this->number;
    }

    public function getCountryCode(): string
    {
        // Extract country code logic
        return mb_substr($this->number, 0, 3);
    }
}
