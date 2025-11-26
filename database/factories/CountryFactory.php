<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Country;
use App\Services\MediaService;
use Illuminate\Database\Eloquent\Factories\Factory;

final class CountryFactory extends Factory
{
    private const array COUNTRIES = [
        'IN' => ['name' => 'India', 'currency' => 'INR'],
        'US' => ['name' => 'United States', 'currency' => 'USD'],
        'UK' => ['name' => 'United Kingdom', 'currency' => 'GBP'],
        'CA' => ['name' => 'Canada', 'currency' => 'CAD'],
        'AU' => ['name' => 'Australia', 'currency' => 'AUD'],
        'DE' => ['name' => 'Germany', 'currency' => 'EUR'],
        'FR' => ['name' => 'France', 'currency' => 'EUR'],
        'JP' => ['name' => 'Japan', 'currency' => 'JPY'],
    ];

    public function definition(): array
    {
        $existingCodes = Country::query()->pluck('code')->toArray();

        $availableCodes = array_diff(array_keys(self::COUNTRIES), $existingCodes);

        $code = $this->faker->randomElement($availableCodes);
        $country = self::COUNTRIES[$code];

        return [
            'name' => $country['name'],
            'code' => $code,
            'currency' => $country['currency'],
            'is_active' => $this->faker->boolean(90),
        ];
    }

    public function withImage(string $url = 'https://placehold.co/600x400/png', string $collection = 'default'): static
    {
        return $this->afterCreating(function (Country $country) use ($url, $collection): void {
            app(MediaService::class)->attachImageFromUrl($country, $url, $collection);
        });
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
