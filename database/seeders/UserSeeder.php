<?php

namespace Database\Seeders;

use App\Models\ConsultingRoom;
use App\Models\Specialization;
use App\Models\User;
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
            'gender' => fake()->randomElement(['M', 'F']),
            'fesa' => fake()->randomNumber(),
            'email' => 'gabriel@studio-8.co',
            'is_admin' => true,
            'email_verified_at' => now(),
            'password' => Hash::make('DUWtXrzfveVmk4JhLxu6pM'),
        ]);
        User::create([
            'first_name' => 'admin',
            'name' => 'admin',
            'last_name1' => 'admin',
            'last_name2' => 'admin',
            'gender' => fake()->randomElement(['M', 'F']),
            'fesa' => fake()->randomNumber(),
            'email' => 'dan@studio-8.co',
            'is_admin' => true,
            'email_verified_at' => now(),
            'password' => Hash::make('vqFYNMyn7fDU3HJcPj5Qm2'),
        ]);
        User::create([
            'first_name' => 'admin',
            'name' => 'admin',
            'last_name1' => 'admin',
            'last_name2' => 'admin',
            'gender' => fake()->randomElement(['M', 'F']),
            'fesa' => fake()->randomNumber(),
            'email' => 'victor.hernandez@fanafesa.com',
            'is_admin' => true,
            'email_verified_at' => now(),
            'password' => Hash::make('vHjqFPr9b4AQpyTausX7kd'),
        ]);
        User::factory(10)
            ->has(ConsultingRoom::factory()->count(3), 'rooms')
            ->has(Specialization::factory()->count(3), 'rooms')
            ->create();
    }
}
