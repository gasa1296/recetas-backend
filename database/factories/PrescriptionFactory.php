<?php

namespace Database\Factories;

use app\Models\Patient;
use App\Models\Prescription;
use app\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

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
            'prescription_hash' => hash('sha256', Str::random(32)),
            'status' => 0,
        ];
    }

    public function makePrescription(User $user): array
    {
        return [
            'patient_id' => Patient::inRandomOrder()->first()->id,
            'room_id' => $user->rooms()->inRandomOrder()->first()->id,
            'specialty_id' => $user->specialty->id,
        ];
    }
}
