<?php

namespace Database\Seeders;

use App\Models\Patient;
use App\Models\Prescription;
use App\Models\User;
use App\Services\CertificateService;
use Illuminate\Database\Seeder;

class TestSeeder extends Seeder
{
    public function run(): void
    {
        User::factory()
            ->hasSpecialty()
            ->hasRooms(3)
            ->hasPatients(10)
            ->hasPrescriptions(5, fn (array $attributes, User $user) => Prescription::factory()->makePrescription($user))
            ->afterCreating(fn (User $user) => $this->generateCertificate($user))
            ->create([
                'email' => 'example@example.com',
            ]);
    }

    protected function generateCertificate(User $user): void
    {
        $certificate = app(CertificateService::class)->generateForUser($user);

        $user->update([
            'certificate_path' => $certificate['certificate_path'],
            'certificate_key_path' => $certificate['key_path'],
            'certificate_expires_at' => $certificate['expires_at'],
        ]);
    }
}
