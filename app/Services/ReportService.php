<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\BookingPayment;
use App\Models\Customer;
use App\Models\Expense;
use App\Models\FinancialTransaction;
use App\Models\Property;
use App\Models\Sale;
use App\Models\SalePayment;
use App\Support\Labels;
use Illuminate\Support\Collection;

class ReportService
{
    public function __construct(private readonly FinancialService $financial) {}

    public function properties(array $filters): Collection
    {
        return $this->applyCommon(Property::query()->with('type'), $filters, 'created_at')
            ->when($filters['property_id'] ?? null, fn ($q, $id) => $q->where('id', $id))
            ->get()
            ->map(fn (Property $item) => [
                'الرمز' => $item->code,
                'الاسم' => $item->name,
                'النوع' => $item->type?->name,
                'الغرض' => Labels::propertyPurpose($item->purpose),
                'الحالة' => Labels::propertyStatus($item->status),
                'المدينة' => $item->city,
                'السعر' => $item->price,
                'سعر الإيجار' => $item->rent_price,
            ]);
    }

    public function bookings(array $filters): Collection
    {
        return $this->applyCommon(
            Booking::query()->with(['property', 'customer', 'creator']),
            $filters,
            'start_date'
        )
            ->when($filters['property_id'] ?? null, fn ($q, $id) => $q->where('property_id', $id))
            ->when($filters['customer_id'] ?? null, fn ($q, $id) => $q->where('customer_id', $id))
            ->when($filters['user_id'] ?? null, fn ($q, $id) => $q->where('created_by', $id))
            ->get()
            ->map(fn (Booking $item) => [
                'الرمز' => $item->code,
                'العقار' => $item->property?->name,
                'العميل' => $item->customer?->name,
                'من' => $item->start_date?->format('Y-m-d'),
                'إلى' => $item->end_date?->format('Y-m-d'),
                'الليالي' => $item->nights,
                'الإجمالي' => $item->total,
                'المدفوع' => $item->paid_amount,
                'المتبقي' => $item->remaining_amount,
                'الحالة' => Labels::bookingStatus($item->status),
            ]);
    }

    public function sales(array $filters): Collection
    {
        return $this->applyCommon(Sale::query()->with(['property', 'customer']), $filters, 'sale_date')
            ->when($filters['property_id'] ?? null, fn ($q, $id) => $q->where('property_id', $id))
            ->when($filters['customer_id'] ?? null, fn ($q, $id) => $q->where('customer_id', $id))
            ->when($filters['user_id'] ?? null, fn ($q, $id) => $q->where('created_by', $id))
            ->get()
            ->map(fn (Sale $item) => [
                'الرمز' => $item->code,
                'العقار' => $item->property?->name,
                'العميل' => $item->customer?->name,
                'التاريخ' => $item->sale_date?->format('Y-m-d'),
                'السعر النهائي' => $item->final_price,
                'المدفوع' => $item->paid_amount,
                'المتبقي' => $item->remaining_amount,
                'الحالة' => Labels::saleStatus($item->status),
            ]);
    }

    public function customers(array $filters): Collection
    {
        return $this->applyCommon(Customer::query(), $filters, 'created_at')
            ->when($filters['customer_id'] ?? null, fn ($q, $id) => $q->where('id', $id))
            ->get()
            ->map(fn (Customer $item) => [
                'الاسم' => $item->name,
                'الهاتف' => $item->phone,
                'البريد' => $item->email,
                'المستحق' => $item->due_amount,
                'إجمالي المدفوع' => $item->total_paid,
            ]);
    }

    public function payments(array $filters): Collection
    {
        $bookings = $this->applyCommon(BookingPayment::query()->with(['customer', 'property', 'booking']), $filters, 'paid_at')
            ->when($filters['property_id'] ?? null, fn ($q, $id) => $q->where('property_id', $id))
            ->when($filters['customer_id'] ?? null, fn ($q, $id) => $q->where('customer_id', $id))
            ->when($filters['user_id'] ?? null, fn ($q, $id) => $q->where('created_by', $id))
            ->get()
            ->map(fn (BookingPayment $item) => [
                'الرمز' => $item->code,
                'النوع' => 'دفعة إيجار',
                'العميل' => $item->customer?->name,
                'العقار' => $item->property?->name,
                'المبلغ' => $item->amount,
                'التاريخ' => $item->paid_at?->format('Y-m-d'),
                'طريقة الدفع' => Labels::paymentMethod($item->payment_method),
            ]);

        $sales = $this->applyCommon(SalePayment::query()->with(['customer', 'property']), $filters, 'paid_at')
            ->when($filters['property_id'] ?? null, fn ($q, $id) => $q->where('property_id', $id))
            ->when($filters['customer_id'] ?? null, fn ($q, $id) => $q->where('customer_id', $id))
            ->when($filters['user_id'] ?? null, fn ($q, $id) => $q->where('created_by', $id))
            ->get()
            ->map(fn (SalePayment $item) => [
                'الرمز' => $item->code,
                'النوع' => 'دفعة بيع',
                'العميل' => $item->customer?->name,
                'العقار' => $item->property?->name,
                'المبلغ' => $item->amount,
                'التاريخ' => $item->paid_at?->format('Y-m-d'),
                'طريقة الدفع' => Labels::paymentMethod($item->payment_method),
            ]);

        return $bookings->concat($sales)->values();
    }

