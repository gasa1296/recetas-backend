<?php

namespace Database\Seeders;

use App\Models\Prescription;
use App\Models\PrescriptionMedicament;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PrescriptionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Prescription::factory(10)
            ->has(PrescriptionMedicament::factory()->count(10), 'medicaments')
            ->create();
    }
}
