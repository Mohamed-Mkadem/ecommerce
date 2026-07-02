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
            'price' => fake()->randomElement($prices), 
            'shipping_name' => fake()->words(2, true)
        ]);
        return $arr;
    }
   
}
