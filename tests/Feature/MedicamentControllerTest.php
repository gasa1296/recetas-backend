<?php

use App\Models\Medicament;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

/*
|--------------------------------------------------------------------------
| MedicamentController
|--------------------------------------------------------------------------
| GET /api/medicaments         -> auth:sanctum (SearchRequest)
| GET /api/medicaments/{id}    -> auth:sanctum
*/

test('medicaments index requires authentication', function () {
    $response = $this->getJson('/api/medicaments');

    $response->assertStatus(401);
});

test('medicaments index rejects invalid search query', function () {
    $response = $this->actingAs(User::factory()->create(), 'sanctum')
        ->getJson('/api/medicaments?search='.str_repeat('a', 256));

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['search']);
});

test('medicaments index accepts valid optional search query', function () {
    Medicament::factory()->count(3)->create([
        'active_ingredient' => 'Paracetamol',
    ]);

    $response = $this->actingAs(User::factory()->create(), 'sanctum')
        ->getJson('/api/medicaments?search=Para');

    $response->assertSuccessful();
});

test('medicaments index returns paginated collection with correct structure', function () {
    Medicament::factory()->count(15)->create();

    $response = $this->actingAs(User::factory()->create(), 'sanctum')
        ->getJson('/api/medicaments');

    $response->assertSuccessful()
        ->assertJsonStructure([
            'message',
            'success',
            'data' => [
                '*' => ['id', 'active_ingredient', 'type', 'group'],
            ],
        ])
        ->assertJsonCount(10, 'data');
});

test('medicaments index filters by active_ingredient when search is provided', function () {
    Medicament::factory()->create(['active_ingredient' => 'Ibuprofen']);
    Medicament::factory()->create(['active_ingredient' => 'Paracetamol']);

    $response = $this->actingAs(User::factory()->create(), 'sanctum')
        ->getJson('/api/medicaments?search=Ibupr');

    $response->assertSuccessful()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.active_ingredient', 'Ibuprofen');
});

test('medicaments show requires authentication', function () {
    $medicament = Medicament::factory()->create();

    $response = $this->getJson('/api/medicaments/'.$medicament->id);

    $response->assertStatus(401);
});

test('medicaments show returns the requested medicament', function () {
    $medicament = Medicament::factory()->create([
        'active_ingredient' => 'Aspirin',
        'type' => 'Analgesic',
        'group' => 'NSAID',
    ]);

    $response = $this->actingAs(User::factory()->create(), 'sanctum')
        ->getJson('/api/medicaments/'.$medicament->id);

    $response->assertSuccessful()
        ->assertJsonStructure([
            'success',
            'message',
            'data' => ['id', 'active_ingredient', 'type', 'group'],
        ])
        ->assertJsonPath('data.id', $medicament->id)
        ->assertJsonPath('data.active_ingredient', 'Aspirin')
        ->assertJsonPath('data.type', 'Analgesic')
        ->assertJsonPath('data.group', 'NSAID');
});

test('medicaments show returns 404 for missing medicament', function () {
    $response = $this->actingAs(User::factory()->create(), 'sanctum')
        ->getJson('/api/medicaments/999999');

    $response->assertNotFound();
});
