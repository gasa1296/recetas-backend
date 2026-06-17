<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $seeders = [
            AdminUserSeeder::class,
            MedicamentSeeder::class,
        ];
        if (config('app.env') === 'local') {
            $seeders[] = TestSeeder::class;
        }
        $this->call($seeders);
    }
}
