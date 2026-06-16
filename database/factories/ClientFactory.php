<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Client>
 */
class ClientFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            //    'phone' => fake()->randomDigit(20000000, 99999999),
            'phone' => fake()->randomNumber(8, true),
            'state_id' => mt_rand(1, 24),
            'address' => fake()->streetAddress(),
        ];
    }
}
