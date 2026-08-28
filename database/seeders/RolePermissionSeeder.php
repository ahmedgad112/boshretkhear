<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
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

        $users = [
            ['name' => 'أحمد المدير', 'email' => 'admin@boshret.test', 'phone' => '01000000001', 'role' => 'المدير العام'],
            ['name' => 'سارة العقارية', 'email' => 'properties@boshret.test', 'phone' => '01000000002', 'role' => 'مدير العقارات'],
            ['name' => 'محمود المحاسب', 'email' => 'accountant@boshret.test', 'phone' => '01000000003', 'role' => 'المحاسب'],
            ['name' => 'نورا الحجوزات', 'email' => 'bookings@boshret.test', 'phone' => '01000000004', 'role' => 'موظف الحجوزات'],
            ['name' => 'خالد الموظف', 'email' => 'staff@boshret.test', 'phone' => '01000000005', 'role' => 'موظف عادي'],
        ];

        foreach ($users as $data) {
            $user = User::query()->updateOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'phone' => $data['phone'],
                    'password' => Hash::make('12345678'),
                    'is_active' => true,
                ]
            );
            $user->syncRoles([$data['role']]);
        }
    }
}
