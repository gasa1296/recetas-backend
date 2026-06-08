<?php

namespace Database\Seeders;

use App\Models\University;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UniversitySeeder extends Seeder
{
    public function run(): void
    {
        $universities = json_decode(
            file_get_contents(__DIR__.'/universities.json'),
            true,
            flags: JSON_THROW_ON_ERROR
        );

        DB::transaction(function () use ($universities) {
            foreach (array_chunk($universities, 500) as $chunk) {
                University::insert(
                    array_map(fn (array $uni) => [
                        'name' => $uni['name'],
                        // 'web_pages' => json_encode($uni['web_pages']),
                        // 'domains' => json_encode($uni['domains']),
                        'alpha_two_code' => $uni['alpha_two_code'],
                        'country' => $uni['country'],
                        // 'state_province' => $uni['state-province'],
                    ], $chunk)
                );
            }
        });
    }
}
