<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
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
            "zip" => fake()->postcode(),
            "street" => fake()->streetAddress(),
            "colony" => fake()->city(),
            "state" => fake()->city(),
            "delegation" => fake()->city(),
            "n_exterior" => fake()->randomNumber(),
            "n_interior" => fake()->randomNumber(),
            "address" => fake()->address(),
            "phone" => fake()->phoneNumber(),
            "logo" => fake()->imageUrl(),
            "design" => Hash::make(fake()->randomNumber()),
            'user_id' => User::factory()->create()->id,

        ];
    }
}
