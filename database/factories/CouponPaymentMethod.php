<?php

declare(strict_types=1);

// database/factories/CouponPaymentMethodFactory.php

namespace Database\Factories;

use App\Models\Coupon;
use App\Models\PaymentMethod;
use Illuminate\Database\Eloquent\Factories\Factory;

final class CouponPaymentMethodFactory extends Factory
{
    public function definition(): array
    {
        return [
            'coupon_id' => Coupon::factory(),
            'payment_method_id' => PaymentMethod::factory(),
            'sort_order' => $this->faker->numberBetween(1, 10),
        ];
    }

    public function forCoupon(Coupon $coupon): static
    {
        return $this->state(fn (array $attributes): array => [
            'coupon_id' => $coupon->id,
        ]);
    }

    public function forPaymentMethod(PaymentMethod $paymentMethod): static
    {
        return $this->state(fn (array $attributes): array => [
            'payment_method_id' => $paymentMethod->id,
        ]);
    }

    public function withSortOrder(int $sortOrder): static
    {
        return $this->state(fn (array $attributes): array => [
            'sort_order' => $sortOrder,
        ]);
    }

    public function walletPayment(): static
    {
        return $this->state(fn (array $attributes): array => [
            'payment_method_id' => PaymentMethod::factory()->wallet(),
            'sort_order' => 1,
        ]);
    }

    public function stripePayment(): static
    {
        return $this->state(fn (array $attributes): array => [
            'payment_method_id' => PaymentMethod::factory()->stripe(),
            'sort_order' => 2,
        ]);
    }

    public function paypalPayment(): static
    {
        return $this->state(fn (array $attributes): array => [
            'payment_method_id' => PaymentMethod::factory()->paypal(),
            'sort_order' => 3,
        ]);
    }
}
