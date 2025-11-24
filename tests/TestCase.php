<?php

declare(strict_types=1);

namespace Tests;

use App\Models\Coupon;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Additional setup
        $this->withoutVite();
        $this->withoutExceptionHandling();
    }

    protected function createUser(array $attributes = []): User
    {
        return User::factory()->create($attributes);
    }

    protected function createAdminUser(): User
    {
        return $this->createUser([
            'email' => 'admin@recharge.com',
            'status' => 'active',
        ]);
    }

    protected function createWalletForUser($user, array $attributes = []): Wallet
    {
        return Wallet::factory()->forUser($user)->create($attributes);
    }

    protected function createCoupon(array $attributes = []): Coupon
    {
        return Coupon::factory()->create($attributes);
    }

    protected function signIn($user = null)
    {
        $user = $user ?: $this->createUser();
        $this->actingAs($user);

        return $user;
    }
}
