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
            ->hasSpecialty()
            ->hasRooms(3)
            ->hasPrescriptions(5, fn (array $attributes, User $user) => $this->makePrescription($user))->create([
                'email' => 'example@example.com',
            ]);
        User::factory()
            ->hasSpecialty()
            ->hasRooms(3)
            ->hasPrescriptions(5, fn (array $attributes, User $user) => $this->makePrescription($user))
            ->count(10)->create();
    }
}
