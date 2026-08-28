<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ExpenseRequest;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\Property;
use App\Services\ExpenseService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ExpenseController extends Controller
{
    public function __construct(private readonly ExpenseService $service) {}

    public function index(Request $request): Response
    {
        $this->requirePermission('expenses.view');

        $expenses = Expense::query()
            ->with(['category', 'property', 'creator'])
            ->when($request->q, fn ($q, $term) => $q->where(function ($inner) use ($term) {
                $inner->where('code', 'like', '%'.$term.'%')->orWhere('description', 'like', '%'.$term.'%');
            }))
            ->latest('expense_date')
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('Admin/Expenses/Index', [
            'expenses' => $expenses,
            'categories' => ExpenseCategory::query()->where('is_active', true)->orderBy('name')->get(['id', 'name']),
            'properties' => Property::query()->orderBy('name')->get(['id', 'name']),
            'filters' => $request->only('q'),
        ]);
    }

    public function store(ExpenseRequest $request): RedirectResponse
    {
        $this->requirePermission('expenses.create');
        $this->service->create($request->validated());

        return back()->with('success', 'تم إضافة المصروف بنجاح.');
    }

    public function update(ExpenseRequest $request, Expense $expense): RedirectResponse
    {
        $this->requirePermission('expenses.update');
        $this->service->update($expense, $request->validated());

        return back()->with('success', 'تم تحديث المصروف بنجاح.');
    }

    public function destroy(Expense $expense): RedirectResponse
    {
        $this->requirePermission('expenses.delete');
        $this->service->delete($expense);

        return back()->with('success', 'تم حذف المصروف بنجاح.');
    }
}
