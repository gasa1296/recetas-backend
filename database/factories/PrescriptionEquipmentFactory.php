<?php

namespace Database\Factories;

use App\Models\Equipment;
use App\Models\Prescription;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PrescriptionEquipment>
 */
class PrescriptionEquipmentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'add' => fake()->words(10, true),
            'equipment_id' => Equipment::factory()->create()->id,
            'prescription_id' => Prescription::factory()->create()->id,
        ];
    }
}
