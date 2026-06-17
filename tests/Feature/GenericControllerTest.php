<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

/*
|--------------------------------------------------------------------------
| GenericController
|--------------------------------------------------------------------------
| GET  /api/generic/genders            -> auth:sanctum
| GET  /api/generic/prescription-status -> auth:sanctum
*/

test('genders endpoint requires authentication', function () {
    $response = $this->getJson('/api/generic/genders');

    $response->assertStatus(401);
});

test('genders endpoint returns the configured gender list', function () {
    User::factory()->create();

    $response = $this->actingAs(User::factory()->create(), 'sanctum')
        ->getJson('/api/generic/genders');

    $response->assertSuccessful()
        ->assertJsonStructure(['success', 'message', 'data'])
        ->assertJsonPath('data', config('custom.gender'));
});

test('genders endpoint returns exactly the configured number of entries', function () {
    $response = $this->actingAs(User::factory()->create(), 'sanctum')
        ->getJson('/api/generic/genders');

    $expectedCount = count(config('custom.gender'));

    $response->assertSuccessful()
        ->assertJsonCount($expectedCount, 'data');
});

test('prescription status endpoint requires authentication', function () {
    $response = $this->getJson('/api/generic/prescription-status');

    $response->assertStatus(401);
});

test('prescription status endpoint returns the configured status map', function () {
    $response = $this->actingAs(User::factory()->create(), 'sanctum')
        ->getJson('/api/generic/prescription-status');

    $response->assertSuccessful()
        ->assertJsonStructure(['success', 'message', 'data'])
        ->assertJsonPath('data', config('custom.prescription.status'));
});

test('prescription status endpoint returns exactly the configured number of statuses', function () {
    $response = $this->actingAs(User::factory()->create(), 'sanctum')
        ->getJson('/api/generic/prescription-status');

    $expectedCount = count(config('custom.prescription.status'));

    $response->assertSuccessful()
        ->assertJsonCount($expectedCount, 'data');
});
