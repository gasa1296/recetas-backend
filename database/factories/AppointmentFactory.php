<?php

namespace Database\Factories;

use App\Models\Appointment;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Appointment>
 */
class AppointmentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $startsAt = fake()->dateTimeBetween('now', '+1 month');
        $endsAt = (clone $startsAt)->modify('+30 minutes');

        return [
            'user_id' => User::factory(),
            'patient_id' => Patient::factory(),
            'room_id' => null,
            'specialty_id' => null,
            'starts_at' => $startsAt,
            'ends_at' => $endsAt,
            'reason' => fake()->sentence(3),
            'status' => Appointment::STATUS_SCHEDULED,
            'notes' => fake()->paragraph(),
            'reminder_channel' => 'email',
            'reminder_enabled' => true,
            'reminder_sent_at' => null,
        ];
    }
}
