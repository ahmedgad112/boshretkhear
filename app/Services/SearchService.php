<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\BookingPayment;
use App\Models\Customer;
use App\Models\Expense;
use App\Models\Property;
use App\Models\Sale;
use App\Models\SalePayment;

class SearchService
{
    public function search(string $term): array
    {
        $term = trim($term);

        if (mb_strlen($term) < 2) {
            return [];
        }

        $like = '%'.$term.'%';

        return [
            'properties' => Property::query()
                ->where(fn ($q) => $q->where('name', 'like', $like)->orWhere('code', 'like', $like)->orWhere('city', 'like', $like))
                ->limit(5)
                ->get(['id', 'name', 'code'])
                ->map(fn ($item) => ['id' => $item->id, 'label' => $item->name.' ('.$item->code.')', 'url' => '/admin/properties/'.$item->id]),
            'customers' => Customer::query()
                ->where(fn ($q) => $q->where('name', 'like', $like)->orWhere('phone', 'like', $like)->orWhere('email', 'like', $like))
                ->limit(5)
                ->get(['id', 'name', 'phone'])
                ->map(fn ($item) => ['id' => $item->id, 'label' => $item->name.' - '.$item->phone, 'url' => '/admin/customers/'.$item->id]),
            'bookings' => Booking::query()
                ->where('code', 'like', $like)
                ->limit(5)
                ->get(['id', 'code'])
                ->map(fn ($item) => ['id' => $item->id, 'label' => $item->code, 'url' => '/admin/bookings/'.$item->id]),
            'sales' => Sale::query()
                ->where('code', 'like', $like)
                ->limit(5)
                ->get(['id', 'code'])
                ->map(fn ($item) => ['id' => $item->id, 'label' => $item->code, 'url' => '/admin/sales/'.$item->id]),
            'payments' => BookingPayment::query()
                ->where('code', 'like', $like)
                ->limit(5)
                ->get(['id', 'code'])
                ->map(fn ($item) => ['id' => $item->id, 'label' => $item->code, 'url' => '/admin/payments'])
                ->concat(
                    SalePayment::query()->where('code', 'like', $like)->limit(5)->get(['id', 'code'])
                        ->map(fn ($item) => ['id' => $item->id, 'label' => $item->code, 'url' => '/admin/payments'])
                )->values(),
            'expenses' => Expense::query()
                ->where(fn ($q) => $q->where('code', 'like', $like)->orWhere('description', 'like', $like))
                ->limit(5)
                ->get(['id', 'code', 'description'])
                ->map(fn ($item) => ['id' => $item->id, 'label' => $item->code, 'url' => '/admin/expenses']),
        ];
    }
}
