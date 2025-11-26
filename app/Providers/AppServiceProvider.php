<?php

declare(strict_types=1);

namespace App\Providers;

use App\Contracts\Services\NotificationServiceInterface;
use App\Contracts\Services\PaymentGatewayInterface;
use App\Services\NotificationService;
use App\Services\PaymentGateways\PayPalGateway;
use App\Services\PaymentGateways\StripeGateway;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\ServiceProvider;
use InvalidArgumentException;

final class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(NotificationServiceInterface::class, NotificationService::class);
        /* $this->app->singleton(MediaService::class, function ($app) { */
        /*     return new MediaService(); */
        /* }); */

        $this->app->bind(function ($app): PaymentGatewayInterface {
            $defaultGateway = config('services.default_payment_gateway', 'stripe');

            return match ($defaultGateway) {
                'stripe' => $app->make(StripeGateway::class),
                'paypal' => $app->make(PayPalGateway::class),
                default => throw new InvalidArgumentException("Unsupported payment gateway: {$defaultGateway}")
            };
        });

        /* $this->app->singleton(PaymentService::class, function ($app) { */
        /*     return new PaymentServic(); */
        /* }); */
    }

    public function boot(): void
    {
        $this->bootModelsDefaults();
    }

    private function bootModelsDefaults(): void
    {
        Model::unguard();
    }
}
