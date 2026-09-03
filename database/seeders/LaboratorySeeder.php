<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\Laboratory;
use Illuminate\Database\Seeder;

class LaboratorySeeder extends Seeder
{
    public function run(): void
    {
        $laboratories = [
            [
                'name' => 'Laboratorios Leti',
                'code' => 'J-00032541-0',
                'country' => 'Venezuela',
                'brands' => ['Letisan', 'Flemibar', 'Terbifin', 'Ciprolet', 'Cetirivax'],
            ],
            [
                'name' => 'Calox International',
                'code' => 'J-00084512-4',
                'country' => 'Venezuela',
                'brands' => ['Atamel Forte', 'Dol', 'Amoval', 'Caloxmina', 'Clavumox'],
            ],
            [
                'name' => 'Laboratorios Behrens',
                'code' => 'J-00021458-1',
                'country' => 'Venezuela',
                'brands' => ['Behrens Solución', 'Dextrosa Behrens', 'Suero Oral Behrens'],
            ],
            [
                'name' => 'Genven Laboratorios',
                'code' => 'J-30485912-7',
                'country' => 'Venezuela',
                'brands' => ['Ibuprofeno Genven', 'Omeprazol Genven', 'Losartan Genven', 'Amoxicilina Genven'],
            ],
            [
                'name' => 'Bayer Healthcare',
                'code' => 'J-00142856-9',
                'country' => 'Alemania',
                'brands' => ['Aspirina Protect', 'Baycuten', 'Xarelto', 'Apronax', 'Canesten'],
            ],
            [
                'name' => 'Pfizer',
                'code' => 'J-00096521-3',
                'country' => 'Estados Unidos',
                'brands' => ['Lipitor', 'Zithromax', 'Celebrex', 'Lyrica', 'Diflucan'],
            ],
            [
                'name' => 'Laboratorios Elter',
                'code' => 'J-31084251-6',
                'country' => 'Venezuela',
                'brands' => ['Secnidal Elter', 'Ketoconazol Elter', 'Diclofenac Elter'],
            ],
            [
                'name' => 'Sanofi',
                'code' => 'J-00185496-2',
                'country' => 'Francia',
                'brands' => ['Plavix', 'Lantus', 'Allegra', 'Enterogermina', 'Doltrix'],
            ],
            [
                'name' => 'Laboratorios Roemmers',
                'code' => 'J-30514892-0',
                'country' => 'Argentina',
                'brands' => ['Sertal Compuesto', 'Taural', 'Amoxidal', 'Lotrial'],
            ],
        ];

        foreach ($laboratories as $labData) {
            $brands = $labData['brands'];
            unset($labData['brands']);

            $lab = Laboratory::firstOrCreate(['name' => $labData['name']], $labData);

            foreach ($brands as $brandName) {
                Brand::firstOrCreate([
                    'laboratory_id' => $lab->id,
                    'name' => $brandName,
                ]);
            }
        }
    }
}
