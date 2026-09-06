<?php

use App\Models\Patient;
use App\Models\Prescription;
use App\Models\Room;
use App\Models\Specialty;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(RoleSeeder::class);

    $this->doctorA = User::factory()->create();
    $this->doctorA->assignRole('medic');

    $this->doctorB = User::factory()->create();
    $this->doctorB->assignRole('medic');

    $this->admin = User::factory()->create();
    $this->admin->assignRole('admin');

    $this->pharmacy = User::factory()->create();
    $this->pharmacy->assignRole('farmacia');

    $patient = Patient::factory()->for($this->doctorA)->create();
    $room = Room::factory()->for($this->doctorA)->create();
    $specialty = Specialty::factory()->for($this->doctorA)->create();

    $this->draftPrescription = Prescription::factory()
        ->for($patient, 'patient')
        ->for($room, 'room')
        ->for($specialty, 'specialty')
        ->for($this->doctorA)
        ->create([
            'status' => config('custom.prescription.status_keys.draft'),
        ]);

    $this->activePrescription = Prescription::factory()
        ->for($patient, 'patient')
        ->for($room, 'room')
        ->for($specialty, 'specialty')
        ->for($this->doctorA)
        ->create([
            'status' => config('custom.prescription.status_keys.active'),
            'expires_at' => now()->addDays(7),
        ]);
});

it('allows owning doctor, admin, and pharmacy to view prescription', function () {
    expect($this->doctorA->can('view', $this->draftPrescription))->toBeTrue();
    expect($this->admin->can('view', $this->draftPrescription))->toBeTrue();
    expect($this->pharmacy->can('view', $this->draftPrescription))->toBeTrue();
    expect($this->doctorB->can('view', $this->draftPrescription))->toBeFalse();
});

it('allows medic and admin to create prescriptions', function () {
    expect($this->doctorA->can('create', Prescription::class))->toBeTrue();
    expect($this->admin->can('create', Prescription::class))->toBeTrue();
    expect($this->pharmacy->can('create', Prescription::class))->toBeFalse();
});

it('allows update and delete only on draft prescriptions by the owner or admin', function () {
    // Draft can be updated/deleted by owner and admin
    expect($this->doctorA->can('update', $this->draftPrescription))->toBeTrue();
    expect($this->admin->can('update', $this->draftPrescription))->toBeTrue();
    expect($this->doctorB->can('update', $this->draftPrescription))->toBeFalse();

    expect($this->doctorA->can('delete', $this->draftPrescription))->toBeTrue();
    expect($this->admin->can('delete', $this->draftPrescription))->toBeTrue();
    expect($this->doctorB->can('delete', $this->draftPrescription))->toBeFalse();

    // Active prescription cannot be updated or deleted even by owner
    expect($this->doctorA->can('update', $this->activePrescription))->toBeFalse();
    expect($this->doctorA->can('delete', $this->activePrescription))->toBeFalse();
});

it('allows only pharmacy and admin to dispense prescriptions', function () {
    expect($this->pharmacy->can('dispense', $this->activePrescription))->toBeTrue();
    expect($this->admin->can('dispense', $this->activePrescription))->toBeTrue();
    expect($this->doctorA->can('dispense', $this->activePrescription))->toBeFalse();
    expect($this->doctorB->can('dispense', $this->activePrescription))->toBeFalse();
});

it('allows owning doctor and admin to nullify active prescriptions', function () {
    expect($this->doctorA->can('nullify', $this->activePrescription))->toBeTrue();
    expect($this->admin->can('nullify', $this->activePrescription))->toBeTrue();
    expect($this->doctorB->can('nullify', $this->activePrescription))->toBeFalse();
    expect($this->pharmacy->can('nullify', $this->activePrescription))->toBeFalse();

    // Draft prescription cannot be nullified
    expect($this->doctorA->can('nullify', $this->draftPrescription))->toBeFalse();
});
