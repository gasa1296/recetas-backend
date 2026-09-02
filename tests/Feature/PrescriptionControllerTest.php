<?php

use App\Models\Medicament;
use App\Models\Patient;
use App\Models\Prescription;
use App\Models\Room;
use App\Models\Specialty;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

/*
|--------------------------------------------------------------------------
| PrescriptionController
|--------------------------------------------------------------------------
| GET    /api/prescriptions               -> auth:sanctum (SearchRequest)
| POST   /api/prescriptions               -> auth:sanctum (PrescriptionRequest)
| GET    /api/prescriptions/{id}          -> auth:sanctum
| PUT    /api/prescriptions/{id}          -> auth:sanctum (PrescriptionRequest)
| DELETE /api/prescriptions/{id}          -> auth:sanctum
| POST   /api/prescriptions/{id}/finish   -> auth:sanctum
*/

dataset('invalid_prescription_payloads', [
    'missing patient_id' => [
        ['specialty_id' => 1, 'room_id' => 1],
        ['patient_id'],
    ],
    'missing room_id' => [
        ['specialty_id' => 1, 'patient_id' => 1],
        ['room_id'],
    ],
    'missing specialty_id' => [
        ['patient_id' => 1, 'room_id' => 1],
        ['specialty_id'],
    ],
    'non-existent room_id' => [
        ['specialty_id' => 1, 'patient_id' => 1, 'room_id' => 999999],
        ['room_id'],
    ],
    'non-existent patient_id' => [
        ['specialty_id' => 1, 'patient_id' => 999999, 'room_id' => 1],
        ['patient_id'],
    ],
    'non-existent specialty_id' => [
        ['specialty_id' => 999999, 'patient_id' => 1, 'room_id' => 1],
        ['patient_id'],
    ],
    'negative temp' => [
        ['specialty_id' => 1, 'patient_id' => 1, 'room_id' => 1, 'temp' => -1],
        ['temp'],
    ],
    'medicament without dosage' => [
        ['specialty_id' => 1, 'patient_id' => 1, 'room_id' => 1, 'medicaments' => [
            ['id' => 1, 'frequency' => '8h', 'duration' => '5d', 'medicament_quantity' => 1, 'medicament_quantity_letters' => 'one'],
        ]],
        ['medicaments.1.dosage'],
    ],
]);

test('prescriptions index requires authentication', function () {
    $response = $this->getJson('/api/prescriptions');

    $response->assertStatus(401);
});

test('prescriptions index rejects invalid search query', function () {
    $response = $this->actingAs(User::factory()->hasSpecialty()->create(), 'sanctum')
        ->getJson('/api/prescriptions?search='.str_repeat('a', 256));

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['search']);
});

test('prescriptions index returns only the authenticated user prescriptions', function () {
    $user = User::factory()->create();
    $other = User::factory()->create();
    $patient = Patient::factory()->create();
    $room = Room::factory()->for($user)->create();
    $specialty = Specialty::factory()->for($user)->create();
    Prescription::factory()->count(3)
        ->for($patient, 'patient')
        ->for($room, 'room')
        ->for($specialty, 'specialty')
        ->for($user)
        ->create();
    Prescription::factory()->count(2)
        ->for($patient, 'patient')
        ->for($room, 'room')
        ->for($specialty, 'specialty')
        ->for($other)
        ->create();

    $response = $this->actingAs($user, 'sanctum')
        ->getJson('/api/prescriptions');

    $response->assertSuccessful()
        ->assertJsonStructure([
            'message',
            'success',
            'data' => [
                '*' => [
                    'id',
                    'user_id',
                    'patient_id',
                    'room_id',
                    'specialty_id',
                    'temp',
                    'weight',
                    'height',
                    'pressure',
                    'saturation',
                    'ppm',
                    'allergy',
                    'diagnostic',
                    'diet',
                    'comments',
                    'status',
                    'pretty_status',
                ],
            ],
        ])
        ->assertJsonCount(3, 'data');
});

