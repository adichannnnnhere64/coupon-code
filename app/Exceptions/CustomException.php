<?php

declare(strict_types=1);

namespace App\Exceptions;

use App\Enums\ExceptionCode;

final class CustomException extends CoreException
{
    public static function couponUnavailable(): self
    {
        return self::new(
            ExceptionCode::COUPON_UNAVAILABLE,
        );

    }

    public static function insufficientBalance(): self
    {
        return self::new(
            ExceptionCode::INSUFFICIENT_BALANCE,
        );
    }

    public static function stockLimitExceeded(): self
    {
        return self::new(
            ExceptionCode::STOCK_LIMIT_EXCEEDED,
        );
    }

    public static function invalidImage(): self
    {
        return self::new(
            ExceptionCode::INVALID_IMAGE,
        );
    }
}
