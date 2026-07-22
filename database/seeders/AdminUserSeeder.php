<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Country;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'first_name' => 'Admin',
            'last_name' => 'User',
            'identification' => 'admin',
            'phone' => ['000-000-0000'],
            'email' => 'admin@admin.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'is_admin' => true,
            'signature_hash' => hash('sha256', Str::random(32)),
            'country_id' => Country::where('code', 've')->first()->id, // Assuming 've' is the code for Venezuela
        ]);
    }
}
