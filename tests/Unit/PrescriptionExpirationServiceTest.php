<?php

use App\Models\Medicament;
use App\Models\Prescription;
use App\Services\Prescription\PrescriptionExpirationService;
use Illuminate\Support\Carbon;
use Tests\TestCase;

uses(TestCase::class);

test('calculates default expiration days when prescription has no special medicaments', function () {
    config()->set('custom.prescription.expiration_days', [
        'default' => 30,
        'antibiotic' => 7,
    ]);

    $service = new PrescriptionExpirationService;
    $prescription = new Prescription;
    $prescription->setRelation('medicaments', collect());

    $now = Carbon::parse('2026-09-06 10:00:00');
    Carbon::setTestNow($now);

    $result = $service->calculateExpiration($prescription);

    expect($result['days'])->toBe(30)
        ->and($result['expires_at']->toDateString())->toBe($now->copy()->addDays(30)->toDateString());
});

test('calculates specific expiration days based on medicament type', function () {
    config()->set('custom.prescription.expiration_days', [
        'default' => 30,
        'antibiotic' => 7,
        'psychotropic' => 15,
    ]);

    $service = new PrescriptionExpirationService;
    $prescription = new Prescription;

    $medicament = new Medicament;
    $medicament->type = 'antibiotic';
    $prescription->setRelation('medicaments', collect([$medicament]));

    $now = Carbon::parse('2026-09-06 10:00:00');
    Carbon::setTestNow($now);

    $result = $service->calculateExpiration($prescription);

    expect($result['days'])->toBe(7)
        ->and($result['expires_at']->toDateString())->toBe($now->copy()->addDays(7)->toDateString());
});
