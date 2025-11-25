<?php

declare(strict_types=1);

namespace App\Exceptions;

use App\Enums\ExceptionCode;
use Exception;

class CoreException extends Exception
{
    protected string $errorMessage;

    protected string $description;

    protected int $statusCode;

    protected ExceptionCode $internalCode;

    public static function new(
        ExceptionCode $code,
        ?string $message = null,
        ?string $description = null,
        ?int $statusCode = null,
    ): static {
        $exception = new static(
            $message ?? $code->getMessage(),
            $statusCode ?? $code->getStatusCode()
        );

        $exception->errorMessage = $message ?? $code->getMessage();
        $exception->description = $description ?? $code->getDescription();
        $exception->statusCode = $statusCode ?? $code->getStatusCode();
        $exception->internalCode = $code;

        return $exception;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getInternalCode(): ExceptionCode
    {
        return $this->internalCode;
    }
}
