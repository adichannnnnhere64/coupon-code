<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Coupon;
use App\Models\Operator;
use App\Models\PaymentMethod;
use App\Models\PlanType;
use App\Services\MediaService;
use Illuminate\Database\Eloquent\Factories\Factory;

final class CouponFactory extends Factory
{
    public function definition(): array
    {
        $denominations = [10, 20, 50, 100, 200, 300, 500, 1000];
        $denomination = $this->faker->randomElement($denominations);

        // Selling price is usually slightly higher than denomination
        $sellingPrice = $denomination + $this->faker->randomFloat(2, 1, 10);

        return [
            'operator_id' => Operator::factory(),
            'plan_type_id' => PlanType::factory(),
            'denomination' => $denomination,
            'selling_price' => $sellingPrice,
            'coupon_code' => $this->faker->unique()->bothify('??????##??##??##'),
            'serial_number' => $this->faker->unique()->bothify('SN##??##??##??##'),
            'validity_days' => $this->faker->randomElement([7, 15, 30, 60, 90, 180, 365]),
            'stock_quantity' => $this->faker->numberBetween(0, 1000),
            'low_stock_threshold' => $this->faker->numberBetween(5, 20),
            'is_active' => $this->faker->boolean(90),
            'created_at' => $this->faker->dateTimeBetween('-6 months', 'now'),
        ];
    }

    public function withImage(string $url = 'https://placehold.co/600x400/png', string $collection = 'default'): static
    {
        return $this->afterCreating(function (Coupon $operator) use ($url, $collection): void {
            app(MediaService::class)->attachImageFromUrl($operator, $url, $collection);

        });
    }

    public function forOperator(Operator $operator): static
    {
        return $this->state(fn (array $attributes): array => [
            'operator_id' => $operator->id,
        ]);
    }

    public function forPlanType(PlanType $planType): static
    {
        return $this->state(fn (array $attributes): array => [
            'plan_type_id' => $planType->id,
        ]);
    }

    public function active(): static
    {
        return $this->state(fn (array $attributes): array => [
            'is_active' => true,
        ]);
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes): array => [
            'is_active' => false,
        ]);
    }

    public function inStock(): static
    {
        return $this->state(fn (array $attributes): array => [
            'stock_quantity' => $this->faker->numberBetween(10, 1000),
        ]);
    }

    public function outOfStock(): static
    {
        return $this->state(fn (array $attributes): array => [
            'stock_quantity' => 0,
        ]);
    }

    public function lowStock(): static
    {
        return $this->state(fn (array $attributes): array => [
            'stock_quantity' => $this->faker->numberBetween(1, 5),
        ]);
    }

    public function withDenomination(float $denomination): static
    {
        return $this->state(fn (array $attributes): array => [
            'denomination' => $denomination,
            'selling_price' => $denomination + $this->faker->randomFloat(2, 1, 5),
        ]);
    }

    public function withPaymentMethods(array $paymentMethodCodes = ['wallet', 'stripe']): static
    {
        return $this->afterCreating(function (Coupon $coupon) use ($paymentMethodCodes): void {
            $sortOrder = 1;
            foreach ($paymentMethodCodes as $code) {
                $paymentMethod = PaymentMethod::query()->where('code', $code)->first();

                if ($paymentMethod) {
                    $coupon->paymentMethods()->attach($paymentMethod->id, [
                        'sort_order' => $sortOrder++,
                    ]);
                }
            }
        });
    }

    public function withWalletOnly(): static
    {
        return $this->withPaymentMethods(['wallet']);
    }

    public function withAllPaymentMethods(): static
    {
        return $this->withPaymentMethods(['wallet', 'stripe', 'paypal']);
    }

    public function withGatewayPaymentsOnly(): static
    {
        return $this->withPaymentMethods(['stripe', 'paypal']);
    }
}
