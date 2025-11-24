<?php

declare(strict_types=1);

namespace App\Exceptions;

use Exception;

final class StockLimitExceededException extends Exception
{
    protected string $message = 'Insufficient stock for this coupon';

    protected int $code = 422;
}
