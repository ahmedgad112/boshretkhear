<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\CustomerRequest;
use App\Models\Customer;
use App\Services\ActivityLogger;
use App\Support\CodeGenerator;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class CustomerController extends Controller
{
    public function __construct(private readonly ActivityLogger $logger) {}

    public function index(Request $request): Response
    {
        $this->requirePermission('customers.view');

        $customers = Customer::query()
            ->when($request->q, fn ($q, $term) => $q->where(function ($inner) use ($term) {
                $inner->where('name', 'like', '%'.$term.'%')
                    ->orWhere('phone', 'like', '%'.$term.'%')
                    ->orWhere('national_id', 'like', '%'.$term.'%');
            }))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('Admin/Customers/Index', [
            'customers' => $customers,
            'filters' => $request->only('q'),
        ]);
    }

    public function create(): Response
    {
        $this->requirePermission('customers.create');

        return Inertia::render('Admin/Customers/Form');
    }

    public function store(CustomerRequest $request): RedirectResponse
    {
        $this->requirePermission('customers.create');

        $customer = Customer::query()->create([
            ...$request->safe()->except(['id_card_image', 'remove_id_card']),
            'created_by' => Auth::id(),
        ]);

        if ($request->hasFile('id_card_image')) {
            $this->storeIdCard($customer, $request->file('id_card_image'));
        }

        $this->logger->log('customer.created', 'تم إضافة عميل: '.$customer->name, $customer);

        return redirect()->route('admin.customers.index')->with('success', 'تم إضافة العميل بنجاح.');
    }

    public function show(Customer $customer): Response
    {
        $this->requirePermission('customers.view');

        $customer->load([
            'bookings.property',
            'sales.property',
            'bookingPayments.property',
            'salePayments.property',
        ]);

        return Inertia::render('Admin/Customers/Show', [
            'customer' => [
                ...$customer->toArray(),
                'id_card_url' => $customer->id_card_url,
            ],
            'rented' => $customer->bookings->whereIn('status', ['confirmed', 'active', 'completed'])->values(),
            'purchased' => $customer->sales->where('status', 'completed')->values(),
            'due' => $customer->due_amount,
            'total_paid' => $customer->total_paid,
            'total_deals' => (float) $customer->bookings->sum('total') + (float) $customer->sales->sum('final_price'),
        ]);
    }

    public function edit(Customer $customer): Response
    {
        $this->requirePermission('customers.update');

        return Inertia::render('Admin/Customers/Form', [
            'customer' => [
                ...$customer->toArray(),
                'id_card_url' => $customer->id_card_url,
            ],
        ]);
    }

    public function update(CustomerRequest $request, Customer $customer): RedirectResponse
    {
        $this->requirePermission('customers.update');

        $customer->update($request->safe()->except(['id_card_image', 'remove_id_card']));

        if ($request->boolean('remove_id_card')) {
            $this->deleteIdCard($customer);
        }

        if ($request->hasFile('id_card_image')) {
            $this->deleteIdCard($customer);
            $this->storeIdCard($customer, $request->file('id_card_image'));
        }

        $this->logger->log('customer.updated', 'تم تعديل العميل: '.$customer->name, $customer);

        return redirect()->route('admin.customers.index')->with('success', 'تم تحديث بيانات العميل بنجاح.');
    }

    public function destroy(Customer $customer): RedirectResponse
    {
        $this->requirePermission('customers.delete');
        $this->deleteIdCard($customer);
        $customer->delete();
        $this->logger->log('customer.deleted', 'تم حذف العميل: '.$customer->name, $customer);

        return back()->with('success', 'تم حذف العميل بنجاح.');
    }

    private function storeIdCard(Customer $customer, UploadedFile $file): void
    {
        $path = $file->storeAs(
            'customers/'.$customer->id,
            CodeGenerator::uniqueFileName($file->getClientOriginalName()),
            'public',
        );

        $customer->update(['id_card_path' => $path]);
    }

    private function deleteIdCard(Customer $customer): void
    {
        if (! $customer->id_card_path) {
            return;
        }

        Storage::disk('public')->delete($customer->id_card_path);
        $customer->update(['id_card_path' => null]);
    }
}
