<?php

namespace Database\Factories;

use App\Models\Prescription;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Prescription>
 */
class PrescriptionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'temp' => fake()->randomFloat(1, 36, 42),
            'weight' => fake()->randomFloat(1, 50, 120),
            'height' => fake()->randomFloat(1, 150, 200),
            'pressure' => fake()->randomFloat(1, 80, 140),
            'saturation' => fake()->randomFloat(1, 95, 100),
            'ppm' => fake()->randomFloat(1, 0, 100),
            'allergy' => fake()->sentence(),
            'diagnostic' => fake()->paragraph(),
            'diet' => fake()->sentence(),
            'comments' => fake()->paragraph(),
            'status' => 0,
        ];
    }
}
