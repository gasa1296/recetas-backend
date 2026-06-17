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
    Patient::factory()->count(12)->create();

    $response = $this->actingAs(User::factory()->create(), 'sanctum')
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
    Patient::factory()->create(['identification' => '11111111']);
    Patient::factory()->create(['identification' => '22222222']);

    $response = $this->actingAs(User::factory()->create(), 'sanctum')
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

    $response = $this->getJson("/api/patients/{$patient->id}");

    $response->assertStatus(401);
});

test('patients show returns the requested patient', function () {
    $patient = Patient::factory()->create([
        'first_name' => 'Jane',
        'last_name' => 'Smith',
        'identification' => '99999999',
    ]);

    $response = $this->actingAs(User::factory()->create(), 'sanctum')
        ->getJson("/api/patients/{$patient->id}");

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

    $response = $this->putJson("/api/patients/{$patient->id}", [
        'first_name' => 'X',
        'last_name' => 'Y',
        'identification' => '1',
        'gender' => 'M',
    ]);

    $response->assertStatus(401);
});

test('patients update rejects invalid request structure', function () {
    $patient = Patient::factory()->create();

    $response = $this->actingAs(User::factory()->create(), 'sanctum')
        ->putJson("/api/patients/{$patient->id}", [
            'first_name' => null,
        ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['first_name']);
});

test('patients update modifies the patient with valid request structure', function () {
    $patient = Patient::factory()->create([
        'first_name' => 'Old',
    ]);

    $response = $this->actingAs(User::factory()->create(), 'sanctum')
        ->putJson("/api/patients/{$patient->id}", [
            'first_name' => 'New',
            'last_name' => 'Doe',
            'identification' => $patient->identification,
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

test('patients destroy requires authentication', function () {
    $patient = Patient::factory()->create();

    $response = $this->deleteJson("/api/patients/{$patient->id}");

    $response->assertStatus(401);
});

test('patients destroy deletes the patient', function () {
    $patient = Patient::factory()->create();

    $response = $this->actingAs(User::factory()->create(), 'sanctum')
        ->deleteJson("/api/patients/{$patient->id}");

    $response->assertSuccessful()
        ->assertJsonStructure(['success', 'message', 'data']);

    $this->assertSoftDeleted('patients', [
        'id' => $patient->id,
    ]);
});
