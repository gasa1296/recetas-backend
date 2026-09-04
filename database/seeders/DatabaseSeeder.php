<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $seeders = [
            RoleSeeder::class,
            MedicamentSeeder::class,
            AdminUserSeeder::class,
        ];
        if (config('app.env') === 'local') {
            $seeders[] = TestSeeder::class;
        }
        $this->call($seeders);
    }
}
