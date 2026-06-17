<?php

namespace Database\Factories;

use App\Models\Specialty;
use App\Models\University;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Specialty>
 */
class SpecialtyFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->words(rand(3, 8), true),
            'identification' => strtoupper(fake()->bothify('???-?????')),
        ];
    }

    /**
     * Indicate that the specialty should not have a university assigned.
     */
    public function withoutUniversity(): static
    {
        return $this->state(fn (array $attributes) => [
            'university' => null,
        ]);
    }
}
