<?php

namespace App\Http\Middleware;

use App\Models\Setting;
use App\Services\NotificationService;
use App\Support\Labels;
use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    protected $rootView = 'app';

    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    public function share(Request $request): array
    {
        $user = $request->user();

        return [
            ...parent::share($request),
            'auth' => [
                'user' => $user ? [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone,
                    'roles' => $user->getRoleNames(),
                    'permissions' => $user->getAllPermissions()->pluck('name'),
                ] : null,
            ],
            'settings' => Setting::allValues(),
            'flash' => [
                'success' => $request->session()->get('success'),
                'error' => $request->session()->get('error'),
            ],
            'labels' => [
                'propertyStatuses' => Labels::propertyStatuses(),
                'propertyPurposes' => Labels::propertyPurposes(),
                'rentPeriods' => Labels::rentPeriods(),
                'bookingStatuses' => Labels::bookingStatuses(),
                'saleStatuses' => Labels::saleStatuses(),
                'paymentMethods' => Labels::paymentMethods(),
                'transactionTypes' => Labels::transactionTypes(),
            ],
            'notifications' => $user ? [
                'unread' => app(NotificationService::class)->unreadCount($user->id),
                'items' => app(NotificationService::class)->latest($user->id, 8),
            ] : ['unread' => 0, 'items' => []],
        ];
    }
}
