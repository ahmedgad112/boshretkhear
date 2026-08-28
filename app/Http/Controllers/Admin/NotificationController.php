<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SystemNotification;
use App\Services\NotificationService;
use Illuminate\Http\RedirectResponse;

class NotificationController extends Controller
{
    public function markAsRead(SystemNotification $notification, NotificationService $service): RedirectResponse
    {
        $service->markAsRead($notification);

        return $notification->link ? redirect($notification->link) : back();
    }

    public function markAll(NotificationService $service): RedirectResponse
    {
        $service->markAllAsRead(auth()->id());

        return back()->with('success', 'تم تعليم جميع الإشعارات كمقروءة.');
    }
}
