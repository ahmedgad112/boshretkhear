<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Property;
use App\Models\Sale;
use App\Models\SystemNotification;

class NotificationService
{
    public function refresh(): void
    {
        $today = now()->toDateString();

        $this->notifyDueAmounts();
        $this->notifyBookingsStartingToday($today);
        $this->notifyRentalsEndingToday($today);
        $this->notifyPropertiesNeedingStatus($today);
    }

    public function unreadCount(?int $userId = null): int
    {
        return SystemNotification::query()
            ->unread()
            ->where(function ($query) use ($userId) {
                $query->whereNull('user_id');
                if ($userId) {
                    $query->orWhere('user_id', $userId);
                }
            })
            ->count();
    }

    public function latest(?int $userId = null, int $limit = 12)
    {
        return SystemNotification::query()
            ->where(function ($query) use ($userId) {
                $query->whereNull('user_id');
                if ($userId) {
                    $query->orWhere('user_id', $userId);
                }
            })
            ->latest()
            ->limit($limit)
            ->get();
    }

    public function markAsRead(SystemNotification $notification): void
    {
        $notification->update(['read_at' => now()]);
    }

    public function markAllAsRead(?int $userId = null): void
    {
        SystemNotification::query()
            ->unread()
            ->where(function ($query) use ($userId) {
                $query->whereNull('user_id');
                if ($userId) {
                    $query->orWhere('user_id', $userId);
                }
            })
            ->update(['read_at' => now()]);
    }

    private function notifyDueAmounts(): void
    {
        $bookingDue = Booking::query()
            ->whereNotIn('status', ['cancelled'])
            ->where('remaining_amount', '>', 0)
            ->count();

        $saleDue = Sale::query()
            ->whereNotIn('status', ['cancelled'])
            ->where('remaining_amount', '>', 0)
            ->count();

        if ($bookingDue + $saleDue === 0) {
            return;
        }

        $this->onceToday(
            'due_amount',
            'يوجد مبلغ مستحق من عميل',
            'هناك '.($bookingDue + $saleDue).' عملية عليها مبالغ مستحقة.',
            '/admin/accounts',
        );
    }

    private function notifyBookingsStartingToday(string $today): void
    {
        $count = Booking::query()
            ->whereIn('status', ['pending', 'confirmed'])
            ->whereDate('start_date', $today)
            ->count();

        if ($count === 0) {
            return;
        }

        $this->onceToday(
            'booking_start',
            'يوجد حجز يبدأ اليوم',
            'عدد الحجوزات التي تبدأ اليوم: '.$count,
            '/admin/bookings',
        );
    }

    private function notifyRentalsEndingToday(string $today): void
    {
        $count = Booking::query()
            ->whereIn('status', ['confirmed', 'active'])
            ->whereDate('end_date', $today)
            ->count();

        if ($count === 0) {
            return;
        }

        $this->onceToday(
            'rental_end',
            'يوجد إيجار ينتهي اليوم',
            'عدد الإيجارات التي تنتهي اليوم: '.$count,
            '/admin/rentals',
        );
    }

    private function notifyPropertiesNeedingStatus(string $today): void
    {
        $ended = Booking::query()
            ->whereIn('status', ['confirmed', 'active'])
            ->whereDate('end_date', '<=', $today)
            ->count();

        $soldMismatch = Property::query()
            ->where('status', '!=', 'sold')
            ->whereHas('sales', fn ($query) => $query->where('status', 'completed'))
            ->count();

        if ($ended + $soldMismatch === 0) {
            return;
        }

        $this->onceToday(
            'property_status',
            'يوجد عقار يحتاج إلى تحديث حالته',
            'هناك عقارات تحتاج مراجعة الحالة بعد انتهاء الإيجار أو إتمام البيع.',
            '/admin/properties',
        );
    }

    private function onceToday(string $type, string $title, string $message, string $link): void
    {
        $exists = SystemNotification::query()
            ->where('type', $type)
            ->whereDate('created_at', now()->toDateString())
            ->exists();

        if ($exists) {
            return;
        }

        SystemNotification::query()->create([
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'link' => $link,
        ]);
    }
}
