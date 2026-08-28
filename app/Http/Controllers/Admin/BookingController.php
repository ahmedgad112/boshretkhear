<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\BookingRequest;
use App\Models\Booking;
use App\Models\Customer;
use App\Models\Property;
use App\Models\Setting;
use App\Services\BookingService;
use App\Support\Labels;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Inertia\Inertia;
use Inertia\Response;

class BookingController extends Controller
{
    public function __construct(private readonly BookingService $service) {}

    public function index(Request $request): Response
    {
        $this->requirePermission('bookings.view');

        $bookings = Booking::query()
            ->with(['property', 'customer'])
            ->when($request->q, fn ($q, $term) => $q->where('code', 'like', '%'.$term.'%'))
            ->when($request->status, fn ($q, $status) => $q->where('status', $status))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('Admin/Bookings/Index', [
            'bookings' => $bookings,
            'filters' => $request->only(['q', 'status']),
        ]);
    }

    public function create(): Response
    {
        $this->requirePermission('bookings.create');

        return Inertia::render('Admin/Bookings/Form', $this->formData());
    }

    public function store(BookingRequest $request): RedirectResponse
    {
        $this->requirePermission('bookings.create');
        $this->service->create($request->validated());

        return redirect()->route('admin.bookings.index')->with('success', 'تم إنشاء الحجز بنجاح.');
    }

    public function show(Booking $booking): Response
    {
        $this->requirePermission('bookings.view');
        $booking->load(['property', 'customer', 'payments.creator', 'creator']);

        return Inertia::render('Admin/Bookings/Show', [
            'booking' => [
                ...$booking->toArray(),
                'start_date' => $booking->start_date?->format('Y-m-d'),
                'end_date' => $booking->end_date?->format('Y-m-d'),
                'payments' => $booking->payments->map(fn ($payment) => [
                    ...$payment->toArray(),
                    'paid_at' => $payment->paid_at instanceof \Carbon\CarbonInterface
                        ? $payment->paid_at->format('Y-m-d')
                        : \Illuminate\Support\Carbon::parse($payment->paid_at)->format('Y-m-d'),
                ]),
            ],
        ]);
    }

    public function edit(Booking $booking): Response
    {
        $this->requirePermission('bookings.update');

        return Inertia::render('Admin/Bookings/Form', [
            ...$this->formData(),
            'booking' => [
                ...$booking->toArray(),
                'start_date' => $booking->start_date?->format('Y-m-d'),
                'end_date' => $booking->end_date?->format('Y-m-d'),
            ],
        ]);
    }

    public function update(BookingRequest $request, Booking $booking): RedirectResponse
    {
        $this->requirePermission('bookings.update');
        $this->service->update($booking, $request->validated());

        return redirect()->route('admin.bookings.index')->with('success', 'تم تحديث الحجز بنجاح.');
    }

    public function destroy(Booking $booking): RedirectResponse
    {
        $this->requirePermission('bookings.delete');
        $this->service->delete($booking);

        return back()->with('success', 'تم حذف الحجز بنجاح.');
    }

    public function changeStatus(Request $request, Booking $booking): RedirectResponse
    {
        $this->requirePermission('bookings.update');
        $request->validate(['status' => 'required|in:pending,confirmed,active,completed,cancelled']);
        $this->service->changeStatus($booking, $request->string('status'));

        return back()->with('success', 'تم تغيير حالة الحجز بنجاح.');
    }

    public function contract(Booking $booking): View
    {
        $this->requirePermission('bookings.view');

        $booking->load(['property.type', 'customer', 'payments']);

        return view('contracts.rental', [
            'booking' => $booking,
            'settings' => Setting::allValues(),
            'currency' => Setting::getValue('currency', 'جنيه'),
            'statusLabel' => Labels::bookingStatus($booking->status),
            'paymentMethod' => Labels::paymentMethod($booking->payment_method),
            'printedAt' => now()->format('Y-m-d H:i'),
        ]);
    }

    private function formData(): array
    {
        return [
            'properties' => Property::query()->where('status', '!=', 'sold')->orderBy('name')->get(['id', 'name', 'code', 'rent_price']),
            'customers' => Customer::query()->orderBy('name')->get(['id', 'name', 'phone']),
        ];
    }
}
