<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'first_name' => 'admin',
            'name' => 'admin',
            'last_name1' => 'admin',
            'last_name2' => 'admin',
            'identification' => fake()->randomNumber(),
            'especialization' => fake()->jobTitle(),
            'phone1' => fake()->phoneNumber(),
            'phone2' => fake()->phoneNumber(),
            'genre' => fake()->randomElement(['M', 'F']),
            'university' => fake()->name(),
            'fesa' => fake()->randomNumber(),
            'image' => fake()->imageUrl(),
            'email' => 'admin@admin.com',
            'is_admin' => true,
            'email_verified_at' => now(),
            'password' => Hash::make('12345678'),
        ]);
    }
}
