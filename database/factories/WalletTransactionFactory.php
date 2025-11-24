<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Wallet;
use Illuminate\Database\Eloquent\Factories\Factory;

final class WalletTransactionFactory extends Factory
{
    public function definition(): array
    {
        $type = $this->faker->randomElement(['credit', 'debit']);
        $amount = $this->faker->randomFloat(2, 10, 1000);

        return [
            'wallet_id' => Wallet::factory(),
            'type' => $type,
            'amount' => $amount,
            'balance_after' => $this->faker->randomFloat(2, 0, 5000),
            'description' => $this->getTransactionDescription($type),
            'reference_id' => $this->faker->unique()->bothify('REF##??##??##??##'),
            'metadata' => $this->faker->optional()->passthrough(['payment_gateway' => 'stripe']),
            'created_at' => $this->faker->dateTimeBetween('-6 months', 'now'),
        ];
    }

    public function forWallet(Wallet $wallet): static
    {
        return $this->state(fn (array $attributes): array => [
            'wallet_id' => $wallet->id,
        ]);
    }

    public function credit(): static
    {
        return $this->state(fn (array $attributes): array => [
            'type' => 'credit',
            'description' => $this->getTransactionDescription('credit'),
        ]);
    }

    public function debit(): static
    {
        return $this->state(fn (array $attributes): array => [
            'type' => 'debit',
            'description' => $this->getTransactionDescription('debit'),
        ]);
    }

    public function withAmount(float $amount): static
    {
        return $this->state(fn (array $attributes): array => [
            'amount' => $amount,
        ]);
    }

    private function getTransactionDescription(string $type): string
    {
        $descriptions = [
            'credit' => [
                'Wallet top-up via Stripe',
                'Manual wallet credit by admin',
                'Refund for failed transaction',
                'Bonus credit',
                'Wallet recharge via UPI',
            ],
            'debit' => [
                'Coupon purchase',
                'Service fee',
                'Commission charge',
                'Refund to user',
            ],
        ];

        return $this->faker->randomElement($descriptions[$type]);
    }
}