    public function expenses(array $filters): Collection
    {
        return $this->applyCommon(Expense::query()->with(['category', 'property', 'creator']), $filters, 'expense_date')
            ->when($filters['property_id'] ?? null, fn ($q, $id) => $q->where('property_id', $id))
            ->when($filters['user_id'] ?? null, fn ($q, $id) => $q->where('created_by', $id))
            ->get()
            ->map(fn (Expense $item) => [
                'الرمز' => $item->code,
                'النوع' => $item->category?->name,
                'العقار' => $item->property?->name,
                'المبلغ' => $item->amount,
                'التاريخ' => $item->expense_date?->format('Y-m-d'),
                'الوصف' => $item->description,
            ]);
    }

    public function revenues(array $filters): Collection
    {
        return $this->transactions($filters, ['rent_income', 'sale_income', 'customer_payment']);
    }

    public function profits(array $filters): array
    {
        $summary = $this->financial->summary(
            $filters['from'] ?? null,
            $filters['to'] ?? null,
            $filters['property_id'] ?? null,
        );

        return collect([$summary])->map(fn ($item) => [
            'إجمالي الإيرادات' => $item['revenues'],
            'إجمالي المصروفات' => $item['expenses'],
            'صافي الأرباح' => $item['net'],
            'إيرادات الإيجارات' => $item['rent_income'],
            'إيرادات المبيعات' => $item['sale_income'],
        ])->all();
    }

    public function dues(array $filters): Collection
    {
        $bookings = Booking::query()
            ->with(['customer', 'property'])
            ->where('remaining_amount', '>', 0)
            ->whereNotIn('status', ['cancelled'])
            ->when($filters['property_id'] ?? null, fn ($q, $id) => $q->where('property_id', $id))
            ->when($filters['customer_id'] ?? null, fn ($q, $id) => $q->where('customer_id', $id))
            ->get()
            ->map(fn (Booking $item) => [
                'النوع' => 'حجز',
                'الرمز' => $item->code,
                'العميل' => $item->customer?->name,
                'العقار' => $item->property?->name,
                'المتبقي' => $item->remaining_amount,
            ]);

        $sales = Sale::query()
            ->with(['customer', 'property'])
            ->where('remaining_amount', '>', 0)
            ->whereNotIn('status', ['cancelled'])
            ->when($filters['property_id'] ?? null, fn ($q, $id) => $q->where('property_id', $id))
            ->when($filters['customer_id'] ?? null, fn ($q, $id) => $q->where('customer_id', $id))
            ->get()
            ->map(fn (Sale $item) => [
                'النوع' => 'بيع',
                'الرمز' => $item->code,
                'العميل' => $item->customer?->name,
                'العقار' => $item->property?->name,
                'المتبقي' => $item->remaining_amount,
            ]);

        return $bookings->concat($sales)->values();
    }

    public function transactions(array $filters, ?array $types = null): Collection
    {
        return $this->applyCommon(FinancialTransaction::query()->with(['property', 'customer', 'user']), $filters, 'transaction_date')
            ->when($types, fn ($q) => $q->whereIn('type', $types))
            ->when($filters['type'] ?? null, fn ($q, $type) => $q->where('type', $type))
            ->when($filters['property_id'] ?? null, fn ($q, $id) => $q->where('property_id', $id))
            ->when($filters['customer_id'] ?? null, fn ($q, $id) => $q->where('customer_id', $id))
            ->when($filters['user_id'] ?? null, fn ($q, $id) => $q->where('user_id', $id))
            ->orderByDesc('transaction_date')
            ->get()
            ->map(fn (FinancialTransaction $item) => [
                'الرمز' => $item->code,
                'النوع' => Labels::transactionType($item->type),
                'المبلغ' => $item->amount,
                'التاريخ' => $item->transaction_date?->format('Y-m-d'),
                'العقار' => $item->property?->name,
                'العميل' => $item->customer?->name,
                'المستخدم' => $item->user?->name,
                'الوصف' => $item->description,
            ]);
    }

    private function applyCommon($query, array $filters, string $dateColumn)
    {
        return $query
            ->when($filters['from'] ?? null, fn ($q, $from) => $q->whereDate($dateColumn, '>=', $from))
            ->when($filters['to'] ?? null, fn ($q, $to) => $q->whereDate($dateColumn, '<=', $to));
    }
}
