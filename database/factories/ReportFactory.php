<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Report>
 */
class ReportFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $user = \App\Models\User::inRandomOrder()->first();
        $role_of_creator = $user->roles->pluck('name')->first();
        return [
            'type' => $this->faker->randomElement(['user', 'device']),
            'user_id' => function (array $attributes) {
                return $attributes['type'] === 'user' ? \App\Models\User::factory()->create()->id : null;
            },
            'device_id' => function (array $attributes) {
                return $attributes['type'] === 'device' ? \App\Models\IOT_Device::factory()->create()->id : null;
            },
            'description' => $this->faker->sentence,
            'vote' => $this->faker->numberBetween(1, 5),
            'status' => $this->faker->randomElement(['pending', 'in progress', 'resolved']),
            'created_by' => $user,
            'role_of_creator' => $role_of_creator,
        ];
    }
}
