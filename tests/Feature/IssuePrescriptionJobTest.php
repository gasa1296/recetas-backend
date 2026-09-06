<?php

use App\Jobs\IssuePrescriptionJob;
use App\Models\Medicament;
use App\Models\Patient;
use App\Models\Prescription;
use App\Models\Room;
use App\Models\Specialty;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(RoleSeeder::class);
    Storage::fake('local');

    $this->doctor = User::factory()->create();
    $this->doctor->assignRole('medic');

    $this->specialty = Specialty::factory()->create(['user_id' => $this->doctor->id]);
    $this->room = Room::factory()->create(['user_id' => $this->doctor->id]);
    $this->patient = Patient::factory()->create(['user_id' => $this->doctor->id]);

    $this->prescription = Prescription::factory()->create([
        'user_id' => $this->doctor->id,
        'patient_id' => $this->patient->id,
        'room_id' => $this->room->id,
        'specialty_id' => $this->specialty->id,
        'status' => config('custom.prescription.status_keys.draft'),
    ]);

    $this->medicament = Medicament::factory()->create(['type' => 'general']);
    $this->prescription->medicaments()->attach($this->medicament->id, [
        'dosage' => '1 cada 8 horas',
        'frequency' => '8h',
        'duration' => '5 días',
        'medicament_quantity' => 1,
        'medicament_quantity_letters' => 'uno',
    ]);
});

it('dispatches IssuePrescriptionJob when async is requested', function () {
    Queue::fake();

    $response = $this->actingAs($this->doctor, 'sanctum')
        ->postJson("/api/prescriptions/{$this->prescription->id}/finish", [
            'async' => true,
            'signature' => base64_encode('test-signature'),
        ]);

    $response->assertStatus(202)
        ->assertJson([
            'success' => true,
        ]);

    Queue::assertPushed(IssuePrescriptionJob::class, function ($job) {
        return $job->prescription->id === $this->prescription->id &&
            $job->doctor->id === $this->doctor->id;
    });
});

it('processes IssuePrescriptionJob and transitions prescription to active', function () {
    $this->doctor->update(['saved_signature' => base64_encode('test-signature')]);

    IssuePrescriptionJob::dispatchSync(
        $this->prescription,
        $this->doctor,
        null,
        false
    );

    $fresh = $this->prescription->fresh();
    expect((int) $fresh->status)->toBe((int) config('custom.prescription.status_keys.active'));
    expect($fresh->expires_at)->not()->toBeNull();
});
