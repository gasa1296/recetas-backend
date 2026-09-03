<?php

namespace Database\Factories;

use App\Models\Brand;
use App\Models\Laboratory;
use Illuminate\Database\Eloquent\Factories\Factory;

class BrandFactory extends Factory
{
    protected $model = Brand::class;

    public function definition(): array
    {
        return [
            'laboratory_id' => Laboratory::factory(),
            'name' => fake()->unique()->word().' '.fake()->word(),
            'is_active' => true,
        ];
    }
}
