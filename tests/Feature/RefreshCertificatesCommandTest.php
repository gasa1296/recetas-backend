<?php

use App\Jobs\RefreshExpiringCertificatesJob;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Bus;

uses(RefreshDatabase::class);

it('dispatches the RefreshExpiringCertificatesJob', function () {
    Bus::fake([RefreshExpiringCertificatesJob::class]);

    Artisan::call('certificates:refresh');

    Bus::assertDispatched(RefreshExpiringCertificatesJob::class);
});

it('returns success status', function () {
    Bus::fake([RefreshExpiringCertificatesJob::class]);

    $exitCode = Artisan::call('certificates:refresh');

    expect($exitCode)->toBe(0);
});

it('outputs dispatch confirmation', function () {
    Bus::fake([RefreshExpiringCertificatesJob::class]);

    Artisan::call('certificates:refresh');

    $output = Artisan::output();
    expect($output)->toContain('Certificate refresh job dispatched.');
});
