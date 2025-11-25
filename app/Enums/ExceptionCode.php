<?php

declare(strict_types=1);

namespace App\Enums;

enum ExceptionCode: int
{
    case COUPON_UNAVAILABLE = 10_000;
    case INSUFFICIENT_BALANCE = 10_001;
    case STOCK_LIMIT_EXCEEDED = 10_002;

    public function getStatusCode(): int
    {
        $value = $this->value;

        return match (true) {
            $value >= 10_000 => 422,
            default => 500,
        };
    }

    public function getDescription(): string
    {
        return __('exceptions.'.$this->value.'.description');
    }

    public function getLink(): string
    {
        return '';

    }

    public function getMessage(): string
    {
        return __('exceptions.'.$this->value.'.message');
    }
}
