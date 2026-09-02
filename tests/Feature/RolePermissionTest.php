<?php

use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RoleSeeder;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(RoleSeeder::class);
    $this->seed(PermissionSeeder::class);
});

it('creates admin and medic roles', function () {
    expect(Role::findByName('admin'))->toBeInstanceOf(Role::class);
    expect(Role::findByName('medic'))->toBeInstanceOf(Role::class);
});

it('creates CRUD permissions for all resources', function () {
    $resources = ['users', 'patients', 'prescriptions', 'medicaments', 'rooms', 'specialties'];
    $actions = ['view', 'create', 'update', 'delete'];

    foreach ($resources as $resource) {
        foreach ($actions as $action) {
            expect(Permission::findByName("{$resource}.{$action}"))->toBeInstanceOf(Permission::class);
        }
    }
});

it('admin has all permissions', function () {
    $admin = Role::findByName('admin');
    expect($admin->getAllPermissions()->count())->toBe(24);
});

it('medic has only clinical permissions', function () {
    $medic = Role::findByName('medic');
    expect($medic->getAllPermissions()->count())->toBe(10);

    $expected = [
        'patients.view', 'patients.create', 'patients.update',
        'prescriptions.view', 'prescriptions.create', 'prescriptions.update',
        'medicaments.view',
        'rooms.view', 'rooms.update',
        'specialties.view',
    ];

    foreach ($expected as $perm) {
        expect($medic->hasPermissionTo($perm))->toBeTrue();
    }
});

it('admin user can access filament panel', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');

    $panel = Filament::getDefaultPanel();
    expect($admin->canAccessPanel($panel))->toBeTrue();
});

it('medic user cannot access filament panel', function () {
    $medic = User::factory()->create();
    $medic->assignRole('medic');

    $panel = Filament::getDefaultPanel();
    expect($medic->canAccessPanel($panel))->toBeFalse();
});

it('user without role cannot access filament panel', function () {
    $user = User::factory()->create();

    $panel = Filament::getDefaultPanel();
    expect($user->canAccessPanel($panel))->toBeFalse();
});

it('admin user has admin role', function () {
    $user = User::factory()->create();
    $user->assignRole('admin');

    expect($user->hasRole('admin'))->toBeTrue();
    expect($user->hasRole('medic'))->toBeFalse();
});

it('medic user has medic role', function () {
    $user = User::factory()->create();
    $user->assignRole('medic');

    expect($user->hasRole('medic'))->toBeTrue();
    expect($user->hasRole('admin'))->toBeFalse();
});

it('user can have multiple roles', function () {
    $user = User::factory()->create();
    $user->assignRole(['admin', 'medic']);

    expect($user->hasRole('admin'))->toBeTrue();
    expect($user->hasRole('medic'))->toBeTrue();
});
