<?php

namespace Database\Seeders;

use App\Models\University;
use App\Models\User;
use Illuminate\Database\Seeder;

class TestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        User::factory()
            ->hasSpecialties(3, function (array $attributes, User $user) {
                return [
                    'university' => University::inRandomOrder()->first()->name,
                ];
            })
            ->hasPatients(5)
            ->hasRooms(3)
            ->hasPrescriptions(5, function (array $attributes, User $user) {
                return [
                    'patient_id' => $user->patients()->inRandomOrder()->first()->id,
                    'room_id' => $user->rooms()->inRandomOrder()->first()->id,
                    // 'specialty_id' => $user->specialties()->inRandomOrder()->first()->id,
                ];
            })->create([
                'email' => 'example@example.com',
            ]);
        User::factory()
            ->hasSpecialties(3, function (array $attributes, User $user) {
                return [
                    'university' => University::inRandomOrder()->first()->name,
                ];
            })
            ->hasPatients(5)
            ->hasRooms(3)
            ->hasPrescriptions(5, function (array $attributes, User $user) {
                return [
                    'patient_id' => $user->patients()->inRandomOrder()->first()->id,
                    'room_id' => $user->rooms()->inRandomOrder()->first()->id,
                    // 'specialty_id' => $user->specialties()->inRandomOrder()->first()->id,
                ];
            })
            ->count(10)->create();
    }
}
