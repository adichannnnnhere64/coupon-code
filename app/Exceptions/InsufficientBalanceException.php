<?php

declare(strict_types=1);

namespace App\Exceptions;

use Exception;

final class InsufficientBalanceException extends Exception
{
    public function __construct()
    {
        parent::__construct('Insufficient wallet balance', 422);
    }
}
