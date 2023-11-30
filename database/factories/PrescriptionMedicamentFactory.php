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
            'dose' => fake()->randomDigit(),
            'way' => fake()->words(10, true),
            'frequency' => fake()->randomNumber(24),
            'duration' => fake()->randomNumber(15),
            'medicament_id' => Medicament::factory()->create()->id,
            'prescription_id' => Prescription::factory()->create()->id,
        ];
    }
}
