<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

it('refreshes certificates expiring within configured days', function () {
    Storage::fake('local');

    $user = User::factory()->create([
        'certificate_expires_at' => now()->addDays(3),
    ]);

    Artisan::call('certificates:refresh');

    $user->refresh();

    expect($user->certificate_path)->not->toBeNull();
    expect($user->certificate_key_path)->not->toBeNull();
    expect($user->certificate_expires_at)->toBeGreaterThan(now());
    expect(Storage::disk('local')->exists($user->certificate_path))->toBeTrue();
});

it('does not refresh certificates not expiring soon', function () {
    Storage::fake('local');

    $user = User::factory()->create([
        'certificate_expires_at' => now()->addDays(30)->toDateTimeString(),
    ]);

    Artisan::call('certificates:refresh');

    $user->refresh();

    $expiresAt = Carbon::parse($user->certificate_expires_at);
    expect($expiresAt->format('Y-m-d'))->toBe(now()->addDays(30)->format('Y-m-d'));
});

it('reports number of refreshed certificates', function () {
    Storage::fake('local');

    User::factory()->count(2)->create([
        'certificate_expires_at' => now()->addDays(3),
    ]);

    Artisan::call('certificates:refresh');

    $output = Artisan::output();

    expect($output)->toContain('Refreshed 2');
});
