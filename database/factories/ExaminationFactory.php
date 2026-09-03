<?php

namespace Database\Factories;

use App\Models\Examination;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Examination>
 */
class ExaminationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'patient_id' => Patient::factory(),
            'user_id' => User::factory(),
            'prescription_id' => null,
            'name' => fake()->randomElement([
                'Perfil 20 Completo',
                'Biometría Hemática',
                'Química Sanguínea',
                'Uroanálisis',
                'Ecocardiograma Doppler',
                'Perfil Lipídico',
            ]),
            'type' => Examination::TYPE_LABORATORY,
            'examined_at' => fake()->dateTimeBetween('-30 days', 'now')->format('Y-m-d'),
            'laboratory_name' => 'Laboratorio Clínico San Rafael',
            'findings' => 'Glicemia basal: 92 mg/dL. Colesterol total: 185 mg/dL. Triglicéridos: 120 mg/dL. Parámetros dentro de límites normales.',
            'status' => Examination::STATUS_COMPLETED,
        ];
    }
}
