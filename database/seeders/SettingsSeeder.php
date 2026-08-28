<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            'business_name' => 'بشرى خير',
            'phone' => '01012345678',
            'email' => 'info@boshret.test',
            'address' => 'شارع التحرير، القاهرة',
            'contact_info' => 'نعمل يوميًا من العاشرة صباحًا حتى العاشرة مساءً',
            'currency' => 'جنيه',
            'default_rent_period' => 'nightly',
            'notify_due_amounts' => '1',
            'notify_bookings' => '1',
            'about' => 'منصة بشرى خير لإدارة وعرض العقارات بكل احترافية ووضوح.',
        ];

        foreach ($settings as $key => $value) {
            Setting::query()->updateOrCreate(['key' => $key], ['value' => $value]);
        }
    }
}
