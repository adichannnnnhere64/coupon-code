<?php

declare(strict_types=1);

// database/factories/PaymentMethodFactory.php

namespace Database\Factories;

use App\Models\PaymentMethod;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PaymentMethod>
 */
final class PaymentMethodFactory extends Factory
{
    protected $model = PaymentMethod::class;

    public function definition(): array
    {
        $paymentMethods = [
            [
                'name' => 'Wallet',
                'code' => 'wallet',
                'display_name' => 'Wallet Balance',
                'description' => 'Pay using your wallet balance',
            ],
            [
                'name' => 'Stripe',
                'code' => 'stripe',
                'display_name' => 'Credit/Debit Card',
                'description' => 'Pay securely with your credit or debit card',
            ],
            [
                'name' => 'PayPal',
                'code' => 'paypal',
                'display_name' => 'PayPal',
                'description' => 'Pay with your PayPal account',
            ],
            [
                'name' => 'Bank Transfer',
                'code' => 'bank_transfer',
                'display_name' => 'Bank Transfer',
                'description' => 'Transfer funds directly from your bank account',
            ],
        ];

        $method = $this->faker->randomElement($paymentMethods);

        return [
            'name' => $method['name'],
            'code' => $method['code'],
            'display_name' => $method['display_name'],
            'description' => $method['description'],
            'is_active' => $this->faker->boolean(90),
            'config' => $this->faker->optional()->passthrough([
                'api_key' => $this->faker->optional()->sha1(),
                'webhook_secret' => $this->faker->optional()->sha1(),
                'test_mode' => $this->faker->boolean(),
            ]),
            'sort_order' => $this->faker->numberBetween(1, 10),
        ];
    }

    public function wallet(): static
    {
        return $this->state(fn (array $attributes): array => [
            'name' => 'Wallet',
            'code' => 'wallet',
            'display_name' => 'Wallet Balance',
            'description' => 'Pay using your wallet balance',
            'is_active' => true,
            'sort_order' => 1,
        ]);
    }

    public function stripe(): static
    {
        return $this->state(fn (array $attributes): array => [
            'name' => 'Stripe',
            'code' => 'stripe',
            'display_name' => 'Credit/Debit Card',
            'description' => 'Pay securely with your credit or debit card',
            'is_active' => true,
            'config' => [
                'publishable_key' => 'pk_test_'.$this->faker->sha1(),
                'secret_key' => 'sk_test_'.$this->faker->sha1(),
                'webhook_secret' => 'whsec_'.$this->faker->sha1(),
            ],
            'sort_order' => 2,
        ]);
    }

    public function paypal(): static
    {
        return $this->state(fn (array $attributes): array => [
            'name' => 'PayPal',
            'code' => 'paypal',
            'display_name' => 'PayPal',
            'description' => 'Pay with your PayPal account',
            'is_active' => true,
            'config' => [
                'client_id' => $this->faker->uuid(),
                'client_secret' => $this->faker->sha1(),
                'sandbox' => true,
            ],
            'sort_order' => 3,
        ]);
    }

    public function bankTransfer(): static
    {
        return $this->state(fn (array $attributes): array => [
            'name' => 'Bank Transfer',
            'code' => 'bank_transfer',
            'display_name' => 'Bank Transfer',
            'description' => 'Transfer funds directly from your bank account',
            'is_active' => true,
            'config' => [
                'account_number' => $this->faker->bankAccountNumber(),
                'routing_number' => $this->faker->randomNumber(9),
            ],
            'sort_order' => 4,
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

    public function withConfig(array $config): static
    {
        return $this->state(fn (array $attributes): array => [
            'config' => $config,
        ]);
    }

    public function withSortOrder(int $sortOrder): static
    {
        return $this->state(fn (array $attributes): array => [
            'sort_order' => $sortOrder,
        ]);
    }
}
