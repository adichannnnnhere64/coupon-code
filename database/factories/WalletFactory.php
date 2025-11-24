<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

final class WalletFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'balance' => $this->faker->randomFloat(2, 0, 5000),
            'created_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'updated_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
        ];
    }

    public function forUser(User $user): static
    {
        return $this->state(fn (array $attributes): array => [
            'user_id' => $user->id,
        ]);
    }

    public function withBalance(float $balance): static
    {
        return $this->state(fn (array $attributes): array => [
            'balance' => $balance,
        ]);
    }

    public function zeroBalance(): static
    {
        return $this->state(fn (array $attributes): array => [
            'balance' => 0.00,
        ]);
    }

    public function highBalance(): static
    {
        return $this->state(fn (array $attributes): array => [
            'balance' => $this->faker->randomFloat(2, 1000, 10000),
        ]);
    }
}
