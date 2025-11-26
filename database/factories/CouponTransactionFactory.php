<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Coupon;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

final class CouponTransactionFactory extends Factory
{
    public function definition(): array
    {
        $status = $this->faker->randomElement(['pending', 'success', 'failed']);
        $deliveryMethods = $this->faker->randomElements(['sms', 'email', 'whatsapp', 'print'], $this->faker->numberBetween(1, 3));

        return [
            'user_id' => User::factory(),
            'payment_method' => 'wallet',
            'coupon_id' => Coupon::factory(),
            'transaction_id' => $this->faker->unique()->bothify('TXN##??##??##??##'),
            'amount' => $this->faker->randomFloat(2, 10, 1000),
            'delivery_methods' => $deliveryMethods,
            'status' => $status,
            'coupon_delivered_at' => $status === 'success' ? $this->faker->dateTimeBetween('-1 month', 'now') : null,
            'created_at' => $this->faker->dateTimeBetween('-3 months', 'now'),
        ];
    }

    public function forUser(User $user): static
    {
        return $this->state(fn (array $attributes): array => [
            'user_id' => $user->id,
        ]);
    }

    public function forCoupon(Coupon $coupon): static
    {
        return $this->state(fn (array $attributes): array => [
            'coupon_id' => $coupon->id,
            'amount' => $coupon->selling_price,
        ]);
    }

    public function success(): static
    {
        return $this->state(fn (array $attributes): array => [
            'status' => 'success',
            'coupon_delivered_at' => $this->faker->dateTimeBetween('-1 month', 'now'),
        ]);
    }

    public function pending(): static
    {
        return $this->state(fn (array $attributes): array => [
            'status' => 'pending',
            'coupon_delivered_at' => null,
        ]);
    }

    public function failed(): static
    {
        return $this->state(fn (array $attributes): array => [
            'status' => 'failed',
            'coupon_delivered_at' => null,
        ]);
    }
}
