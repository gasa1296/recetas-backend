<?php

use App\Jobs\RefreshExpiringCertificatesJob;
use App\Models\User;
use App\Services\CertificateService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

it('dispatches as a queued job', function () {
    Bus::fake([RefreshExpiringCertificatesJob::class]);

    RefreshExpiringCertificatesJob::dispatch();

    Bus::assertDispatched(RefreshExpiringCertificatesJob::class);
});

it('refreshes certificates expiring within threshold', function () {
    Storage::fake('local');

    $user = User::factory()->create([
        'certificate_expires_at' => now()->addDays(3),
    ]);

    $job = new RefreshExpiringCertificatesJob;
    $job->handle(app(CertificateService::class));

    $user->refresh();

    expect($user->certificate_path)->not->toBeNull();
    expect($user->certificate_key_path)->not->toBeNull();
    expect($user->certificate_expires_at)->toBeGreaterThan(now()->addDays(300));
    expect(Storage::disk('local')->exists($user->certificate_path))->toBeTrue();
});

it('does not refresh certificates not expiring soon', function () {
    Storage::fake('local');

    $user = User::factory()->create([
        'certificate_expires_at' => now()->addDays(30)->toDateTimeString(),
    ]);

    $job = new RefreshExpiringCertificatesJob;
    $job->handle(app(CertificateService::class));

    $user->refresh();

    $expiresAt = Carbon::parse($user->certificate_expires_at);
    expect($expiresAt->format('Y-m-d'))->toBe(now()->addDays(30)->format('Y-m-d'));
});

it('handles users without certificate_expires_at', function () {
    Storage::fake('local');

    $user = User::factory()->create([
        'certificate_expires_at' => null,
    ]);

    $job = new RefreshExpiringCertificatesJob;
    $job->handle(app(CertificateService::class));

    $user->refresh();
    expect($user->certificate_expires_at)->toBeNull();
});

it('refreshes multiple users in batch', function () {
    Storage::fake('local');

    $users = User::factory()->count(3)->create([
        'certificate_expires_at' => now()->addDays(2),
    ]);

    $job = new RefreshExpiringCertificatesJob;
    $job->handle(app(CertificateService::class));

    foreach ($users as $user) {
        $user->refresh();
        expect($user->certificate_expires_at)->toBeGreaterThan(now()->addDays(300));
    }
});

it('does not fail when one user certificate refresh throws', function () {
    Storage::fake('local');

    $goodUser = User::factory()->create([
        'certificate_expires_at' => now()->addDays(2),
    ]);

    $job = new RefreshExpiringCertificatesJob;
    $job->handle(app(CertificateService::class));

    $goodUser->refresh();
    expect($goodUser->certificate_expires_at)->toBeGreaterThan(now()->addDays(300));
});
