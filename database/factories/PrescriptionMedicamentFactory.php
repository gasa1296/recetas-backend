<?php

namespace Database\Factories;

use App\Models\Medicament;
use App\Models\Prescription;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PrescriptionMedicament>
 */
class PrescriptionMedicamentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'dose' => fake()->word(),
            'way' => fake()->words(10, true),
            'frequency' => fake()->word(),
            'duration' => fake()->word(),
            "quantity" => fake()->randomDigit(),
            'medicament_id' => fake()->randomNumber(),
            'name' => fake()->word(),
            'type' => fake()->word(),
            'family' => fake()->word(),
            'group' => fake()->word(),
            'prescription_id' => Prescription::factory()->create()->id,
        ];
    }
}
