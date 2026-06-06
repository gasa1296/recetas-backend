<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Specialization>
 */
class SpecializationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->words(3, true),
            'identification' => fake()->unique()->words(3, true),
            'university' => fake()->words(3, true),
            'logo' => fake()->imageUrl(),
            'user_id' => User::factory()->create()->id,
        ];
    }
}
