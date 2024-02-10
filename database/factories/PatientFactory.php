<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Patient>
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
            "first_name" => fake()->firstName(),
            "last_name1" => fake()->lastName(),
            "last_name2" => fake()->lastName(),
            "email" => fake()->email(),
            "phone1" => fake()->phoneNumber(),
            "phone2" => fake()->phoneNumber(),
            "birth_date" => fake()->date(),
            'gender' => fake()->randomElement(['M', 'F']),
            'user_id' => 1,
        ];
    }
}
