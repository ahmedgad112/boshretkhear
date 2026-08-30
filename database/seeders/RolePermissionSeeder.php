<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = collect(config('permissions.groups'))->flatMap(fn ($group) => array_keys($group));

        foreach ($permissions as $permission) {
            Permission::findOrCreate($permission, 'web');
        }

        $roles = [
            'المدير العام' => $permissions->all(),
            'مدير العقارات' => [
                'dashboard.view',
                'properties.view', 'properties.create', 'properties.update', 'properties.delete', 'properties.change_status',
                'property_types.view', 'property_types.create', 'property_types.update',
            ],
            'موظف عادي' => [
                'dashboard.view',
                'properties.view',
            ],
        ];

        foreach ($roles as $name => $perms) {
            $role = Role::findOrCreate($name, 'web');
            $role->syncPermissions($perms);
        }
    }
}