test('prescriptions index filters by description when search is provided', function () {
    $user = User::factory()->create();
    $patient = Patient::factory()->create();
    $room = Room::factory()->for($user)->create();
    $specialty = Specialty::factory()->for($user)->create();
    Prescription::factory()
        ->for($user)
        ->for($patient, 'patient')
        ->for($room, 'room')
        ->for($specialty, 'specialty')
        ->create(['diagnostic' => 'flu']);
    Prescription::factory()
        ->for($user)
        ->for($patient, 'patient')
        ->for($room, 'room')
        ->for($specialty, 'specialty')
        ->create(['diagnostic' => 'migraine']);

    $response = $this->actingAs($user, 'sanctum')
        ->getJson('/api/prescriptions?search=flu');

    $response->assertSuccessful()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.diagnostic', 'flu');
});

test('prescriptions store requires authentication', function () {
    $payload = [
        'patient_id' => 1,
        'room_id' => 1,
        'specialty_id' => 1,
    ];

    $response = $this->postJson('/api/prescriptions', $payload);

    $response->assertStatus(401);
});

test('prescriptions store rejects invalid request structure', function (array $payload, array $errors) {
    $user = User::factory()->create();

    $response = $this->actingAs($user, 'sanctum')
        ->postJson('/api/prescriptions', $payload);

    $response->assertStatus(422)
        ->assertJsonValidationErrors($errors);
})->with('invalid_prescription_payloads');

test('prescriptions store creates a prescription with valid request structure', function () {
    $user = User::factory()->create();
    $patient = Patient::factory()->create();
    $room = Room::factory()->for($user)->create();
    $specialty = Specialty::factory()->for($user)->create();
    $medicament = Medicament::factory()->create();

    $response = $this->actingAs($user, 'sanctum')
        ->postJson('/api/prescriptions', [
            'temp' => 37.0,
            'weight' => 70,
            'height' => 175,
            'pressure' => 120,
            'saturation' => 98,
            'ppm' => 75,
            'allergy' => 'None',
            'diagnostic' => 'Common cold',
            'diet' => 'Normal',
            'comments' => 'Rest',
            'room_id' => $room->id,
            'patient_id' => $patient->id,
            'specialty_id' => $specialty->id,
            'medicaments' => [
                [
                    'id' => $medicament->id,
                    'dosage' => '500mg',
                    'frequency' => '8h',
                    'duration' => '5d',
                    'medicament_quantity' => 10,
                    'medicament_quantity_letters' => 'ten',
                ],
            ],
        ]);
    $response->assertSuccessful()
        ->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'id',
                'user_id',
                'patient_id',
                'room_id',
                'specialty_id',
                'temp',
                'weight',
                'height',
                'pressure',
                'saturation',
                'ppm',
                'allergy',
                'diagnostic',
                'diet',
                'comments',
                'room' => ['id', 'name', 'zip', 'address', 'phone'],
                'specialty' => ['name', 'identification'],
                'patient' => ['id', 'first_name', 'last_name', 'identification', 'email', 'phone', 'birth_date'],
                'medicaments' => [
                    '*' => [
                        'id',
                        'active_ingredient',
                        'type',
                        'group',
                        'dosage',
                        'frequency',
                        'duration',
                        'medicament_quantity',
                        'medicament_quantity_letters',
                    ],
                ],
                'status',
                'pretty_status',
            ],
        ])
        ->assertJsonPath('data.diagnostic', 'Common cold')
        ->assertJsonPath('data.status', config('custom.prescription.status_keys.draft'))
        ->assertJsonPath('data.pretty_status', config('custom.prescription.status.0'))
        ->assertJsonPath('data.user_id', $user->id)
        ->assertJsonPath('data.room.id', $room->id)
        ->assertJsonPath('data.specialty.name', $specialty->name)
        ->assertJsonPath('data.specialty.identification', $specialty->identification)
        ->assertJsonPath('data.patient.id', $patient->id)
        ->assertJsonPath('data.medicaments.0.dosage', '500mg');

    $this->assertDatabaseHas('prescriptions', [
        'user_id' => $user->id,
        'patient_id' => $patient->id,
        'room_id' => $room->id,
        'specialty_id' => $specialty->id,
        'diagnostic' => 'Common cold',
    ]);
});

test('prescriptions show requires authentication', function () {
    $user = User::factory()->create();
    $room = Room::factory()->for($user)->create();
    $specialty = Specialty::factory()->for($user)->create();
    $patient = Patient::factory()->create();
    $prescription = Prescription::factory()
        ->for($user)
        ->for($patient, 'patient')
        ->for($room, 'room')
        ->for($specialty, 'specialty')
        ->create(['status' => config('custom.prescription.status_keys.draft')]);

    $response = $this->getJson('/api/prescriptions/'.$prescription->id);

    $response->assertStatus(401);
});

