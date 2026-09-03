<?php

use App\Models\Patient;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

/*
|--------------------------------------------------------------------------
| PatientController
|--------------------------------------------------------------------------
| GET    /api/patients            -> auth:sanctum (SearchRequest)
| POST   /api/patients            -> auth:sanctum (PatientRequest)
| GET    /api/patients/{id}       -> auth:sanctum
| PUT    /api/patients/{id}       -> auth:sanctum (PatientRequest)
| DELETE /api/patients/{id}       -> auth:sanctum
*/

dataset('invalid_patient_payloads', [
    'missing first_name' => [
        ['last_name' => 'Doe', 'identification' => '123', 'gender' => 'M'],
        ['first_name'],
    ],
    'missing last_name' => [
        ['first_name' => 'John', 'identification' => '123', 'gender' => 'M'],
        ['last_name'],
    ],
    'missing identification' => [
        ['first_name' => 'John', 'last_name' => 'Doe', 'gender' => 'M'],
        ['identification'],
    ],
    'missing gender' => [
        ['first_name' => 'John', 'last_name' => 'Doe', 'identification' => '123'],
        ['gender'],
    ],
    'invalid gender' => [
        ['first_name' => 'John', 'last_name' => 'Doe', 'identification' => '123', 'gender' => 'X'],
        ['gender'],
    ],
    'invalid email' => [
        ['first_name' => 'John', 'last_name' => 'Doe', 'identification' => '123', 'gender' => 'M', 'email' => 'not-email'],
        ['email'],
    ],
    'phone not array' => [
        ['first_name' => 'John', 'last_name' => 'Doe', 'identification' => '123', 'gender' => 'M', 'phone' => 'string'],
        ['phone'],
    ],
]);

test('patients index requires authentication', function () {
    $response = $this->getJson('/api/patients');

    $response->assertStatus(401);
});

test('patients index rejects invalid search query', function () {
    $response = $this->actingAs(User::factory()->create(), 'sanctum')
        ->getJson('/api/patients?search='.str_repeat('a', 256));

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['search']);
});

test('patients index returns paginated collection with correct structure', function () {
    $user = User::factory()->create();
    Patient::factory()->for($user)->count(12)->create();

    $response = $this->actingAs($user, 'sanctum')
        ->getJson('/api/patients');

    $response->assertSuccessful()
        ->assertJsonStructure([
            'message',
            'success',
            'data' => [
                '*' => [
                    'id',
                    'first_name',
                    'last_name',
                    'identification',
                    'email',
                    'phone',
                    'birth_date',
                ],
            ],
        ])
        ->assertJsonCount(10, 'data');
});

test('patients index filters by identification when search is provided', function () {
    $user = User::factory()->create();
    Patient::factory()->for($user)->create(['identification' => '11111111']);
    Patient::factory()->for($user)->create(['identification' => '22222222']);

    $response = $this->actingAs($user, 'sanctum')
        ->getJson('/api/patients?search=1111');

    $response->assertSuccessful()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.identification', '11111111');
});

test('patients store requires authentication and authorized user', function () {
    $payload = [
        'first_name' => 'John',
        'last_name' => 'Doe',
        'identification' => '12345678',
        'gender' => 'M',
    ];

    $response = $this->postJson('/api/patients', $payload);

    $response->assertStatus(401);
});

test('patients store rejects invalid request structure', function (array $payload, array $errors) {
    $response = $this->actingAs(User::factory()->create(), 'sanctum')
        ->postJson('/api/patients', $payload);

    $response->assertStatus(422)
        ->assertJsonValidationErrors($errors);
})->with('invalid_patient_payloads');

test('patients store creates a patient with valid request structure', function () {
    $user = User::factory()->create();
    $payload = [
        'first_name' => 'John',
        'last_name' => 'Doe',
        'identification' => '12345678',
        'email' => 'john@example.com',
        'phone' => ['+123456789'],
        'gender' => 'M',
        'birth_date' => '1990-01-01',
    ];

    $response = $this->actingAs($user, 'sanctum')
        ->postJson('/api/patients', $payload);

    $response->assertSuccessful()
        ->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'id',
                'first_name',
                'last_name',
                'identification',
                'email',
                'phone',
                'birth_date',
            ],
        ])
        ->assertJsonPath('data.first_name', 'John')
        ->assertJsonPath('data.last_name', 'Doe')
        ->assertJsonPath('data.identification', '12345678')
        ->assertJsonPath('data.email', 'john@example.com')
        ->assertJsonPath('data.gender', 'M');

    $this->assertDatabaseHas('patients', [
        'identification' => '12345678',
        'email' => 'john@example.com',
    ]);
});

test('patients show requires authentication', function () {
    $patient = Patient::factory()->create();

    $response = $this->getJson('/api/patients/'.$patient->id);

    $response->assertStatus(401);
});

