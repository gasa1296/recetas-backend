<?php

namespace Database\Factories;

use App\Models\ConsultingRoom;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Prescription>
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
        $room = ConsultingRoom::factory()->create();
        
        return [
            'temp' => fake()->randomFloat(2, 20, 40),
            'weight' => fake()->randomFloat(2, 30, 300),
            'height' => fake()->randomFloat(2, 30, 300),
            'pressure' => fake()->randomNumber() . ' ' . fake()->randomNumber(),
            'saturation' => fake()->randomFloat(2, 1, 100),
            'ppm' => fake()->randomFloat(2, 1, 100),
            'allergy' => fake()->words(10, true),
            'diagnostic' => fake()->words(10, true),
            'diet' => fake()->words(10, true),
            'additional' => fake()->words(10, true),
            'user_id' => $room->user_id,
            'room_id' => $room->id,
            'patient_id' => Patient::factory()->create()->id,
            'file' => fake()->imageUrl(),
            'status' => fake()->randomNumber(3),
        ];
    }
}