test('prescriptions show returns 404 for prescription not owned by user', function () {
    $user = User::factory()->create();
    $stranger = User::factory()->create();
    $room = Room::factory()->for($user)->create();
    $specialty = Specialty::factory()->for($user)->create();
    $patient = Patient::factory()->create();
    $prescription = Prescription::factory()
        ->for($user)
        ->for($patient, 'patient')
        ->for($room, 'room')
        ->for($specialty, 'specialty')
        ->create(['status' => config('custom.prescription.status_keys.draft')]);

    $response = $this->actingAs($stranger, 'sanctum')
        ->getJson('/api/prescriptions/'.$prescription->id);

    $response->assertNotFound();
});

test('prescriptions show returns the requested prescription with full relations', function () {
    $user = User::factory()->create();
    $room = Room::factory()->for($user)->create();
    $patient = Patient::factory()->create();
    $specialty = Specialty::factory()->for($user)->create();
    $medicament = Medicament::factory()->create();
    $prescription = Prescription::factory()
        ->for($user)
        ->for($patient, 'patient')
        ->for($room, 'room')
        ->for($specialty, 'specialty')
        ->create(['status' => config('custom.prescription.status_keys.draft')]);
    $prescription->medicaments()->attach($medicament->id, [
        'dosage' => '500mg',
        'frequency' => '8h',
        'duration' => '5d',
        'medicament_quantity' => '10',
        'medicament_quantity_letters' => 'ten',
    ]);

    $response = $this->actingAs($user, 'sanctum')
        ->getJson('/api/prescriptions/'.$prescription->id);

    $response->assertSuccessful()
        ->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'id',
                'user_id',
                'patient_id',
                'room_id',
                'specialty_id',
                'temp',
                'weight',
                'height',
                'pressure',
                'saturation',
                'ppm',
                'allergy',
                'diagnostic',
                'diet',
                'comments',
                'room' => ['id', 'name', 'zip', 'address', 'phone'],
                'specialty' => ['name', 'identification'],
                'patient' => ['id', 'first_name', 'last_name', 'identification', 'email', 'phone', 'birth_date'],
                'medicaments' => [
                    '*' => [
                        'id',
                        'active_ingredient',
                        'type',
                        'group',
                        'dosage',
                        'frequency',
                        'duration',
                        'medicament_quantity',
                        'medicament_quantity_letters',
                    ],
                ],
                'status',
                'pretty_status',
            ],
        ])
        ->assertJsonPath('data.id', $prescription->id)
        ->assertJsonPath('data.user_id', $user->id)
        ->assertJsonPath('data.patient.id', $patient->id)
        ->assertJsonPath('data.specialty.name', $specialty->name)
        ->assertJsonPath('data.specialty.identification', $specialty->identification)
        ->assertJsonPath('data.room.id', $room->id);

});

test('prescriptions update requires authentication', function () {
    $user = User::factory()->create();
    $room = Room::factory()->for($user)->create();
    $patient = Patient::factory()->create();
    $specialty = Specialty::factory()->for($user)->create();
    $prescription = Prescription::factory()
        ->for($user)
        ->for($patient, 'patient')
        ->for($room, 'room')
        ->for($specialty, 'specialty')
        ->create(['status' => config('custom.prescription.status_keys.draft')]);

    $response = $this->putJson('/api/prescriptions/'.$prescription->id, [
        'patient_id' => 1,
        'room_id' => 1,
    ]);

    $response->assertStatus(401);
});

