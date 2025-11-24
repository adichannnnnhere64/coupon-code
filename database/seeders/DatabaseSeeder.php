<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Country;
use App\Models\Coupon;
use App\Models\CouponTransaction;
use App\Models\Notification;
use App\Models\Operator;
use App\Models\PlanType;
use App\Models\User;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

final class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create specific countries
        $countries = [
            ['name' => 'India', 'code' => 'IN', 'currency' => 'INR', 'is_active' => true],
            ['name' => 'United States', 'code' => 'US', 'currency' => 'USD', 'is_active' => true],
            ['name' => 'United Kingdom', 'code' => 'UK', 'currency' => 'GBP', 'is_active' => true],
            ['name' => 'Canada', 'code' => 'CA', 'currency' => 'CAD', 'is_active' => true],
            ['name' => 'Australia', 'code' => 'AU', 'currency' => 'AUD', 'is_active' => true],
        ];

        foreach ($countries as $countryData) {
            Country::query()->create($countryData);
        }

        // Create plan types
        $planTypes = [
            ['name' => 'full_talktime', 'description' => '100% balance without additional benefits'],
            ['name' => 'sms_pack', 'description' => 'SMS-only plans with unlimited or limited SMS'],
            ['name' => 'data_pack', 'description' => 'Internet data plans with high-speed data'],
            ['name' => 'combo_pack', 'description' => 'Combined talktime, SMS, and data offers'],
            ['name' => 'special_offer', 'description' => 'Limited time special offers and promotions'],
        ];

        foreach ($planTypes as $planTypeData) {
            PlanType::query()->create($planTypeData);
        }

        // Create operators for each country
        $countryOperators = [
            'IN' => [
                ['name' => 'Jio', 'code' => 'JIO', 'is_active' => true],
                ['name' => 'Airtel', 'code' => 'AIRTEL', 'is_active' => true],
                ['name' => 'Vodafone Idea', 'code' => 'VODA', 'is_active' => true],
                ['name' => 'BSNL', 'code' => 'BSNL', 'is_active' => true],
            ],
            'US' => [
                ['name' => 'Verizon', 'code' => 'VERIZ', 'is_active' => true],
                ['name' => 'AT&T', 'code' => 'ATT', 'is_active' => true],
                ['name' => 'T-Mobile', 'code' => 'TMOB', 'is_active' => true],
            ],
            'UK' => [
                ['name' => 'EE', 'code' => 'EE', 'is_active' => true],
                ['name' => 'O2', 'code' => 'O2', 'is_active' => true],
                ['name' => 'Vodafone UK', 'code' => 'VODUK', 'is_active' => true],
            ],
        ];

        foreach ($countryOperators as $countryCode => $operators) {
            $country = Country::query()->where('code', $countryCode)->first();
            foreach ($operators as $operatorData) {
                Operator::query()->create(array_merge($operatorData, ['country_id' => $country->id]));
            }
        }

        // Create admin user
        $admin = User::query()->create([
            'name' => 'Admin User',
            'email' => 'admin@recharge.com',
            'phone' => '+919876543210',
            'password' => Hash::make('password'),
            'status' => 'active',
            'email_verified_at' => now(),
            'phone_verified_at' => now(),
        ]);

        Wallet::query()->create([
            'user_id' => $admin->id,
            'balance' => 10000.00,
        ]);

        // Create test users with wallets
        $users = User::factory()
            ->count(50)
            ->active()
            ->create()
            ->each(function ($user): void {
                Wallet::factory()->forUser($user)->withBalance(1000.00)->create();
            });

        // Create coupons for Indian operators
        $indianOperators = Operator::query()->whereHas('country', fn ($q) => $q->where('code', 'IN'))->get();
        $planTypes = PlanType::all();

        foreach ($indianOperators as $operator) {
            foreach ($planTypes as $planType) {
                Coupon::factory()
                    ->count(10)
                    ->forOperator($operator)
                    ->forPlanType($planType)
                    ->inStock()
                    ->active()
                    ->create();
            }
        }

        // Create some low stock and out of stock coupons
        Coupon::factory()
            ->count(5)
            ->lowStock()
            ->active()
            ->create();

        Coupon::factory()
            ->count(3)
            ->outOfStock()
            ->active()
            ->create();

        // Create wallet transactions for users
        foreach ($users as $user) {
            WalletTransaction::factory()
                ->count(5)
                ->forWallet($user->wallet)
                ->credit()
                ->create();

            WalletTransaction::factory()
                ->count(3)
                ->forWallet($user->wallet)
                ->debit()
                ->create();
        }

        // Create coupon transactions
        $activeCoupons = Coupon::query()->where('stock_quantity', '>', 0)->get();

        foreach ($users->take(30) as $user) {
            $coupons = $activeCoupons->random(fake()->numberBetween(1, 5));

            foreach ($coupons as $coupon) {
                CouponTransaction::factory()
                    ->forUser($user)
                    ->forCoupon($coupon)
                    ->success()
                    ->create();

                // Decrement coupon stock
                $coupon->decrement('stock_quantity');
            }
        }

        // Create some failed and pending transactions
        CouponTransaction::factory()
            ->count(10)
            ->failed()
            ->create();

        CouponTransaction::factory()
            ->count(5)
            ->pending()
            ->create();

        // Create notifications
        Notification::factory()
            ->count(20)
            ->sent()
            ->create();

        Notification::factory()
            ->count(5)
            ->pending()
            ->create();

        Notification::factory()
            ->count(10)
            ->broadcast()
            ->sent()
            ->create();

        $this->command->info('Database seeded successfully!');
        $this->command->info('Admin Login: admin@recharge.com / password');
        $this->command->info('Test users created with wallets and transactions.');
    }
}
