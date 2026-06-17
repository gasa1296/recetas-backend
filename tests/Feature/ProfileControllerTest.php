<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

/*
|--------------------------------------------------------------------------
| ProfileController
|--------------------------------------------------------------------------
| GET    /api/profile -> auth:sanctum
| PUT    /api/profile -> auth:sanctum (ProfileRequest)
| DELETE /api/profile -> auth:sanctum
*/

dataset('invalid_profile_payloads', [
    'missing first_name' => [
        ['last_name' => 'Doe'],
        ['first_name'],
    ],
    'missing last_name' => [
        ['first_name' => 'John'],
        ['last_name'],
    ],
    'password without confirmation' => [
        ['first_name' => 'John', 'last_name' => 'Doe', 'password' => 'newpass1'],
        ['password'],
    ],
    'phone not array' => [
        ['first_name' => 'John', 'last_name' => 'Doe', 'phone' => 'string'],
        ['phone'],
    ],
]);

test('profile index requires authentication', function () {
    $response = $this->getJson('/api/profile');

    $response->assertStatus(401);
});

test('profile index returns the authenticated user profile', function () {
    $user = User::factory()->create([
        'first_name' => 'John',
        'last_name' => 'Doe',
        'email' => 'john@example.com',
    ]);

    $response = $this->actingAs($user, 'sanctum')
        ->getJson('/api/profile');

    $response->assertSuccessful()
        ->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'first_name',
                'last_name',
                'identification',
                'phone',
                'email',
                'rooms',
                'specialties',
            ],
        ])
        ->assertJsonPath('data.first_name', 'John')
        ->assertJsonPath('data.last_name', 'Doe')
        ->assertJsonPath('data.email', 'john@example.com');
});

test('profile update requires authentication', function () {
    $response = $this->putJson('/api/profile', [
        'first_name' => 'John',
        'last_name' => 'Doe',
    ]);

    $response->assertStatus(401);
});

test('profile update rejects invalid request structure', function (array $payload, array $errors) {
    $user = User::factory()->create();

    $response = $this->actingAs($user, 'sanctum')
        ->putJson('/api/profile', $payload);

    $response->assertStatus(422)
        ->assertJsonValidationErrors($errors);
})->with('invalid_profile_payloads');

test('profile update modifies the user with valid request structure', function () {
    $user = User::factory()->create(['first_name' => 'Old']);

    $response = $this->actingAs($user, 'sanctum')
        ->putJson('/api/profile', [
            'first_name' => 'New',
            'last_name' => 'Name',
        ]);

    $response->assertSuccessful()
        ->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'first_name',
                'last_name',
                'identification',
                'phone',
                'email',
                'rooms',
                'specialties',
            ],
        ])
        ->assertJsonPath('data.first_name', 'New')
        ->assertJsonPath('data.last_name', 'Name');

    $this->assertDatabaseHas('users', [
        'id' => $user->id,
        'first_name' => 'New',
        'last_name' => 'Name',
    ]);
});

test('profile update persists password when confirmation matches', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user, 'sanctum')
        ->putJson('/api/profile', [
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'password' => 'newpass1',
            'password_confirmation' => 'newpass1',
        ]);

    $response->assertSuccessful();
});

test('profile destroy requires authentication', function () {
    $response = $this->deleteJson('/api/profile');

    $response->assertStatus(401);
});

test('profile destroy soft deletes the authenticated user', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user, 'sanctum')
        ->deleteJson('/api/profile');

    $response->assertSuccessful()
        ->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'first_name',
                'last_name',
                'identification',
                'phone',
                'email',
                'rooms',
                'specialties',
            ],
        ]);

    $this->assertSoftDeleted('users', [
        'id' => $user->id,
    ]);
});
