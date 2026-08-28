<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Customer;
use App\Models\Expense;
use App\Models\FinancialTransaction;
use App\Models\Property;
use App\Models\Sale;
use App\Services\BookingService;
use App\Services\FinancialService;
use App\Services\NotificationService;
use Illuminate\Support\Carbon;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __invoke(
        BookingService $bookings,
        FinancialService $financial,
        NotificationService $notifications,
    ): Response {
        $this->requirePermission('dashboard.view');

        $bookings->refreshExpiredBookings();
        $notifications->refresh();

        $summary = $financial->summary();
        $due = (float) Booking::query()->whereNotIn('status', ['cancelled'])->sum('remaining_amount')
            + (float) Sale::query()->whereNotIn('status', ['cancelled'])->sum('remaining_amount');

        $months = collect(range(5, 0))->map(function ($ago) {
            $date = now()->subMonths($ago);

            $monthsAr = [1 => 'يناير', 2 => 'فبراير', 3 => 'مارس', 4 => 'أبريل', 5 => 'مايو', 6 => 'يونيو', 7 => 'يوليو', 8 => 'أغسطس', 9 => 'سبتمبر', 10 => 'أكتوبر', 11 => 'نوفمبر', 12 => 'ديسمبر'];

            return [
                'key' => $date->format('Y-m'),
                'label' => $monthsAr[(int) $date->format('n')],
            ];
        });

        $monthly = $months->map(function ($month) {
            $start = Carbon::parse($month['key'].'-01')->startOfMonth();
            $end = (clone $start)->endOfMonth();

            return [
                'label' => $month['label'],
                'revenues' => (float) FinancialTransaction::query()
                    ->whereIn('type', ['rent_income', 'sale_income', 'customer_payment'])
                    ->whereBetween('transaction_date', [$start, $end])
                    ->sum('amount'),
                'expenses' => (float) FinancialTransaction::query()
                    ->whereIn('type', ['expense', 'refund'])
                    ->whereBetween('transaction_date', [$start, $end])
                    ->sum('amount'),
                'rentals' => Booking::query()->whereBetween('start_date', [$start, $end])->count(),
                'sales' => Sale::query()->whereBetween('sale_date', [$start, $end])->count(),
            ];
        });

        $topProperties = Property::query()
            ->withSum(['financialTransactions as income' => function ($q) {
                $q->whereIn('type', ['rent_income', 'sale_income', 'customer_payment']);
            }], 'amount')
            ->orderByDesc('income')
            ->limit(5)
            ->get(['id', 'name', 'code'])
            ->map(fn ($p) => [
                'name' => $p->name,
                'income' => (float) ($p->income ?? 0),
            ]);

        return Inertia::render('Admin/Dashboard', [
            'stats' => [
                'properties_total' => Property::query()->count(),
                'properties_available' => Property::query()->where('status', 'available')->count(),
                'properties_rented' => Property::query()->where('status', 'rented')->count(),
                'properties_sold' => Property::query()->where('status', 'sold')->count(),
                'properties_reserved' => Property::query()->where('status', 'reserved')->count(),
                'customers' => Customer::query()->count(),
                'bookings' => Booking::query()->count(),
                'revenues' => $summary['revenues'],
                'expenses' => $summary['expenses'],
                'net' => $summary['net'],
                'due' => $due,
            ],
            'charts' => [
                'monthly' => $monthly->values(),
                'topProperties' => $topProperties->values(),
            ],
        ]);
    }
}
