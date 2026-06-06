<?php

namespace Database\Seeders;

use App\Models\Prescription;
use App\Models\PrescriptionMedicament;
use Illuminate\Database\Seeder;

class PrescriptionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        for ($i = 0; $i < 10; $i++) {
            Prescription::factory()
                ->has(PrescriptionMedicament::factory()->count(10), 'medicaments')
                ->create();
        }
    }
}
