<?php

declare(strict_types=1);

use App\Models\Coupon;
use App\Models\Wallet;

beforeEach(function (): void {
    $this->user = $this->signIn();
    $this->wallet = Wallet::factory()->forUser($this->user)->create(['balance' => 1000.00]);
});

test('user can purchase coupon', function (): void {
    $coupon = Coupon::factory()->inStock()->active()->create(['selling_price' => 100.00]);

    $response = $this->postJson('/api/coupons/purchase', [
        'coupon_id' => $coupon->id,
        'delivery_methods' => ['sms', 'email'],
    ]);

    $response->assertStatus(200)
        ->assertJsonStructure([
            'data' => [
                'id',
                'transaction_id',
                'amount',
                'status',
                'coupon_code',
                'operator',
            ],
        ]);

    $this->assertDatabaseHas('coupon_transactions', [
        'user_id' => $this->user->id,
        'coupon_id' => $coupon->id,
        'status' => 'success',
    ]);

    expect($this->wallet->fresh()->balance)->toBe(900.00);
    expect($coupon->fresh()->stock_quantity)->toBe($coupon->stock_quantity - 1);
});

test('user cannot purchase out of stock coupon', function (): void {
    $coupon = Coupon::factory()->outOfStock()->active()->create();

    $response = $this->postJson('/api/coupons/purchase', [
        'coupon_id' => $coupon->id,
        'delivery_methods' => ['sms'],
    ]);

    $response->assertStatus(422);
});

test('user cannot purchase with insufficient balance', function (): void {
    $this->wallet->update(['balance' => 50.00]);
    $coupon = Coupon::factory()->inStock()->active()->create(['selling_price' => 100.00]);

    $response = $this->postJson('/api/coupons/purchase', [
        'coupon_id' => $coupon->id,
        'delivery_methods' => ['sms'],
    ]);

    $response->assertStatus(422);
});
