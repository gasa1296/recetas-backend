<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\ConsultingRoom;
use App\Models\Specialization;
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
            'phone1' => fake()->phoneNumber(),
            'phone2' => fake()->phoneNumber(),
            'gender' => fake()->randomElement(['M', 'F']),
            'fesa' => fake()->randomNumber(),
            'email' => 'admin@admin.com',
            'is_admin' => true,
            'email_verified_at' => now(),
            'password' => Hash::make('12345678'),
        ]);
        User::create([
            'first_name' => 'admin',
            'name' => 'admin',
            'last_name1' => 'admin',
            'last_name2' => 'admin',
            'phone1' => fake()->phoneNumber(),
            'phone2' => fake()->phoneNumber(),
            'gender' => 'M',
            'fesa' => fake()->randomNumber(),
            'email' => 'dan@studio-8.co',
            'is_admin' => true,
            'email_verified_at' => now(),
            'password' => Hash::make('1234567890'),
        ]);
        User::create([
            'first_name' => 'admin',
            'name' => 'admin',
            'last_name1' => 'admin',
            'last_name2' => 'admin',
            'phone1' => fake()->phoneNumber(),
            'phone2' => fake()->phoneNumber(),
            'gender' => 'M',
            'fesa' => fake()->randomNumber(),
            'email' => 'victor.hernandez@fanafesa.com',
            'is_admin' => true,
            'email_verified_at' => now(),
            'password' => Hash::make('1234567890'),
        ]);
        User::factory(10)
            ->has(ConsultingRoom::factory()->count(3), 'rooms')
            ->has(Specialization::factory()->count(3), 'rooms')
            ->create();
    }
}
