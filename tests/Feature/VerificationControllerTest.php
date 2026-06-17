<?php

use App\Models\User;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\URL;

uses(RefreshDatabase::class);

/*
|--------------------------------------------------------------------------
| VerificationController
|--------------------------------------------------------------------------
| GET  /api/emailVerification/verify  -> public (signature check)
| POST /api/emailVerification/resend  -> public
*/

test('verify endpoint authorizes public access with valid signature', function () {
    $user = User::factory()->unverified()->create();

    $url = URL::temporarySignedRoute(
        'emailVerification.verify',
        now()->addMinutes(30),
        ['user_id' => $user->id, 'hash' => sha1($user->getEmailForVerification())],
    );

    $response = $this->getJson($url);

    $response->assertSuccessful()
        ->assertJsonStructure(['success', 'message', 'data']);

    $this->assertNotNull($user->fresh()->email_verified_at);
});

test('verify endpoint rejects requests with invalid or expired signature', function () {
    $user = User::factory()->unverified()->create();

    $response = $this->getJson('/api/emailVerification/verify?user_id='.$user->id);

    $response->assertStatus(400)
        ->assertJsonStructure(['success', 'message', 'data']);
});

test('verify endpoint returns 404 for missing user', function () {
    $url = URL::temporarySignedRoute(
        'emailVerification.verify',
        now()->addMinutes(30),
        ['user_id' => 999999, 'hash' => sha1('fake@email.com')],
    );

    $response = $this->getJson($url);

    $response->assertNotFound();
});

test('resend endpoint authorizes public access and sends notification', function () {
    Notification::fake();
    $user = User::factory()->unverified()->create();

    $response = $this->postJson('/api/emailVerification/resend', [
        'email' => $user->email,
    ]);

    $response->assertSuccessful()
        ->assertJsonStructure(['success', 'message', 'data']);

    Notification::assertSentTo($user, VerifyEmail::class);
});

test('resend endpoint rejects request without email', function () {
    $response = $this->postJson('/api/emailVerification/resend', []);

    $response->assertStatus(400)
        ->assertJsonStructure(['success', 'message', 'data']);
});

test('resend endpoint rejects already verified users', function () {
    Notification::fake();
    $user = User::factory()->create();

    $response = $this->postJson('/api/emailVerification/resend', [
        'email' => $user->email,
    ]);

    $response->assertStatus(400)
        ->assertJsonStructure(['success', 'message', 'data']);

    Notification::assertNothingSent();
});
