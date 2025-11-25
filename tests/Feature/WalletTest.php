<?php

declare(strict_types=1);

use App\Models\Wallet;
use App\Models\WalletTransaction;

beforeEach(function (): void {
    $this->user = $this->signIn();
    $this->wallet = Wallet::factory()->forUser($this->user)->create(['balance' => 500.00]);
});

test('user can check wallet balance', function (): void {
    $response = $this->getJson('/api/wallet/balance');

    $response->assertStatus(200)
        ->assertJson([
            'balance' => 500.00,
            'currency' => 'INR',
        ]);
});

test('user can add money to wallet', function (): void {
    $response = $this->postJson('/api/wallet/add-balance', [
        'amount' => 1000.00,
        'payment_method' => 'stripe',
    ]);

    $response->assertStatus(201)
        ->assertJsonStructure([
            'data' => [
                'id',
                'type',
                'amount',
                'balance_after',
            ],
        ]);

    expect((float) $this->wallet->fresh()->balance)->toBe(1500.00);
});

test('user can view transaction history', function (): void {
    // Create some transactions
    WalletTransaction::factory()->count(5)->forWallet($this->wallet)->create();

    $response = $this->getJson('/api/wallet/transactions');

    $response->assertStatus(200)
        ->assertJsonCount(5, 'data');
});
