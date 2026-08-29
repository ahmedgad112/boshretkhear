<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
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
                    'password' => '12345678',
                    'is_active' => true,
                ]
            );

            $user->syncRoles([$data['role']]);
        }
    }
}
