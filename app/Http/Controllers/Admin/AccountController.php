<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\FinancialTransaction;
use App\Models\Property;
use App\Services\FinancialService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AccountController extends Controller
{
    public function __invoke(Request $request, FinancialService $financial): Response
    {
        $this->requirePermission('accounts.view');

        $filters = $request->only(['from', 'to', 'property_id', 'customer_id', 'type']);

        $transactions = FinancialTransaction::query()
            ->with(['property', 'customer', 'user'])
            ->when($filters['from'] ?? null, fn ($q, $from) => $q->whereDate('transaction_date', '>=', $from))
            ->when($filters['to'] ?? null, fn ($q, $to) => $q->whereDate('transaction_date', '<=', $to))
            ->when($filters['property_id'] ?? null, fn ($q, $id) => $q->where('property_id', $id))
            ->when($filters['customer_id'] ?? null, fn ($q, $id) => $q->where('customer_id', $id))
            ->when($filters['type'] ?? null, fn ($q, $type) => $q->where('type', $type))
            ->orderByDesc('transaction_date')
            ->orderByDesc('id')
            ->paginate(20)
            ->withQueryString();

        $summary = $financial->summary($filters['from'] ?? null, $filters['to'] ?? null, $filters['property_id'] ?? null);

        $due = (float) \App\Models\Booking::query()->whereNotIn('status', ['cancelled'])->sum('remaining_amount')
            + (float) \App\Models\Sale::query()->whereNotIn('status', ['cancelled'])->sum('remaining_amount');

        $propertyAccounts = Property::query()
            ->withSum(['financialTransactions as income' => fn ($q) => $q->whereIn('type', ['rent_income', 'sale_income', 'customer_payment'])], 'amount')
            ->withSum(['financialTransactions as costs' => fn ($q) => $q->whereIn('type', ['expense', 'refund'])], 'amount')
            ->orderBy('name')
            ->get()
            ->map(fn ($p) => [
                'id' => $p->id,
                'name' => $p->name,
                'income' => (float) ($p->income ?? 0),
                'costs' => (float) ($p->costs ?? 0),
                'net' => (float) ($p->income ?? 0) - (float) ($p->costs ?? 0),
            ]);

        return Inertia::render('Admin/Accounts/Index', [
            'transactions' => $transactions,
            'summary' => [...$summary, 'due' => $due],
            'propertyAccounts' => $propertyAccounts,
            'filters' => $filters,
            'properties' => Property::query()->orderBy('name')->get(['id', 'name']),
            'customers' => Customer::query()->orderBy('name')->get(['id', 'name']),
        ]);
    }
}
