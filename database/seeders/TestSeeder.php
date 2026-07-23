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
            ->hasSpecialty(1, fn () => $this->makeIdentification())
            ->hasRooms(3)
            ->hasPrescriptions(5, fn (array $attributes, User $user) => $this->makePrescription($user))->create([
                'email' => 'example@example.com',
            ]);
        User::factory()
            ->hasSpecialty(1, fn () => $this->makeIdentification())
            ->hasRooms(3)
            ->hasPrescriptions(5, fn (array $attributes, User $user) => $this->makePrescription($user))
            ->count(10)->create();
    }

    private function makePrescription(User $user): array
    {
        return [
            'patient_id' => Patient::inRandomOrder()->first()->id,
            'room_id' => $user->rooms()->inRandomOrder()->first()->id,
            'specialty_id' => $user->specialty->id,
        ];
    }

    private function makeIdentification(): array
    {
        $conf = config('custom.professional_identification', []);

        return [
            'identification' => collect($conf)->mapWithKeys(function ($item, $key) {
                return [$key => fake()->unique()->numberBetween(100000, 999999)];
            })->toArray(),
        ];
    }
}
