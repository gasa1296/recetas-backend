<?php

use App\Jobs\SendAppointmentRemindersJob;
use App\Models\Appointment;
use App\Models\Patient;
use App\Models\Room;
use App\Models\Specialty;
use App\Models\User;
use App\Notifications\AppointmentReminderNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;

uses(RefreshDatabase::class);

/*
|--------------------------------------------------------------------------
| AppointmentController Tests
|--------------------------------------------------------------------------
*/

it('requires authentication to access appointments endpoints', function () {
    $this->getJson('/api/appointments')->assertUnauthorized();
    $this->postJson('/api/appointments', [])->assertUnauthorized();
    $this->getJson('/api/appointments/1')->assertUnauthorized();
    $this->putJson('/api/appointments/1', [])->assertUnauthorized();
    $this->deleteJson('/api/appointments/1')->assertUnauthorized();
    $this->postJson('/api/appointments/1/status', [])->assertUnauthorized();
});

it('lists only the appointments belonging to the authenticated doctor', function () {
    $doctor1 = User::factory()->create();
    $doctor2 = User::factory()->create();

    $patient1 = Patient::factory()->create();
    $patient2 = Patient::factory()->create();

    Appointment::factory()->count(3)->create([
        'user_id' => $doctor1->id,
        'patient_id' => $patient1->id,
    ]);

    Appointment::factory()->count(2)->create([
        'user_id' => $doctor2->id,
        'patient_id' => $patient2->id,
    ]);

    $response = $this->actingAs($doctor1, 'sanctum')
        ->getJson('/api/appointments')
        ->assertOk()
        ->assertJsonStructure([
            'data' => [
                '*' => ['id', 'user_id', 'patient_id', 'starts_at', 'ends_at', 'status'],
            ],
        ]);

    expect($response->json('data'))->toHaveCount(3);
    foreach ($response->json('data') as $item) {
        expect($item['user_id'])->toBe($doctor1->id);
    }
});

it('filters appointments by date range and status', function () {
    $doctor = User::factory()->create();
    $patient = Patient::factory()->for($doctor)->create();

    // Appointment tomorrow
    Appointment::factory()->create([
        'user_id' => $doctor->id,
        'patient_id' => $patient->id,
        'starts_at' => now()->addDay()->setTime(10, 0),
        'ends_at' => now()->addDay()->setTime(10, 30),
        'status' => Appointment::STATUS_SCHEDULED,
    ]);

    // Appointment next week
    Appointment::factory()->create([
        'user_id' => $doctor->id,
        'patient_id' => $patient->id,
        'starts_at' => now()->addDays(7)->setTime(10, 0),
        'ends_at' => now()->addDays(7)->setTime(10, 30),
        'status' => Appointment::STATUS_CONFIRMED,
    ]);

    // Filter range matching only tomorrow
    $from = now()->addDay()->startOfDay()->toIso8601String();
    $to = now()->addDay()->endOfDay()->toIso8601String();

    $response = $this->actingAs($doctor, 'sanctum')
        ->getJson("/api/appointments?from={$from}&to={$to}")
        ->assertOk();

    expect($response->json('data'))->toHaveCount(1);
    expect($response->json('data.0.status'))->toBe(Appointment::STATUS_SCHEDULED);
});

it('allows a doctor to create an appointment', function () {
    $doctor = User::factory()->create();
    $patient = Patient::factory()->for($doctor)->create();
    $room = Room::factory()->create(['user_id' => $doctor->id]);
    $specialty = Specialty::factory()->create(['user_id' => $doctor->id]);

    $payload = [
        'patient_id' => $patient->id,
        'room_id' => $room->id,
        'specialty_id' => $specialty->id,
        'starts_at' => now()->addDays(2)->setTime(9, 0)->toIso8601String(),
        'ends_at' => now()->addDays(2)->setTime(9, 30)->toIso8601String(),
        'reason' => 'Consulta de control general',
        'notes' => 'Paciente refiere dolor de cabeza leve',
        'reminder_channel' => 'email',
        'reminder_enabled' => true,
    ];

    $response = $this->actingAs($doctor, 'sanctum')
        ->postJson('/api/appointments', $payload)
        ->assertCreated()
        ->assertJsonPath('data.reason', 'Consulta de control general')
        ->assertJsonPath('data.status', Appointment::STATUS_SCHEDULED)
        ->assertJsonPath('data.patient.id', $patient->id);

    $this->assertDatabaseHas('appointments', [
        'user_id' => $doctor->id,
        'patient_id' => $patient->id,
        'room_id' => $room->id,
        'reason' => 'Consulta de control general',
    ]);
});

