<?php

namespace Database\Factories;

use App\Models\Home;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Address>
 */
class AddressFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {

        return [
            'unit_number' => $this->faker->buildingNumber,
            'street_number' => $this->faker->numberBetween(1, 1000),
            'address_line' => $this->faker->streetAddress,
            'ward' => $this->faker->streetName,
            'district' => $this->faker->citySuffix,
            'city' => $this->faker->city,
            'province' => $this->faker->state,
            'country_name' => $this->faker->country,
        ];
    }
}
