<?php

use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;

uses(RefreshDatabase::class);

/*
|--------------------------------------------------------------------------
| ResetController
|--------------------------------------------------------------------------
| POST /api/password/request -> public (ResetRequestRequest)
| POST /api/password/reset   -> public (ResetRequest)
*/

dataset('invalid_reset_request_payloads', [
    'missing email' => [[]],
    'invalid email format' => [['email' => 'not-an-email']],
]);

dataset('invalid_reset_payloads', [
    'empty body' => [[], ['token', 'email', 'password']],
    'missing token' => [
        ['email' => 'a@b.com', 'password' => 'newpass1', 'password_confirmation' => 'newpass1'],
        ['token'],
    ],
    'missing email' => [
        ['token' => 'abc', 'password' => 'newpass1', 'password_confirmation' => 'newpass1'],
        ['email'],
    ],
    'invalid email' => [
        ['token' => 'abc', 'email' => 'nope', 'password' => 'newpass1', 'password_confirmation' => 'newpass1'],
        ['email'],
    ],
    'password too short' => [
        ['token' => 'abc', 'email' => 'a@b.com', 'password' => 'short', 'password_confirmation' => 'short'],
        ['password'],
    ],
    'password not confirmed' => [
        ['token' => 'abc', 'email' => 'a@b.com', 'password' => 'newpass1', 'password_confirmation' => 'different1'],
        ['password'],
    ],
]);

test('password request rejects invalid request structure', function (array $payload) {
    $response = $this->postJson('/api/password/request', $payload);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['email']);
})->with('invalid_reset_request_payloads');

test('password request authorizes public access without authentication', function () {
    $user = User::factory()->create();

    Notification::fake();

    $response = $this->postJson('/api/password/request', [
        'email' => $user->email,
    ]);

    $response->assertSuccessful()
        ->assertJsonStructure(['success', 'message', 'data']);

    Notification::assertSentTo($user, ResetPassword::class);
});

test('password request returns error when no user matches the email', function () {
    Notification::fake();

    $response = $this->postJson('/api/password/request', [
        'email' => 'nobody@example.com',
    ]);

    $response->assertStatus(400)
        ->assertJsonStructure(['success', 'message', 'errors' => ['email']]);
});

test('password reset rejects invalid request structure', function (array $payload, array $errors) {
    $response = $this->postJson('/api/password/reset', $payload);

    $response->assertStatus(422)
        ->assertJsonValidationErrors($errors);
})->with('invalid_reset_payloads');

test('password reset authorizes public access and returns success on valid token', function () {
    $user = User::factory()->create();
    $token = Password::broker()->createToken($user);

    $response = $this->postJson('/api/password/reset', [
        'token' => $token,
        'email' => $user->email,
        'password' => 'brandnew1',
        'password_confirmation' => 'brandnew1',
    ]);

    $response->assertSuccessful()
        ->assertJsonStructure(['success', 'message', 'data']);
});

test('password reset returns error for invalid token', function () {
    $user = User::factory()->create();

    $response = $this->postJson('/api/password/reset', [
        'token' => 'invalid-token',
        'email' => $user->email,
        'password' => 'brandnew1',
        'password_confirmation' => 'brandnew1',
    ]);

    $response->assertStatus(400)
        ->assertJsonStructure(['success', 'message', 'data']);
});