it('rejects creation when the doctor has an overlapping active appointment', function () {
    $doctor = User::factory()->create();
    $patient1 = Patient::factory()->for($doctor)->create();
    $patient2 = Patient::factory()->for($doctor)->create();

    // Existing appointment: 10:00 to 11:00
    Appointment::factory()->create([
        'user_id' => $doctor->id,
        'patient_id' => $patient1->id,
        'starts_at' => now()->addDays(3)->setTime(10, 0),
        'ends_at' => now()->addDays(3)->setTime(11, 0),
        'status' => Appointment::STATUS_SCHEDULED,
    ]);

    // Overlapping attempt: 10:30 to 11:30
    $payload = [
        'patient_id' => $patient2->id,
        'starts_at' => now()->addDays(3)->setTime(10, 30)->toIso8601String(),
        'ends_at' => now()->addDays(3)->setTime(11, 30)->toIso8601String(),
        'reason' => 'Segunda cita en conflicto',
    ];

    $this->actingAs($doctor, 'sanctum')
        ->postJson('/api/appointments', $payload)
        ->assertStatus(422)
        ->assertJsonValidationErrors(['starts_at']);
});

it('rejects creation when the room is booked by another appointment', function () {
    $doctor1 = User::factory()->create();
    $doctor2 = User::factory()->create();
    $room = Room::factory()->create(['user_id' => $doctor1->id]);
    $patient1 = Patient::factory()->for($doctor1)->create();
    $patient2 = Patient::factory()->for($doctor2)->create();

    // Doctor 1 books the room from 14:00 to 15:00
    Appointment::factory()->create([
        'user_id' => $doctor1->id,
        'room_id' => $room->id,
        'patient_id' => $patient1->id,
        'starts_at' => now()->addDays(4)->setTime(14, 0),
        'ends_at' => now()->addDays(4)->setTime(15, 0),
        'status' => Appointment::STATUS_SCHEDULED,
    ]);

    // Doctor 2 tries to book the same room at 14:15 to 14:45
    $payload = [
        'patient_id' => $patient2->id,
        'room_id' => $room->id,
        'starts_at' => now()->addDays(4)->setTime(14, 15)->toIso8601String(),
        'ends_at' => now()->addDays(4)->setTime(14, 45)->toIso8601String(),
        'reason' => 'Conflicto de consultorio',
    ];

    $this->actingAs($doctor2, 'sanctum')
        ->postJson('/api/appointments', $payload)
        ->assertStatus(422)
        ->assertJsonValidationErrors(['room_id']);
});

it('allows overlapping if the previous appointment was cancelled', function () {
    $doctor = User::factory()->create();
    $patient1 = Patient::factory()->for($doctor)->create();
    $patient2 = Patient::factory()->for($doctor)->create();

    // Cancelled appointment at 16:00 to 17:00
    Appointment::factory()->create([
        'user_id' => $doctor->id,
        'patient_id' => $patient1->id,
        'starts_at' => now()->addDays(5)->setTime(16, 0),
        'ends_at' => now()->addDays(5)->setTime(17, 0),
        'status' => Appointment::STATUS_CANCELLED,
    ]);

    // New booking at same time
    $payload = [
        'patient_id' => $patient2->id,
        'starts_at' => now()->addDays(5)->setTime(16, 0)->toIso8601String(),
        'ends_at' => now()->addDays(5)->setTime(17, 0)->toIso8601String(),
        'reason' => 'Nueva cita en horario liberado',
    ];

    $this->actingAs($doctor, 'sanctum')
        ->postJson('/api/appointments', $payload)
        ->assertCreated();
});

