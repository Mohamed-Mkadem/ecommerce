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

        $locality_id = fake()->numberBetween(1, 4876);

        $locality = \App\Models\Locality::find($locality_id);

        $city = $locality->city;

        $state = $city->state;
        return [
            'name' => fake()->name(),
            'phone' => fake()->randomNumber(8, true),
            'state_id' => $state->id,
            'locality_id' => $locality->id,
            'city_id' => $city->id,
            'address' => fake()->streetAddress(),
        ];
    }
}
