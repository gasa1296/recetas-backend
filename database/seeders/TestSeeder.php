<?php

namespace Database\Seeders;

use App\Models\Patient;
use App\Models\User;
use Illuminate\Database\Seeder;

class TestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Patient::factory()
            ->count(10)
            ->create();
        User::factory()
            ->hasSpecialty(1, function (array $attributes, User $user) {
                $conf = config('custom.professional_identification', []);

                return [
                    'identification' => collect($conf)->mapWithKeys(function ($item, $key) {
                        return [$key => fake()->unique()->numberBetween(100000, 999999)];
                    })->toArray(),
                ];
            })
            ->hasRooms(3)
            ->hasPrescriptions(5, fn (array $attributes, User $user) => [
                'patient_id' => Patient::inRandomOrder()->first()->id,
                'room_id' => $user->rooms()->inRandomOrder()->first()->id,
                'specialty_id' => $user->specialty->id,
            ])->create([
                'email' => 'example@example.com',
            ]);
        User::factory()
            ->hasSpecialty(1, function (array $attributes, User $user) {
                $conf = config('custom.professional_identification', []);

                return [
                    'identification' => collect($conf)->mapWithKeys(function ($item, $key) {
                        return [$key => fake()->unique()->numberBetween(100000, 999999)];
                    })->toArray(),
                ];
            })
            ->hasRooms(3)
            ->hasPrescriptions(5, fn (array $attributes, User $user) => [
                'patient_id' => Patient::inRandomOrder()->first()->id,
                'room_id' => $user->rooms()->inRandomOrder()->first()->id,
                'specialty_id' => $user->specialty->id,
            ])
            ->count(10)->create();
    }
}
