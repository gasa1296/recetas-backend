<?php

namespace Database\Seeders;

use App\Models\Medicament;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MedicamentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $medicaments = json_decode(
            file_get_contents(__DIR__.'/medicaments.json'),
            true,
            flags: JSON_THROW_ON_ERROR
        );

        DB::transaction(function () use ($medicaments) {
            foreach (array_chunk($medicaments, 500) as $chunk) {
                Medicament::insert(
                    array_map(fn (array $med) => [
                        'salt' => $med['salt'],
                        'type'=> $med['type'],
                        'group' => $med['group'],
                    ], $chunk)
                );
            }
        });
    }
}