it('allows updating and rescheduling an appointment', function () {
    $doctor = User::factory()->create();
    $patient = Patient::factory()->for($doctor)->create();

    $appointment = Appointment::factory()->create([
        'user_id' => $doctor->id,
        'patient_id' => $patient->id,
        'starts_at' => now()->addDays(1)->setTime(10, 0),
        'ends_at' => now()->addDays(1)->setTime(10, 30),
    ]);

    $updatePayload = [
        'patient_id' => $patient->id,
        'starts_at' => now()->addDays(2)->setTime(11, 0)->toIso8601String(),
        'ends_at' => now()->addDays(2)->setTime(11, 30)->toIso8601String(),
        'reason' => 'Reprogramada por solicitud del paciente',
        'notes' => 'Nueva fecha confirmada',
    ];

    $this->actingAs($doctor, 'sanctum')
        ->putJson("/api/appointments/{$appointment->id}", $updatePayload)
        ->assertOk()
        ->assertJsonPath('data.reason', 'Reprogramada por solicitud del paciente');

    $this->assertDatabaseHas('appointments', [
        'id' => $appointment->id,
        'reason' => 'Reprogramada por solicitud del paciente',
    ]);
});

it('allows transitioning appointment status through the workflow', function () {
    $doctor = User::factory()->create();
    $appointment = Appointment::factory()->create([
        'user_id' => $doctor->id,
        'status' => Appointment::STATUS_SCHEDULED,
    ]);

    // Transition to confirmed
    $this->actingAs($doctor, 'sanctum')
        ->postJson("/api/appointments/{$appointment->id}/status", ['status' => Appointment::STATUS_CONFIRMED])
        ->assertOk()
        ->assertJsonPath('data.status', Appointment::STATUS_CONFIRMED);

    // Transition to in_waiting_room
    $this->actingAs($doctor, 'sanctum')
        ->postJson("/api/appointments/{$appointment->id}/status", ['status' => Appointment::STATUS_IN_WAITING_ROOM])
        ->assertOk()
        ->assertJsonPath('data.status', Appointment::STATUS_IN_WAITING_ROOM);

    // Transition to in_consultation
    $this->actingAs($doctor, 'sanctum')
        ->postJson("/api/appointments/{$appointment->id}/status", ['status' => Appointment::STATUS_IN_CONSULTATION])
        ->assertOk()
        ->assertJsonPath('data.status', Appointment::STATUS_IN_CONSULTATION);

    // Transition to completed
    $this->actingAs($doctor, 'sanctum')
        ->postJson("/api/appointments/{$appointment->id}/status", ['status' => Appointment::STATUS_COMPLETED])
        ->assertOk()
        ->assertJsonPath('data.status', Appointment::STATUS_COMPLETED);
});

it('allows cancelling an appointment via delete', function () {
    $doctor = User::factory()->create();
    $appointment = Appointment::factory()->create([
        'user_id' => $doctor->id,
    ]);

    $this->actingAs($doctor, 'sanctum')
        ->deleteJson("/api/appointments/{$appointment->id}")
        ->assertOk();

    // Soft deleted and status set to cancelled
    $this->assertSoftDeleted('appointments', [
        'id' => $appointment->id,
        'status' => Appointment::STATUS_CANCELLED,
    ]);
});

it('sends automated reminders to patients with upcoming appointments', function () {
    Notification::fake();

    $doctor = User::factory()->create();
    $patient = Patient::factory()->create(['email' => 'paciente@ejemplo.com']);

    // Appointment in 12 hours (within 24h window)
    $appointment = Appointment::factory()->create([
        'user_id' => $doctor->id,
        'patient_id' => $patient->id,
        'starts_at' => now()->addHours(12),
        'ends_at' => now()->addHours(13),
        'status' => Appointment::STATUS_SCHEDULED,
        'reminder_enabled' => true,
        'reminder_sent_at' => null,
    ]);

    // Execute job
    $job = new SendAppointmentRemindersJob;
    $count = $job->handle();

    expect($count)->toBe(1);

    Notification::assertSentTo(
        $patient,
        AppointmentReminderNotification::class,
        function ($notification) use ($appointment) {
            return $notification->appointment->id === $appointment->id;
        }
    );

    expect($appointment->fresh()->reminder_sent_at)->not->toBeNull();

    // Running again should not re-send
    $countAgain = $job->handle();
    expect($countAgain)->toBe(0);
});
