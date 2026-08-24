<?php

use App\Models\User;
use App\Services\CertificateService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

it('generates a certificate for a user', function () {
    Storage::fake('local');
    $user = User::factory()->create();

    $service = new CertificateService;
    $result = $service->generateForUser($user);

    expect($result)->toHaveKeys(['certificate_path', 'key_path', 'expires_at']);
    expect($result['certificate_path'])->toStartWith('certificates/'.$user->id.'/');
    expect($result['key_path'])->toStartWith('certificates/'.$user->id.'/');
    expect($result['expires_at'])->toBeString();
});

it('stores certificate files in storage', function () {
    Storage::fake('local');
    $user = User::factory()->create();

    $service = new CertificateService;
    $result = $service->generateForUser($user);

    expect(Storage::disk('local')->exists($result['certificate_path']))->toBeTrue();
    expect(Storage::disk('local')->exists($result['key_path']))->toBeTrue();
});

it('certificate contains valid PEM content', function () {
    Storage::fake('local');
    $user = User::factory()->create();

    $service = new CertificateService;
    $result = $service->generateForUser($user);

    $certContent = Storage::disk('local')->get($result['certificate_path']);
    $keyContent = Storage::disk('local')->get($result['key_path']);

    expect($certContent)->toContain('-----BEGIN CERTIFICATE-----');
    expect($certContent)->toContain('-----END CERTIFICATE-----');
    expect($keyContent)->toContain('-----BEGIN PRIVATE KEY-----');
    expect($keyContent)->toContain('-----END PRIVATE KEY-----');
});

it('certificate contains user information', function () {
    Storage::fake('local');
    $user = User::factory()->create([
        'first_name' => 'John',
        'last_name' => 'Doe',
        'email' => 'john.doe@example.com',
    ]);

    $service = new CertificateService;
    $result = $service->generateForUser($user);

    $certContent = Storage::disk('local')->get($result['certificate_path']);

    // Verify the certificate can be parsed and contains the expected subject
    $certDetails = openssl_x509_parse($certContent);
    expect($certDetails)->toBeArray();
    expect($certDetails['subject']['CN'])->toBe('John Doe');
    expect($certDetails['subject']['emailAddress'])->toBe('john.doe@example.com');
});

it('detects certificate needing refresh', function () {
    Storage::fake('local');
    $user = User::factory()->create([
        'certificate_expires_at' => now()->addDays(3),
    ]);

    $service = new CertificateService;

    expect($service->needsRefresh($user))->toBeTrue();
});

it('detects certificate not needing refresh', function () {
    Storage::fake('local');
    $user = User::factory()->create([
        'certificate_expires_at' => now()->addDays(30),
    ]);

    $service = new CertificateService;

    expect($service->needsRefresh($user))->toBeFalse();
});

it('detects user without certificate needing refresh', function () {
    $user = User::factory()->create([
        'certificate_expires_at' => null,
    ]);

    $service = new CertificateService;

    expect($service->needsRefresh($user))->toBeTrue();
});

it('refreshes a user certificate', function () {
    Storage::fake('local');
    $user = User::factory()->create([
        'certificate_path' => 'certificates/1/certificate.pem',
        'certificate_key_path' => 'certificates/1/private.key',
        'certificate_expires_at' => now()->addDays(3),
    ]);

    $service = new CertificateService;
    $result = $service->refreshForUser($user);

    expect($result)->toHaveKeys(['certificate_path', 'key_path', 'expires_at']);
    expect($result['certificate_path'])->toContain("certificates/{$user->id}/");
    expect(Storage::disk('local')->exists($result['certificate_path']))->toBeTrue();
    expect(Storage::disk('local')->exists($result['key_path']))->toBeTrue();
});

it('deletes a user certificate', function () {
    Storage::fake('local');
    $user = User::factory()->create([
        'certificate_path' => 'certificates/1/certificate.pem',
        'certificate_key_path' => 'certificates/1/private.key',
    ]);

    Storage::disk('local')->put('certificates/1/certificate.pem', 'cert-content');
    Storage::disk('local')->put('certificates/1/private.key', 'key-content');

    $service = new CertificateService;
    $service->deleteForUser($user);

    expect(Storage::disk('local')->exists('certificates/1/certificate.pem'))->toBeFalse();
    expect(Storage::disk('local')->exists('certificates/1/private.key'))->toBeFalse();
});

it('generates certificate with custom validity days', function () {
    Storage::fake('local');
    $user = User::factory()->create();

    $service = new CertificateService;
    $result = $service->generateForUser($user, 30);

    $expiresAt = Carbon::parse($result['expires_at']);
    $diffInDays = now()->addDays(30)->diffInDays($expiresAt);

    expect($diffInDays)->toBeLessThanOrEqual(1);
});
