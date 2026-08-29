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
                'customers.view', 'customers.create', 'customers.update',
                'bookings.view', 'bookings.create', 'bookings.update',
                'sales.view', 'sales.create', 'sales.update',
            ],
            'المحاسب' => [
                'dashboard.view',
                'payments.view', 'payments.create',
                'expenses.view', 'expenses.create', 'expenses.update',
                'accounts.view',
                'reports.view', 'reports.export',
            ],
            'موظف الحجوزات' => [
                'dashboard.view',
                'properties.view',
                'customers.view', 'customers.create',
                'bookings.view', 'bookings.create', 'bookings.update',
                'payments.view', 'payments.create',
            ],
            'موظف عادي' => [
                'dashboard.view',
                'properties.view',
                'customers.view',
            ],
        ];

        foreach ($roles as $name => $perms) {
            $role = Role::findOrCreate($name, 'web');
            $role->syncPermissions($perms);
        }
    }
}
