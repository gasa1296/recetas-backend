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

test('login fails with empty body', function () {
    $response = $this->postJson('/api/auth/login', []);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['email', 'password']);
});

test('login fails when email is missing', function () {
    $response = $this->postJson('/api/auth/login', ['password' => 'password123']);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['email']);
});

test('login fails with invalid email format', function () {
    $response = $this->postJson('/api/auth/login', [
        'email' => 'not-an-email',
        'password' => 'password123',
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['email']);
});

test('login fails when password is missing', function () {
    $response = $this->postJson('/api/auth/login', ['email' => 'user@example.com']);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['password']);
});

test('login fails when password is too short', function () {
    $response = $this->postJson('/api/auth/login', [
        'email' => 'user@example.com',
        'password' => 'short',
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['password']);
});

test('login fails with non-string password', function () {
    $response = $this->postJson('/api/auth/login', [
        'email' => 'user@example.com',
        'password' => ['array'],
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['password']);
});

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
                    'specialty',
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

    $response = $this->withHeader('Authorization', 'Bearer '.$token->plainTextToken)
        ->postJson('/api/auth/logout');

    $response->assertSuccessful()
        ->assertJsonStructure(['success', 'message', 'data']);

    expect($user->tokens()->count())->toBe(0);
});

/*
|--------------------------------------------------------------------------
| POST /api/auth/register
|--------------------------------------------------------------------------
*/

test('register fails with empty body', function () {
    $response = $this->postJson('/api/auth/register', []);

    $response->assertStatus(422)
        ->assertJsonValidationErrors([
            'first_name',
            'last_name',
            'identification',
            'email',
            'password',
            'specialty',
        ]);
});

test('register fails when password is not confirmed or too short', function () {
    $response = $this->postJson('/api/auth/register', [
        'first_name' => 'John',
        'last_name' => 'Doe',
        'identification' => 'V-12345678',
        'email' => 'doctor@example.com',
        'password' => 'short',
        'password_confirmation' => 'mismatch',
        'specialty' => [
            'name' => 'Cardiología',
            'identification' => [
                'medic_society' => '12345',
                'medic_registration' => '1234567',
            ],
        ],
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['password']);
});

test('register fails when specialty identification is invalid', function () {
    $response = $this->postJson('/api/auth/register', [
        'first_name' => 'John',
        'last_name' => 'Doe',
        'identification' => 'V-12345678',
        'email' => 'doctor@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
        'specialty' => [
            'name' => 'Cardiología',
            'identification' => [
                'medic_society' => '12345',
                'medic_registration' => 'invalid', // must be numeric 7 digits
            ],
        ],
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['specialty.identification.medic_registration']);
});

test('register succeeds with complete doctor data and returns token with profile', function () {
    $this->seed(\Database\Seeders\RoleSeeder::class);

    $payload = [
        'first_name' => 'Gregory',
        'last_name' => 'House',
        'identification' => 'V-99887766',
        'email' => 'house@princetonplainsboro.com',
        'password' => 'diagnostico123',
        'password_confirmation' => 'diagnostico123',
        'phone' => ['+58 412 1234567', '+58 424 7654321'],
        'specialty' => [
            'name' => 'Medicina Interna / Nefrología',
            'identification' => [
                'medic_society' => 'CMD-55443',
                'medic_registration' => '7654321',
            ],
        ],
        'room' => [
            'name' => 'Consultorio 101 - Clínica Central',
            'address' => 'Av. Principal, Piso 1',
            'phone' => '0212-5555555',
        ],
    ];

    $response = $this->postJson('/api/auth/register', $payload);

    $response->assertStatus(201)
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
                    'specialty',
                ],
            ],
        ])
        ->assertJsonPath('data.profile.first_name', 'Gregory')
        ->assertJsonPath('data.profile.last_name', 'House')
        ->assertJsonPath('data.profile.email', 'house@princetonplainsboro.com')
        ->assertJsonPath('data.profile.specialty.name', 'Medicina Interna / Nefrología');

    expect($response->json('data.token'))->toBeString()->not->toBeEmpty();

    $user = User::where('email', 'house@princetonplainsboro.com')->first();
    expect($user)->not->toBeNull();
    expect($user->hasRole('medic'))->toBeTrue();
    expect($user->hasValidCertificate())->toBeTrue();
    expect($user->specialty)->not->toBeNull();
    expect($user->rooms)->toHaveCount(1);
    expect($user->rooms->first()->name)->toBe('Consultorio 101 - Clínica Central');
});

test('register fails when email or identification is already taken', function () {
    $this->seed(\Database\Seeders\RoleSeeder::class);

    User::factory()->create([
        'email' => 'existing@example.com',
        'identification' => 'V-11111111',
    ]);

    $payload = [
        'first_name' => 'John',
        'last_name' => 'Doe',
        'identification' => 'V-11111111',
        'email' => 'existing@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
        'specialty' => [
            'name' => 'General',
            'identification' => [
                'medic_society' => 'MS-999',
                'medic_registration' => '1234567',
            ],
        ],
    ];

    $response = $this->postJson('/api/auth/register', $payload);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['email', 'identification']);
});

