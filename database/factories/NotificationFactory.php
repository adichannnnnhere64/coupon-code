<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

final class NotificationFactory extends Factory
{
    public function definition(): array
    {
        $type = $this->faker->randomElement(['transaction', 'promotional', 'low_stock', 'wallet_credit', 'system']);
        $status = $this->faker->randomElement(['pending', 'sent', 'failed']);
        $channels = $this->faker->randomElements(['email', 'sms', 'whatsapp', 'push'], $this->faker->numberBetween(1, 3));

        return [
            'user_id' => $this->faker->optional(0.7)->passthrough(User::factory()), // 70% have user_id, 30% are broadcast
            'type' => $type,
            'title' => $this->getNotificationTitle($type),
            'message' => $this->getNotificationMessage($type),
            'channels' => $channels,
            'status' => $status,
            'sent_at' => $status === 'sent' ? $this->faker->dateTimeBetween('-1 month', 'now') : null,
            'created_at' => $this->faker->dateTimeBetween('-2 months', 'now'),
        ];
    }

    public function forUser(User $user): static
    {
        return $this->state(fn (array $attributes): array => [
            'user_id' => $user->id,
        ]);
    }

    public function broadcast(): static
    {
        return $this->state(fn (array $attributes): array => [
            'user_id' => null,
        ]);
    }

    public function sent(): static
    {
        return $this->state(fn (array $attributes): array => [
            'status' => 'sent',
            'sent_at' => $this->faker->dateTimeBetween('-1 month', 'now'),
        ]);
    }

    public function pending(): static
    {
        return $this->state(fn (array $attributes): array => [
            'status' => 'pending',
            'sent_at' => null,
        ]);
    }

    private function getNotificationTitle(string $type): string
    {
        return match ($type) {
            'transaction' => $this->faker->randomElement(['Transaction Successful', 'Payment Received', 'Coupon Delivered']),
            'promotional' => $this->faker->randomElement(['Special Offer!', 'New Coupons Available', 'Limited Time Discount']),
            'low_stock' => 'Low Stock Alert',
            'wallet_credit' => 'Wallet Credited',
            'system' => $this->faker->randomElement(['System Update', 'Maintenance Notice', 'New Features']),
            default => 'Notification',
        };
    }

    private function getNotificationMessage(string $type): string
    {
        return match ($type) {
            'transaction' => $this->faker->randomElement([
                'Your coupon purchase was successful.',
                'Payment of ₹{$amount} has been received.',
                'Your recharge coupon has been delivered to your email.',
            ]),
            'promotional' => $this->faker->randomElement([
                'Check out our new coupon denominations with special discounts!',
                'Get 5% cashback on all recharges this weekend.',
                'New operator added with exciting plans.',
            ]),
            'low_stock' => 'Some coupons are running low on stock. Please replenish soon.',
            'wallet_credit' => 'Your wallet has been credited with ₹{$amount}.',
            'system' => $this->faker->randomElement([
                'We are performing scheduled maintenance this weekend.',
                'New features have been added to your account.',
                'System update completed successfully.',
            ]),
            default => $this->faker->sentence(),
        };
    }
}
