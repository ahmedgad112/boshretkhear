<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\SaleRequest;
use App\Models\Customer;
use App\Models\Property;
use App\Models\Sale;
use App\Services\SaleService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class SaleController extends Controller
{
    public function __construct(private readonly SaleService $service) {}

    public function index(Request $request): Response
    {
        $this->requirePermission('sales.view');

        $sales = Sale::query()
            ->with(['property', 'customer'])
            ->when($request->q, fn ($q, $term) => $q->where('code', 'like', '%'.$term.'%'))
            ->when($request->status, fn ($q, $status) => $q->where('status', $status))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('Admin/Sales/Index', [
            'sales' => $sales,
            'filters' => $request->only(['q', 'status']),
        ]);
    }

    public function create(): Response
    {
        $this->requirePermission('sales.create');

        return Inertia::render('Admin/Sales/Form', $this->formData());
    }

    public function store(SaleRequest $request): RedirectResponse
    {
        $this->requirePermission('sales.create');
        $this->service->create($request->validated());

        return redirect()->route('admin.sales.index')->with('success', 'تم تسجيل عملية البيع بنجاح.');
    }

    public function show(Sale $sale): Response
    {
        $this->requirePermission('sales.view');
        $sale->load(['property', 'customer', 'payments.creator', 'creator']);

        return Inertia::render('Admin/Sales/Show', ['sale' => $sale]);
    }

    public function edit(Sale $sale): Response
    {
        $this->requirePermission('sales.update');

        return Inertia::render('Admin/Sales/Form', [
            ...$this->formData(),
            'sale' => [
                ...$sale->toArray(),
                'sale_date' => $sale->sale_date?->format('Y-m-d'),
            ],
        ]);
    }

    public function update(SaleRequest $request, Sale $sale): RedirectResponse
    {
        $this->requirePermission('sales.update');
        $this->service->update($sale, $request->validated());

        return redirect()->route('admin.sales.index')->with('success', 'تم تحديث عملية البيع بنجاح.');
    }

    public function destroy(Sale $sale): RedirectResponse
    {
        $this->requirePermission('sales.delete');
        $this->service->delete($sale);

        return back()->with('success', 'تم حذف عملية البيع بنجاح.');
    }

    public function complete(Sale $sale): RedirectResponse
    {
        $this->requirePermission('sales.update');
        $this->service->complete($sale);

        return back()->with('success', 'تم إتمام البيع وتحديث حالة العقار إلى مباع.');
    }

    private function formData(): array
    {
        return [
            'properties' => Property::query()->where('status', '!=', 'sold')->orderBy('name')->get(['id', 'name', 'code', 'price']),
            'customers' => Customer::query()->orderBy('name')->get(['id', 'name', 'phone']),
        ];
    }
}
