<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\SettingRequest;
use App\Models\Setting;
use App\Services\ActivityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Cache;
use Inertia\Inertia;
use Inertia\Response;

class SettingController extends Controller
{
    public function __construct(private readonly ActivityLogger $logger) {}

    public function edit(): Response
    {
        $this->requirePermission('settings.view');

        return Inertia::render('Admin/Settings/Index', [
            'values' => Setting::allValues(),
        ]);
    }

    public function update(SettingRequest $request): RedirectResponse
    {
        $this->requirePermission('settings.update');

        foreach ($request->except(['logo', '_method']) as $key => $value) {
            Setting::setValue($key, is_bool($value) ? ($value ? '1' : '0') : $value);
        }

        if ($request->hasFile('logo')) {
            $path = $request->file('logo')->store('settings', 'public');
            Setting::setValue('logo', $path);
        }

        Cache::forget('app_settings');
        $this->logger->log('settings.updated', 'تم تحديث إعدادات النظام');

        return back()->with('success', 'تم حفظ الإعدادات بنجاح.');
    }
}
