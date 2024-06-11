<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\IOT_Device>
 */
class IOT_DeviceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'ip' => $this->faker->optional()->ipv4,
            'air_val' => $this->faker->optional()->randomFloat(2, 0, 100),
            'left_status' => $this->faker->optional()->randomFloat(2, 0, 100),
            'right_status' => $this->faker->optional()->randomFloat(2, 0, 100),
            'status' => $this->faker->optional()->sentence,
        ];
    }
}
