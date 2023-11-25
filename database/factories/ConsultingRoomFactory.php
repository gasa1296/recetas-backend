<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ConsultingRoom>
 */
class ConsultingRoomFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            "name"=> fake()->name(),
            "zip" => fake()->randomNumber(),
            "street" => fake()->streetAddress(),
            "colony" => fake()->city(),
            "state" => fake()->city(),
            "delegation" => fake()->city(),
            "n_exterior" => fake()->words(),
            "n_interior" => fake()->words(),
            "address" => fake()->address(),
            "phone" => fake()->phoneNumber(),
            "logo" => fake()->imageUrl(),
            "design" => fake()->randomElement([1,2,3]),
        ];
    }
}