test('prescriptions update rejects invalid request structure', function () {
    $user = User::factory()->create();
    $room = Room::factory()->for($user)->create();
    $patient = Patient::factory()->create();
    $specialty = Specialty::factory()->for($user)->create();
    $prescription = Prescription::factory()
        ->for($user)
        ->for($patient, 'patient')
        ->for($room, 'room')
        ->for($specialty, 'specialty')
        ->create(['status' => config('custom.prescription.status_keys.draft')]);

    $response = $this->actingAs($user, 'sanctum')
        ->putJson('/api/prescriptions/'.$prescription->id, [
            'patient_id' => null,
        ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['patient_id']);
});

test('prescriptions update only allows editing draft prescriptions', function () {
    $user = User::factory()->create();
    $room = Room::factory()->for($user)->create();
    $patient = Patient::factory()->create();
    $specialty = Specialty::factory()->for($user)->create();
    $prescription = Prescription::factory()
        ->for($user)
        ->for($patient, 'patient')
        ->for($room, 'room')
        ->for($specialty, 'specialty')
        ->create(['status' => config('custom.prescription.status_keys.active')]);

    $response = $this->actingAs($user, 'sanctum')
        ->putJson('/api/prescriptions/'.$prescription->id, [
            'patient_id' => $patient->id,
            'room_id' => $room->id,
            'specialty_id' => $specialty->id,
        ]);

    $response->assertNotFound();
});

test('prescriptions update modifies the prescription with valid request structure', function () {
    $user = User::factory()->create();
    $room = Room::factory()->for($user)->create();
    $patient = Patient::factory()->create();
    $specialty = Specialty::factory()->for($user)->create();
    $prescription = Prescription::factory()
        ->for($user)
        ->for($patient, 'patient')
        ->for($room, 'room')
        ->for($specialty, 'specialty')
        ->create(['status' => config('custom.prescription.status_keys.draft')]);

    $response = $this->actingAs($user, 'sanctum')
        ->putJson('/api/prescriptions/'.$prescription->id, [
            'patient_id' => $patient->id,
            'room_id' => $room->id,
            'specialty_id' => $specialty->id,
            'diagnostic' => 'Updated',
        ]);

    $response->assertSuccessful()
        ->assertJsonPath('data.id', $prescription->id)
        ->assertJsonPath('data.diagnostic', 'Updated');

    $this->assertDatabaseHas('prescriptions', [
        'id' => $prescription->id,
        'diagnostic' => 'Updated',
    ]);
});

test('prescriptions destroy requires authentication', function () {
    $user = User::factory()->create();
    $room = Room::factory()->for($user)->create();
    $specialty = Specialty::factory()->for($user)->create();
    $patient = Patient::factory()->create();
    $prescription = Prescription::factory()
        ->for($user)
        ->for($patient, 'patient')
        ->for($room, 'room')
        ->for($specialty, 'specialty')
        ->create(['status' => config('custom.prescription.status_keys.draft')]);

    $response = $this->deleteJson('/api/prescriptions/'.$prescription->id);

    $response->assertStatus(401);
});

test('prescriptions destroy only allows removing draft prescriptions', function () {
    $user = User::factory()->create();
    $room = Room::factory()->for($user)->create();
    $patient = Patient::factory()->create();
    $specialty = Specialty::factory()->for($user)->create();
    $prescription = Prescription::factory()
        ->for($user)
        ->for($patient, 'patient')
        ->for($room, 'room')
        ->for($specialty, 'specialty')
        ->create(['status' => config('custom.prescription.status_keys.active')]);

    $response = $this->actingAs($user, 'sanctum')
        ->deleteJson('/api/prescriptions/'.$prescription->id);

    $response->assertNotFound();
});

test('prescriptions destroy soft deletes a draft prescription', function () {
    $user = User::factory()->create();
    $room = Room::factory()->for($user)->create();
    $specialty = Specialty::factory()->for($user)->create();
    $patient = Patient::factory()->create();
    $prescription = Prescription::factory()
        ->for($user)
        ->for($patient, 'patient')
        ->for($room, 'room')
        ->for($specialty, 'specialty')
        ->create(['status' => config('custom.prescription.status_keys.draft')]);

    $response = $this->actingAs($user, 'sanctum')
        ->deleteJson('/api/prescriptions/'.$prescription->id);

    $response->assertSuccessful()
        ->assertJsonStructure(['success', 'message', 'data']);

    $this->assertSoftDeleted('prescriptions', [
        'id' => $prescription->id,
    ]);
});

test('prescriptions finish requires authentication', function () {
    $response = $this->postJson('/api/prescriptions/1/finish', [
        'signature' => base64_encode('test'),
    ]);

    $response->assertStatus(401);
});

test('prescriptions finish rejects missing signature', function () {
    $user = User::factory()->create();
    $room = Room::factory()->for($user)->create();
    $patient = Patient::factory()->create();
    $specialty = Specialty::factory()->for($user)->create();
    $prescription = Prescription::factory()
        ->for($user)
        ->for($patient, 'patient')
        ->for($room, 'room')
        ->for($specialty, 'specialty')
        ->create(['status' => config('custom.prescription.status_keys.draft')]);

    $response = $this->actingAs($user, 'sanctum')
        ->postJson('/api/prescriptions/'.$prescription->id.'/finish', []);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['signature']);
});

