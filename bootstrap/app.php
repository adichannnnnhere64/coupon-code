<?php

declare(strict_types=1);

use App\Exceptions\CustomException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
    })
    ->withExceptions(function (Exceptions $exceptions): void {

        $exceptions->renderable(function (CustomException $e) {
            $internalCode = $e->getInternalCode();

            return response()->json([
                'status' => 'error',
                'code' => $internalCode->value,
                'message' => $e->getMessage(),
                'description' => $e->getDescription(),
                'details' => $internalCode->getLink(),
            ], $e->getCode());
        });
    })->create();
