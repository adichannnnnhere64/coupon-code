<?php

declare(strict_types=1);

namespace App\Exceptions;

use Exception;

final class CouponUnavailableException extends Exception
{
    protected string $message = 'Coupon is not available';

    protected int $code = 422;
}
