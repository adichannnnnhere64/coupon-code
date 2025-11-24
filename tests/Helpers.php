<?php

declare(strict_types=1);

namespace Tests\Helpers;

use App\Models\Coupon;
use App\Models\User;
use App\Models\Wallet;

function createCouponWithStock($quantity = 10): Coupon
{
    return Coupon::factory()->create(['stock_quantity' => $quantity]);
}

function createUserWithBalance($balance = 1000.00): User
{
    $user = User::factory()->create();
    Wallet::factory()->forUser($user)->create(['balance' => $balance]);

    return $user;
}
