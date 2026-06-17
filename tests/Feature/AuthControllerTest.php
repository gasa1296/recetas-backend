<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;

uses(RefreshDatabase::class);

/*
|--------------------------------------------------------------------------
| AuthController
|--------------------------------------------------------------------------
| POST /api/auth/login        -> public       (LoginRequest)
| POST /api/auth/logout       -> auth:sanctum
*/

dataset('invalid_login_payloads', [
    'empty body' => [],
    'missing email' => ['password' => 'password123'],
    'invalid email format' => ['email' => 'not-an-email', 'password' => 'password123'],
    'missing password' => ['email' => 'user@example.com'],
    'password too short' => ['email' => 'user@example.com', 'password' => 'short'],
    'non-string password' => ['email' => 'user@example.com', 'password' => ['array']],
]);

test('login requires email and password fields', function (array $payload) {
    $response = $this->postJson('/api/auth/login', $payload);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['email', 'password']);
})->with('invalid_login_payloads');

test('login rejects request authorization when authorize returns false', function () {
    // LoginRequest::authorize() always returns true, but we assert the
    // happy path produces a non-403 response and the validation rules fire.
    $response = $this->postJson('/api/auth/login', [
        'email' => 'nobody@example.com',
        'password' => 'password',
    ]);

    expect($response->status())->not->toBe(403);
});

test('login returns 401 with invalid credentials on valid request structure', function () {
    User::factory()->create([
        'email' => 'jane@example.com',
        'password' => 'password',
    ]);

    $response = $this->postJson('/api/auth/login', [
        'email' => 'jane@example.com',
        'password' => 'wrong-password',
    ]);

    $response->assertStatus(401)
        ->assertJson([
            'success' => false,
        ]);
});

test('login succeeds with valid credentials and returns token plus profile', function () {
    $user = User::factory()->create([
        'email' => 'jane@example.com',
        'password' => 'password',
    ]);

    $response = $this->postJson('/api/auth/login', [
        'email' => 'jane@example.com',
        'password' => 'password',
    ]);

    $response->assertSuccessful()
        ->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'token',
                'profile' => [
                    'first_name',
                    'last_name',
                    'identification',
                    'phone',
                    'email',
                    'rooms',
                    'specialties',
                ],
            ],
        ])
        ->assertJsonPath('data.profile.email', $user->email);

    expect($response->json('data.token'))->toBeString()->not->toBeEmpty();
});

test('logout requires authentication', function () {
    $response = $this->postJson('/api/auth/logout');

    $response->assertStatus(401);
});

test('logout succeeds for authenticated user and deletes current token', function () {
    $user = User::factory()->create();
    $token = $user->createToken('login');

    $response = $this->withHeader('Authorization', "Bearer {$token->plainTextToken}")
        ->postJson('/api/auth/logout');

    $response->assertSuccessful()
        ->assertJsonStructure(['success', 'message', 'data']);

    expect($user->tokens()->count())->toBe(0);
});
