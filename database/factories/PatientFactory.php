<?php

namespace Database\Factories;

use App\Models\Patient;
use App\Models\User;
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
            'user_id' => User::factory(),
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'identification' => fake()->unique()->numberBetween(),
            'email' => fake()->safeEmail(),
            'phone' => [fake()->phoneNumber()],
            'gender' => fake()->randomElement(array_keys(config('custom.gender'))),
            'birth_date' => fake()->date(),
        ];
    }
}
