<?php

declare(strict_types=1);

namespace App\Exceptions;

use Exception;

final class CouponUnavailableException extends Exception
{
    public function __construct()
    {
        parent::__construct('Coupon out of stock', 422);
    }
}
