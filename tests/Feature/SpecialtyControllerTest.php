<?php

use App\Models\Specialty;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

/*
|--------------------------------------------------------------------------
| SpecialtyController
|--------------------------------------------------------------------------
| GET    /api/specialties        -> auth:sanctum
| POST   /api/specialties        -> auth:sanctum (SpecialtyRequest)
| GET    /api/specialties/{id}   -> auth:sanctum
| PUT    /api/specialties/{id}   -> auth:sanctum (SpecialtyRequest)
| DELETE /api/specialties/{id}   -> auth:sanctum
*/

dataset('invalid_specialty_payloads', [
    'missing name' => [
        ['identification' => 'ABC-12345'],
        ['name'],
    ],
    'missing identification' => [
        ['name' => 'Cardiology'],
        ['identification'],
    ],
    'name too long' => [
        ['name' => str_repeat('a', 256), 'identification' => 'ABC-12345'],
        ['name'],
    ],
    'identification too long' => [
        ['name' => 'Cardiology', 'identification' => str_repeat('a', 256)],
        ['identification'],
    ],
]);

test('specialties index requires authentication', function () {
    $response = $this->getJson('/api/specialties');

    $response->assertStatus(401);
});

test('specialties index returns only the authenticated user specialties', function () {
    $user = User::factory()->create();
    $other = User::factory()->create();
    Specialty::factory()->count(3)->for($user)->create();
    Specialty::factory()->count(2)->for($other)->create();

    $response = $this->actingAs($user, 'sanctum')
        ->getJson('/api/specialties');

    $response->assertSuccessful()
        ->assertJsonStructure([
            'success',
            'message',
            'data' => [
                '*' => ['id', 'name', 'identification'],
            ],
        ])
        ->assertJsonCount(3, 'data');
});

test('specialties store requires authentication', function () {
    $payload = [
        'name' => 'Cardiology',
        'identification' => 'CARD-12345',
    ];

    $response = $this->postJson('/api/specialties', $payload);

    $response->assertStatus(401);
});

test('specialties store rejects invalid request structure', function (array $payload, array $errors) {
    $response = $this->actingAs(User::factory()->create(), 'sanctum')
        ->postJson('/api/specialties', $payload);

    $response->assertStatus(422)
        ->assertJsonValidationErrors($errors);
})->with('invalid_specialty_payloads');

test('specialties store creates a specialty with valid request structure', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user, 'sanctum')
        ->postJson('/api/specialties', [
            'name' => 'Cardiology',
            'identification' => 'CARD-12345',
        ]);

    $response->assertSuccessful()
        ->assertJsonStructure([
            'success',
            'message',
            'data' => ['id', 'name', 'identification'],
        ])
        ->assertJsonPath('data.name', 'Cardiology')
        ->assertJsonPath('data.identification', 'CARD-12345');

    $this->assertDatabaseHas('specialties', [
        'user_id' => $user->id,
        'name' => 'Cardiology',
        'identification' => 'CARD-12345',
    ]);
});

test('specialties show requires authentication', function () {
    $user = User::factory()->create();
    $specialty = Specialty::factory()->for($user)->create();

    $response = $this->getJson("/api/specialties/{$specialty->id}");

    $response->assertStatus(401);
});

test('specialties show returns 404 for specialty not owned by user', function () {
    $owner = User::factory()->create();
    $stranger = User::factory()->create();
    $specialty = Specialty::factory()->for($owner)->create();

    $response = $this->actingAs($stranger, 'sanctum')
        ->getJson("/api/specialties/{$specialty->id}");

    $response->assertNotFound();
});

test('specialties show returns the requested specialty', function () {
    $user = User::factory()->create();
    $specialty = Specialty::factory()->for($user)->create([
        'name' => 'Cardiology',
        'identification' => 'CARD-12345',
    ]);

    $response = $this->actingAs($user, 'sanctum')
        ->getJson("/api/specialties/{$specialty->id}");

    $response->assertSuccessful()
        ->assertJsonStructure([
            'success',
            'message',
            'data' => ['id', 'name', 'identification'],
        ])
        ->assertJsonPath('data.id', $specialty->id)
        ->assertJsonPath('data.name', 'Cardiology')
        ->assertJsonPath('data.identification', 'CARD-12345');
});

test('specialties update requires authentication', function () {
    $user = User::factory()->create();
    $specialty = Specialty::factory()->for($user)->create();

    $response = $this->putJson("/api/specialties/{$specialty->id}", [
        'name' => 'X',
        'identification' => 'Y',
    ]);

    $response->assertStatus(401);
});

test('specialties update rejects invalid request structure', function () {
    $user = User::factory()->create();
    $specialty = Specialty::factory()->for($user)->create();

    $response = $this->actingAs($user, 'sanctum')
        ->putJson("/api/specialties/{$specialty->id}", [
            'name' => null,
        ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['name']);
});

test('specialties update modifies the specialty with valid request structure', function () {
    $user = User::factory()->create();
    $specialty = Specialty::factory()->for($user)->create(['name' => 'Old']);

    $response = $this->actingAs($user, 'sanctum')
        ->putJson("/api/specialties/{$specialty->id}", [
            'name' => 'New',
            'identification' => 'NEW-67890',
        ]);

    $response->assertSuccessful()
        ->assertJsonPath('data.id', $specialty->id)
        ->assertJsonPath('data.name', 'New')
        ->assertJsonPath('data.identification', 'NEW-67890');

    $this->assertDatabaseHas('specialties', [
        'id' => $specialty->id,
        'name' => 'New',
    ]);
});

test('specialties destroy requires authentication', function () {
    $user = User::factory()->create();
    $specialty = Specialty::factory()->for($user)->create();

    $response = $this->deleteJson("/api/specialties/{$specialty->id}");

    $response->assertStatus(401);
});

test('specialties destroy soft deletes the specialty', function () {
    $user = User::factory()->create();
    $specialty = Specialty::factory()->for($user)->create();

    $response = $this->actingAs($user, 'sanctum')
        ->deleteJson("/api/specialties/{$specialty->id}");

    $response->assertSuccessful()
        ->assertJsonStructure(['success', 'message', 'data']);

    $this->assertSoftDeleted('specialties', [
        'id' => $specialty->id,
    ]);
});
