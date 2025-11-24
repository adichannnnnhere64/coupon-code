<?php

declare(strict_types=1);

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

final class PlanTypeFactory extends Factory
{
    public function definition(): array
    {
        $planTypes = [
            ['name' => 'full_talktime', 'description' => '100% balance without additional benefits'],
            ['name' => 'sms_pack', 'description' => 'SMS-only plans with unlimited or limited SMS'],
            ['name' => 'data_pack', 'description' => 'Internet data plans with high-speed data'],
            ['name' => 'combo_pack', 'description' => 'Combined talktime, SMS, and data offers'],
            ['name' => 'special_offer', 'description' => 'Limited time special offers and promotions'],
            ['name' => 'roaming_pack', 'description' => 'International and domestic roaming packs'],
        ];

        $planType = $this->faker->randomElement($planTypes);

        return [
            'name' => $planType['name'],
            'description' => $planType['description'],
        ];
    }
}
