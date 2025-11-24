<?php

declare(strict_types=1);

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

final class CountryFactory extends Factory
{
    private array $countries = [
        ['name' => 'India', 'code' => 'IN', 'currency' => 'INR'],
        ['name' => 'United States', 'code' => 'US', 'currency' => 'USD'],
        ['name' => 'United Kingdom', 'code' => 'UK', 'currency' => 'GBP'],
        ['name' => 'Canada', 'code' => 'CA', 'currency' => 'CAD'],
        ['name' => 'Australia', 'code' => 'AU', 'currency' => 'AUD'],
        ['name' => 'Germany', 'code' => 'DE', 'currency' => 'EUR'],
        ['name' => 'France', 'code' => 'FR', 'currency' => 'EUR'],
        ['name' => 'Japan', 'code' => 'JP', 'currency' => 'JPY'],
    ];

    public function definition(): array
    {
        $country = $this->faker->unique()->randomElement($this->countries);

        return [
            'name' => $country['name'],
            'code' => $country['code'],
            'currency' => $country['currency'],
            'is_active' => $this->faker->boolean(90),
        ];
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
}