test('patients show returns the requested patient', function () {
    $user = User::factory()->create();
    $patient = Patient::factory()->for($user)->create([
        'first_name' => 'Jane',
        'last_name' => 'Smith',
        'identification' => '99999999',
    ]);

    $response = $this->actingAs($user, 'sanctum')
        ->getJson('/api/patients/'.$patient->id);

    $response->assertSuccessful()
        ->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'id',
                'first_name',
                'last_name',
                'identification',
                'email',
                'phone',
                'birth_date',
            ],
        ])
        ->assertJsonPath('data.id', $patient->id)
        ->assertJsonPath('data.first_name', 'Jane')
        ->assertJsonPath('data.identification', '99999999');
});

test('patients show returns 404 for missing patient', function () {
    $response = $this->actingAs(User::factory()->create(), 'sanctum')
        ->getJson('/api/patients/999999');

    $response->assertNotFound();
});

test('patients update requires authentication', function () {
    $patient = Patient::factory()->create();

    $response = $this->putJson('/api/patients/'.$patient->id, [
        'first_name' => 'X',
        'last_name' => 'Y',
        'identification' => '1',
        'gender' => 'M',
    ]);

    $response->assertStatus(401);
});

test('patients update rejects invalid request structure', function () {
    $user = User::factory()->create();
    $patient = Patient::factory()->for($user)->create();

    $response = $this->actingAs($user, 'sanctum')
        ->putJson('/api/patients/'.$patient->id, [
            'first_name' => null,
        ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['first_name']);
});

test('patients update modifies the patient with valid request structure', function () {
    $user = User::factory()->create();
    $patient = Patient::factory()->for($user)->create([
        'first_name' => 'Old',
    ]);

    $response = $this->actingAs($user, 'sanctum')
        ->putJson('/api/patients/'.$patient->id, [
            'first_name' => 'New',
            'last_name' => 'Doe',
            'identification' => (string) $patient->identification,
            'gender' => 'M',
        ]);

    $response->assertSuccessful()
        ->assertJsonPath('data.id', $patient->id)
        ->assertJsonPath('data.first_name', 'New');

    $this->assertDatabaseHas('patients', [
        'id' => $patient->id,
        'first_name' => 'New',
    ]);
});

test('medic cannot view, update, or delete another medic patient', function () {
    $doctor1 = User::factory()->create();
    $doctor2 = User::factory()->create();
    $patient1 = Patient::factory()->for($doctor1)->create();

    // Doctor 2 cannot view Doctor 1's patient in list
    $this->actingAs($doctor2, 'sanctum')
        ->getJson('/api/patients')
        ->assertSuccessful()
        ->assertJsonCount(0, 'data');

    // Doctor 2 cannot view Doctor 1's patient in show
    $this->actingAs($doctor2, 'sanctum')
        ->getJson('/api/patients/'.$patient1->id)
        ->assertNotFound();

    // Doctor 2 cannot update Doctor 1's patient
    $this->actingAs($doctor2, 'sanctum')
        ->putJson('/api/patients/'.$patient1->id, [
            'first_name' => 'Hacked',
            'last_name' => 'Doe',
            'identification' => '123',
            'gender' => 'M',
        ])
        ->assertNotFound();

    // Doctor 2 cannot delete Doctor 1's patient
    $this->actingAs($doctor2, 'sanctum')
        ->deleteJson('/api/patients/'.$patient1->id)
        ->assertNotFound();
});

test('multiple medics can register patients with the same identification', function () {
    $doctor1 = User::factory()->create();
    $doctor2 = User::factory()->create();
    $sharedId = 'V-88888888';

    // Doctor 1 registers patient with sharedId
    $res1 = $this->actingAs($doctor1, 'sanctum')->postJson('/api/patients', [
        'first_name' => 'Carlos',
        'last_name' => 'Perez',
        'identification' => $sharedId,
        'gender' => 'M',
        'birth_date' => '1990-01-01',
    ]);
    $res1->assertSuccessful();

    // Doctor 2 registers patient with the same sharedId without error
    $res2 = $this->actingAs($doctor2, 'sanctum')->postJson('/api/patients', [
        'first_name' => 'Carlos',
        'last_name' => 'Perez Gomez',
        'identification' => $sharedId,
        'gender' => 'M',
        'birth_date' => '1990-01-01',
    ]);
    $res2->assertSuccessful();

    $this->assertDatabaseCount('patients', 2);
});

test('same medic cannot register two patients with the same identification', function () {
    $doctor = User::factory()->create();
    $duplicateId = 'V-99999999';

    $this->actingAs($doctor, 'sanctum')->postJson('/api/patients', [
        'first_name' => 'Ana',
        'last_name' => 'Lopez',
        'identification' => $duplicateId,
        'gender' => 'F',
        'birth_date' => '1995-05-15',
    ])->assertSuccessful();

    // Re-registering with same doctor fails validation
    $this->actingAs($doctor, 'sanctum')->postJson('/api/patients', [
        'first_name' => 'Ana Maria',
        'last_name' => 'Lopez',
        'identification' => $duplicateId,
        'gender' => 'F',
        'birth_date' => '1995-05-15',
    ])->assertStatus(422)
      ->assertJsonValidationErrors(['identification']);
});
