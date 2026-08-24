<?php

use App\Models\Medicament;
use App\Models\PrescriptionTemplate;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

/*
|--------------------------------------------------------------------------
| PrescriptionTemplateController
|--------------------------------------------------------------------------
| GET    /api/prescription-templates        -> auth:sanctum
| POST   /api/prescription-templates        -> auth:sanctum (PrescriptionTemplateRequest)
| GET    /api/prescription-templates/{id}   -> auth:sanctum
| PUT    /api/prescription-templates/{id}   -> auth:sanctum (PrescriptionTemplateRequest)
| DELETE /api/prescription-templates/{id}   -> auth:sanctum
*/

function templatePayload(array $overrides = []): array
{
    return array_merge([
        'name' => 'Grip template',
        'medicaments' => [
            [
                'id' => Medicament::factory()->create()->id,
                'dosage' => '1 tableta',
                'frequency' => 'cada 8 horas',
                'duration' => '7 dias',
                'medicament_quantity' => 21,
                'recommended_brand' => null,
            ],
        ],
    ], $overrides);
}

test('templates index requires authentication', function () {
    $response = $this->getJson('/api/prescription-templates');

    $response->assertStatus(401);
});

test('templates index returns only the authenticated user templates with medicaments', function () {
    $user = User::factory()->create();
    $other = User::factory()->create();
    PrescriptionTemplate::factory()->count(3)->for($user)->create();
    PrescriptionTemplate::factory()->count(2)->for($other)->create();

    $response = $this->actingAs($user, 'sanctum')
        ->getJson('/api/prescription-templates');

    $response->assertSuccessful()
        ->assertJsonStructure([
            'success',
            'message',
            'data' => [
                '*' => ['id', 'name', 'medicaments', 'created_at'],
            ],
        ])
        ->assertJsonCount(3, 'data');
});

test('templates store requires authentication', function () {
    $response = $this->postJson('/api/prescription-templates', ['name' => 'X']);

    $response->assertStatus(401);
});

test('templates store rejects missing name', function () {
    $payload = templatePayload(['name' => null]);

    $response = $this->actingAs(User::factory()->create(), 'sanctum')
        ->postJson('/api/prescription-templates', $payload);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['name']);
});

test('templates store creates a template with medicaments', function () {
    $user = User::factory()->create();
    $payload = templatePayload();

    $response = $this->actingAs($user, 'sanctum')
        ->postJson('/api/prescription-templates', $payload);

    $templateId = $response->json('data.id');

    $response->assertSuccessful()
        ->assertJsonStructure([
            'success',
            'message',
            'data' => ['id', 'name', 'medicaments', 'created_at'],
        ])
        ->assertJsonPath('data.name', 'Grip template')
        ->assertJsonCount(1, 'data.medicaments');

    expect($response->json('data.medicaments.0.dosage'))->toBe('1 tableta');
    expect($response->json('data.medicaments.0.active_ingredient'))->toBeString();

    $this->assertDatabaseHas('prescription_templates', [
        'id' => $templateId,
        'user_id' => $user->id,
        'name' => 'Grip template',
    ]);
    $this->assertDatabaseHas('medicament_prescription_templates', [
        'prescription_template_id' => $templateId,
        'medicament_id' => $payload['medicaments'][0]['id'],
        'dosage' => '1 tableta',
    ]);
});

test('templates store accepts payload without medicaments', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user, 'sanctum')
        ->postJson('/api/prescription-templates', ['name' => 'Empty']);

    $response->assertSuccessful()
        ->assertJsonPath('data.name', 'Empty');

    $this->assertDatabaseHas('prescription_templates', ['name' => 'Empty']);
});

test('templates show requires authentication', function () {
    $user = User::factory()->create();
    $template = PrescriptionTemplate::factory()->for($user)->create();

    $response = $this->getJson('/api/prescription-templates/'.$template->id);

    $response->assertStatus(401);
});

test('templates show returns 404 for template not owned by user', function () {
    $owner = User::factory()->create();
    $stranger = User::factory()->create();
    $template = PrescriptionTemplate::factory()->for($owner)->create();

    $response = $this->actingAs($stranger, 'sanctum')
        ->getJson('/api/prescription-templates/'.$template->id);

    $response->assertNotFound();
});

test('templates show returns the requested template', function () {
    $user = User::factory()->create();
    $template = PrescriptionTemplate::factory()->for($user)->create(['name' => 'Named']);
    $template->medicaments()->attach(Medicament::factory()->create()->id, [
        'dosage' => '2 ml',
        'frequency' => 'daily',
        'duration' => '5 days',
        'medicament_quantity' => 1,
        'medicament_quantity_letters' => 'one',
        'recommended_brand' => null,
    ]);

    $response = $this->actingAs($user, 'sanctum')
        ->getJson('/api/prescription-templates/'.$template->id);

    $response->assertSuccessful()
        ->assertJsonPath('data.id', $template->id)
        ->assertJsonPath('data.name', 'Named')
        ->assertJsonCount(1, 'data.medicaments')
        ->assertJsonPath('data.medicaments.0.dosage', '2 ml');
});

test('templates update modifies name and syncs medicaments', function () {
    $user = User::factory()->create();
    $template = PrescriptionTemplate::factory()->for($user)->create(['name' => 'Old']);
    $oldMedicament = Medicament::factory()->create();
    $template->medicaments()->attach($oldMedicament->id, [
        'dosage' => 'd', 'frequency' => 'f', 'duration' => 'u',
        'medicament_quantity' => 1, 'medicament_quantity_letters' => 'one',
    ]);

    $newMedicament = Medicament::factory()->create();
    $response = $this->actingAs($user, 'sanctum')
        ->putJson('/api/prescription-templates/'.$template->id, [
            'name' => 'New',
            'medicaments' => [
                [
                    'id' => $newMedicament->id,
                    'dosage' => '3 ui',
                    'frequency' => 'weekly',
                    'duration' => '9 days',
                    'medicament_quantity' => 3,
                    'recommended_brand' => 'Brand X',
                ],
            ],
        ]);

    $response->assertSuccessful()
        ->assertJsonPath('data.id', $template->id)
        ->assertJsonPath('data.name', 'New')
        ->assertJsonCount(1, 'data.medicaments');

    $this->assertDatabaseHas('prescription_templates', [
        'id' => $template->id,
        'name' => 'New',
    ]);
    $this->assertDatabaseMissing('medicament_prescription_templates', [
        'prescription_template_id' => $template->id,
        'medicament_id' => $oldMedicament->id,
    ]);
    $this->assertDatabaseHas('medicament_prescription_templates', [
        'prescription_template_id' => $template->id,
        'medicament_id' => $newMedicament->id,
        'recommended_brand' => 'Brand X',
    ]);
});

test('templates destroy requires authentication', function () {
    $user = User::factory()->create();
    $template = PrescriptionTemplate::factory()->for($user)->create();

    $response = $this->deleteJson('/api/prescription-templates/'.$template->id);

    $response->assertStatus(401);
});

test('templates destroy soft deletes the template', function () {
    $user = User::factory()->create();
    $template = PrescriptionTemplate::factory()->for($user)->create();

    $response = $this->actingAs($user, 'sanctum')
        ->deleteJson('/api/prescription-templates/'.$template->id);

    $response->assertSuccessful()
        ->assertJsonStructure(['success', 'message', 'data']);

    $this->assertSoftDeleted('prescription_templates', [
        'id' => $template->id,
    ]);
});