test('prescriptions finish only allows drafting prescriptions', function () {
    $user = User::factory()->create();
    $room = Room::factory()->for($user)->create();
    $patient = Patient::factory()->create();
    $specialty = Specialty::factory()->for($user)->create();
    $prescription = Prescription::factory()
        ->for($user)
        ->for($patient, 'patient')
        ->for($room, 'room')
        ->for($specialty, 'specialty')
        ->create(['status' => config('custom.prescription.status_keys.active')]);

    $response = $this->actingAs($user, 'sanctum')
        ->postJson('/api/prescriptions/'.$prescription->id.'/finish', [
            'signature' => base64_encode('test'),
        ]);

    $response->assertNotFound();
});

test('prescriptions finish generates signed pdf and activates prescription', function () {
    $user = User::factory()->create();
    $room = Room::factory()->for($user)->create();
    $patient = Patient::factory()->create();
    $specialty = Specialty::factory()->for($user)->create();
    $prescription = Prescription::factory()
        ->for($user)
        ->for($patient, 'patient')
        ->for($room, 'room')
        ->for($specialty, 'specialty')
        ->create(['status' => config('custom.prescription.status_keys.draft')]);

    $response = $this->actingAs($user, 'sanctum')
        ->postJson('/api/prescriptions/'.$prescription->id.'/finish', [
            'signature' => base64_encode('test'),
        ]);

    $response->assertSuccessful()
        ->assertJsonStructure(['success', 'message']);

    $this->assertDatabaseHas('prescriptions', [
        'id' => $prescription->id,
        'status' => config('custom.prescription.status_keys.active'),
    ]);

    $this->assertDatabaseHas('files', [
        'model_id' => $prescription->id,
        'model_type' => Prescription::class,
        'type' => 'signed',
    ]);
});

test('prescriptions finish uses default certificate when user has no certificate', function () {
    $user = User::factory()->create([
        'certificate_path' => null,
        'certificate_key_path' => null,
    ]);
    $room = Room::factory()->for($user)->create();
    $patient = Patient::factory()->create();
    $specialty = Specialty::factory()->for($user)->create();
    $prescription = Prescription::factory()
        ->for($user)
        ->for($patient, 'patient')
        ->for($room, 'room')
        ->for($specialty, 'specialty')
        ->create(['status' => config('custom.prescription.status_keys.draft')]);

    $response = $this->actingAs($user, 'sanctum')
        ->postJson('/api/prescriptions/'.$prescription->id.'/finish', [
            'signature' => base64_encode('test'),
        ]);

    $response->assertSuccessful()
        ->assertJsonStructure(['success', 'message']);

    $this->assertDatabaseHas('prescriptions', [
        'id' => $prescription->id,
        'status' => config('custom.prescription.status_keys.active'),
    ]);
});

test('user has valid certificate returns true when certificate exists and is not expired', function () {
    $user = User::factory()->create([
        'certificate_path' => 'test-cert.pem',
        'certificate_key_path' => 'test-key.pem',
        'certificate_expires_at' => now()->addYear(),
    ]);

    // Mock storage to return true for file existence
    Storage::fake('local');
    Storage::disk('local')->put('test-cert.pem', 'test-cert-content');
    Storage::disk('local')->put('test-key.pem', 'test-key-content');

    expect($user->hasValidCertificate())->toBeTrue();
});

test('user has valid certificate returns false when certificate is expired', function () {
    $user = User::factory()->create([
        'certificate_path' => 'test-cert.pem',
        'certificate_key_path' => 'test-key.pem',
        'certificate_expires_at' => now()->subYear(),
    ]);

    Storage::fake('local');
    Storage::disk('local')->put('test-cert.pem', 'test-cert-content');
    Storage::disk('local')->put('test-key.pem', 'test-key-content');

    expect($user->hasValidCertificate())->toBeFalse();
});

test('user has valid certificate returns false when certificate paths are null', function () {
    $user = User::factory()->create([
        'certificate_path' => null,
        'certificate_key_path' => null,
    ]);

    expect($user->hasValidCertificate())->toBeFalse();
});
