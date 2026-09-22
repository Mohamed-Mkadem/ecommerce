<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\ProductWrapper;
use App\Models\Wrapper;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Wrapper>
 */
class WrapperFactory extends Factory
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
                    'title' => fake($locale)->words(3, true),
                    'description' => fake($locale)->paragraph(4)
                ]
            ];
        })->toArray();

        return array_merge($translations, [
            'caption' => fake()->words(2, true),
            'slug' => Str::slug(fake()->words(5, true)),
            'is_active' => fake()->boolean(),
        ]);
    }


    public function configure()
    {
        return $this->afterCreating(function (Wrapper $wrapper) {
           
        });
    }
}
