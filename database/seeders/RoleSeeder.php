<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Permission names follow the pattern `<resource>.<action>`.
     *
     * @var list<string>
     */
    protected array $permissions = [
        'users.view',
        'users.create',
        'users.update',
        'users.delete',
        'patients.view',
        'patients.create',
        'patients.update',
        'patients.delete',
        'prescriptions.view',
        'prescriptions.create',
        'prescriptions.update',
        'prescriptions.delete',
        'medicaments.view',
        'medicaments.create',
        'medicaments.update',
        'medicaments.delete',
        'rooms.view',
        'rooms.create',
        'rooms.update',
        'rooms.delete',
        'specialties.view',
        'specialties.create',
        'specialties.update',
        'specialties.delete',
    ];

    public function run(): void
    {
        $guard = config('auth.defaults.guard', 'web');

        foreach ($this->permissions as $name) {
            Permission::firstOrCreate([
                'name' => $name,
                'guard_name' => $guard,
            ]);
        }

        $guard = config('auth.defaults.guard', 'web');

        $admin = Role::firstOrCreate(['name' => 'admin', 'guard_name' => $guard]);
        $medic = Role::firstOrCreate(['name' => 'medic', 'guard_name' => $guard]);

        $admin?->syncPermissions(Permission::all());
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
