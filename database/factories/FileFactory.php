<?php

namespace Database\Factories;

use App\Models\File;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<File>
 */
class FileFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'model_type' => Patient::class,
            'model_id' => Patient::factory(),
            'type' => 'jpg',
            'category' => File::CATEGORY_RX,
            'title' => 'Radiografía de Tórax AP',
            'description' => 'Estudio de control pulmonar',
            'mime_type' => 'image/jpeg',
            'size' => 204800,
            'meta' => [
                'evolution_stage' => File::STAGE_BEFORE_TREATMENT,
            ],
            'user_id' => User::factory(),
            'location' => 'local',
            'path' => 'patients/1/media/sample_rx.jpg',
            'filename' => 'sample_rx.jpg',
        ];
    }
}
