<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            ['name' => 'أحمد المدير', 'email' => 'admin@gmail.com', 'phone' => '01000000001', 'role' => 'المدير العام'],
            ['name' => 'سارة العقارية', 'email' => 'properties@gmail.com', 'phone' => '01000000002', 'role' => 'مدير العقارات'],
            ['name' => 'خالد الموظف', 'email' => 'staff@gmail.com', 'phone' => '01000000003', 'role' => 'موظف عادي'],
        ];

        $seedEmails = collect($users)->pluck('email');

        foreach ($users as $data) {
            $user = User::withTrashed()->updateOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'phone' => $data['phone'],
                    'password' => '123456789',
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
