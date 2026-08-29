<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        if (Role::query()->where('guard_name', 'web')->doesntExist()) {
            $this->call(RolePermissionSeeder::class);
        }

        $users = [
            ['name' => 'أحمد المدير', 'email' => 'admin@boshret.test', 'phone' => '01000000001', 'role' => 'المدير العام'],
            ['name' => 'سارة العقارية', 'email' => 'properties@boshret.test', 'phone' => '01000000002', 'role' => 'مدير العقارات'],
            ['name' => 'محمود المحاسب', 'email' => 'accountant@boshret.test', 'phone' => '01000000003', 'role' => 'المحاسب'],
            ['name' => 'نورا الحجوزات', 'email' => 'bookings@boshret.test', 'phone' => '01000000004', 'role' => 'موظف الحجوزات'],
            ['name' => 'خالد الموظف', 'email' => 'staff@boshret.test', 'phone' => '01000000005', 'role' => 'موظف عادي'],
        ];

        $seedEmails = collect($users)->pluck('email');

        foreach ($users as $data) {
            $user = User::withTrashed()->updateOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'phone' => $data['phone'],
                    'password' => '12345678',
                    'is_active' => true,
                    'deleted_at' => null,
                ]
            );

            $user->syncRoles([$data['role']]);
        }

        User::query()
            ->whereNotIn('email', $seedEmails)
            ->delete();
    }
}
