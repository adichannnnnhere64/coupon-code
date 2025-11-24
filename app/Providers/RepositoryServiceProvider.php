<?php

declare(strict_types=1);

namespace App\Providers;

use App\Repositories\Contracts\CouponRepositoryInterface;
use App\Repositories\Eloquent\CouponRepository;
use Illuminate\Support\ServiceProvider;

final class RepositoryServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(CouponRepositoryInterface::class, CouponRepository::class);
        $this->app->bind(WalletRepositoryInterface::class, WalletRepository::class);
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
    }
}
