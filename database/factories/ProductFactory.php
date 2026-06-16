<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $locales = ['en', 'ar', 'fr'];
        $translations = collect($locales)->mapWithKeys(function ($locale) {
            return [
                $locale => [
                    'name' => fake($locale)->words(3, true),
                    'description' => fake($locale)->paragraph(4),

                ]
            ];
        })->toArray();

        $prices = [
            30000,
            40500,
            50500,
            60900,
            30500,
            40000,
            50000,
            60000,
        ];
        $arr =  array_merge($translations, [
            'status' => fake()->randomElement(['published', 'hidden']),
            'rate' => fake()->randomFloat(1, 1, 5), // Between 0.0 and 5.0
            'price' => fake()->randomElement($prices), // Example range
            'type' => 'product',
            'shipping_name' => fake()->words(2, true)
        ]);
        return $arr;
    }
    public function pack(): static
    {
        return $this->state(fn(array $attributes) => [
            'type' => 'pack',
            'ends_at' => '2025-01-15'
        ]);
    }
}
