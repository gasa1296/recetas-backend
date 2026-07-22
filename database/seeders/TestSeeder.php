<?php

namespace Database\Seeders;

use App\Models\Patient;
use App\Models\User;
use App\Models\Country;
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
                $conf = config('custom.professional_identification.' . $user->country_code, []);
                return [
                    'identification' => collect($conf)->mapWithKeys(function ($item, $key) {
                        return [$key => fake()->unique()->word()];
                    })->toArray()
                ];
            })
            ->hasRooms(3)
            ->hasPrescriptions(5, fn (array $attributes, User $user) => [
                'patient_id' => Patient::inRandomOrder()->first()->id,
                'room_id' => $user->rooms()->inRandomOrder()->first()->id,
                'specialty_id' => $user->specialty->id,
            ])->create([
                'email' => 'example@example.com',
                'country_code' => Country::inRandomOrder()->first()->iso2,
            ]);
        User::factory()
            ->hasSpecialty(1, function (array $attributes, User $user) {
                $conf = config('custom.professional_identification.' . $user->country_code, []);
                return [
                    'identification' => collect($conf)->mapWithKeys(function ($item, $key) {
                        return [$key => fake()->unique()->word()];
                    })->toArray()
                ];
            })
            ->hasRooms(3)
            ->hasPrescriptions(5, fn (array $attributes, User $user) => [
                'patient_id' => Patient::inRandomOrder()->first()->id,
                'room_id' => $user->rooms()->inRandomOrder()->first()->id,
                'specialty_id' => $user->specialty->id,
            ])
            ->count(10)->create([
                'country_code' => Country::inRandomOrder()->first()->iso2,
            ]);
    }
}
