<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $guard = config('auth.defaults.guard', 'web');

        $admin = Role::findByName('admin', $guard);
        $admin?->syncPermissions(Permission::all());

        $medic = Role::findByName('medic', $guard);
        $medic?->syncPermissions([
            'patients.view',
            'patients.create',
            'patients.update',
            'prescriptions.view',
            'prescriptions.create',
            'prescriptions.update',
            'medicaments.view',
            'rooms.view',
            'rooms.update',
            'specialties.view',
        ]);
    }
}
