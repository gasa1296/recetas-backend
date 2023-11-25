<?php

namespace Database\Seeders;

use App\Models\ConsultingRoom;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ConsultingRoomSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::factory(10)
            ->has(ConsultingRoom::factory()->count(3), 'rooms')
            ->create();
    }
}
