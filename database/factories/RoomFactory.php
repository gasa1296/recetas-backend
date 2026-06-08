<?php

namespace Database\Factories;

use App\Models\Room;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Room>
 */
class RoomFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->word(),
            'zip' => fake()->numerify('#######'),
            'street' => fake()->streetName(),
            'colony' => fake()->word(),
            'state' => fake()->word(),
            'delegation' => fake()->word(),
            'n_exterior' => fake()->word(),
            'n_interior' => fake()->optional(false)->word(),
            'address' => fake()->optional(false)->sentence(),
            'phone' => json_encode(fake()->phoneNumber()),
            'fav' => false,
            'auto_email' => false,
            'auto_whatsapp' => false,
        ];
    }
}
