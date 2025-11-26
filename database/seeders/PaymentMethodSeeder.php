<?php

declare(strict_types=1);

// database/seeders/PaymentMethodSeeder.php

namespace Database\Seeders;

use App\Models\PaymentMethod;
use Illuminate\Database\Seeder;

final class PaymentMethodSeeder extends Seeder
{
    public function run(): void
    {
        $paymentMethods = [
            [
                'name' => 'Wallet',
                'code' => 'wallet',
                'display_name' => 'Wallet Balance',
                'description' => 'Pay using your wallet balance',
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'name' => 'Stripe',
                'code' => 'stripe',
                'display_name' => 'Credit/Debit Card',
                'description' => 'Pay securely with your credit or debit card',
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'name' => 'PayPal',
                'code' => 'paypal',
                'display_name' => 'PayPal',
                'description' => 'Pay with your PayPal account',
                'is_active' => true,
                'sort_order' => 3,
            ],
        ];

        foreach ($paymentMethods as $method) {
            PaymentMethod::query()->create($method);
        }
    }
}
