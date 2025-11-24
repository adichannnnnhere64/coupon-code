<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Country;
use Illuminate\Database\Eloquent\Factories\Factory;

final class OperatorFactory extends Factory
{
    public function definition(): array
    {
        return [
            'country_id' => Country::factory(),
            'name' => $this->faker->company(),
            'code' => $this->faker->unique()->lexify('???'),
            'logo_url' => $this->faker->optional()->imageUrl(100, 100, 'business', true),
            'is_active' => $this->faker->boolean(85),
        ];
    }

    public function forCountry(Country $country): static
    {
        return $this->state(fn (array $attributes): array => [
            'country_id' => $country->id,
            'name' => $this->getOperatorForCountry($country),
            'code' => $this->getOperatorCodeForCountry($country),
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

    private function getOperatorForCountry(Country $country): string
    {
        $operators = match ($country->code) {
            'IN' => ['Jio', 'Airtel', 'Vodafone Idea', 'BSNL', 'MTNL'],
            'US' => ['Verizon', 'AT&T', 'T-Mobile', 'Sprint'],
            'UK' => ['EE', 'O2', 'Vodafone UK', 'Three'],
            'CA' => ['Rogers', 'Bell', 'Telus'],
            'AU' => ['Telstra', 'Optus', 'Vodafone AU'],
            default => [$this->faker->company()],
        };

        return $this->faker->randomElement($operators);
    }

    private function getOperatorCodeForCountry(Country $country): string
    {
        return match ($country->code) {
            'IN' => $this->faker->unique()->randomElement(['JIO', 'AIRTEL', 'VODA', 'BSNL', 'MTNL']),
            'US' => $this->faker->unique()->randomElement(['VERIZ', 'ATT', 'TMOB', 'SPRINT']),
            'UK' => $this->faker->unique()->randomElement(['EE', 'O2', 'VODUK', 'THREE']),
            default => $this->faker->unique()->lexify('???'),
        };
    }
}
