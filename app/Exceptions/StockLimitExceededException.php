<?php

declare(strict_types=1);

namespace App\Exceptions;

use Exception;

final class StockLimitExceededException extends Exception
{
    public function __construct()
    {
        parent::__construct('Insufficient stock for this coupon', 422);
    }
}
