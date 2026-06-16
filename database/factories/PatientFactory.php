<?php

namespace Database\Factories;

use App\Models\Patient;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Patient>
 */
class PatientFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'first_name' => fake()->firstName(),
            'last_name1' => fake()->lastName(),
            'last_name2' => fake()->lastName(),
            'email' => fake()->safeEmail(),
            'phone' => [fake()->phoneNumber()],
            'gender' => fake()->randomElement(['Male', 'Female']),
            'birth_date' => fake()->date(),
        ];
    }
}
