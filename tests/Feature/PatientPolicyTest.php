<?php

use App\Models\Patient;
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

    $this->patientA = Patient::factory()->create([
        'user_id' => $this->doctorA->id,
    ]);
});

it('allows a doctor to view their own patient', function () {
    expect($this->doctorA->can('view', $this->patientA))->toBeTrue();
});

it('prevents another doctor from viewing a patient they do not own', function () {
    expect($this->doctorB->can('view', $this->patientA))->toBeFalse();
});
