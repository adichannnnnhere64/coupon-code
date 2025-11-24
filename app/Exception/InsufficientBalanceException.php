<?php

declare(strict_types=1);

namespace App\Exceptions;

use Exception;

final class InsufficientBalanceException extends Exception
{
    protected string $message = 'Insufficient wallet balance';

    protected int $code = 422;
}
