<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\PaymentRequest;
use App\Models\Booking;
use App\Models\BookingPayment;
use App\Models\Sale;
use App\Models\SalePayment;
use App\Services\PaymentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PaymentController extends Controller
{
    public function __construct(private readonly PaymentService $service) {}

    public function index(Request $request): Response
    {
        $this->requirePermission('payments.view');

        $bookingPayments = BookingPayment::query()
            ->with(['customer', 'property', 'booking', 'creator'])
            ->when($request->q, fn ($q, $term) => $q->where('code', 'like', '%'.$term.'%'))
            ->latest('paid_at')
            ->get()
            ->map(fn (BookingPayment $p) => $this->mapPayment($p, 'حجز', $p->booking?->code));

        $salePayments = SalePayment::query()
            ->with(['customer', 'property', 'sale', 'creator'])
            ->when($request->q, fn ($q, $term) => $q->where('code', 'like', '%'.$term.'%'))
            ->latest('paid_at')
            ->get()
            ->map(fn (SalePayment $p) => $this->mapPayment($p, 'بيع', $p->sale?->code));

        $payments = $bookingPayments->concat($salePayments)->sortByDesc('paid_at')->values();

        $prefillType = in_array($request->source_type, ['booking', 'sale'], true)
            ? $request->source_type
            : null;
        $prefillId = $request->filled('source_id') ? (int) $request->source_id : null;

        $bookings = Booking::query()
            ->with('customer:id,name,phone')
            ->where(function ($query) use ($prefillType, $prefillId) {
                $query->where(function ($inner) {
                    $inner->where('remaining_amount', '>', 0)
                        ->whereNotIn('status', ['cancelled']);
                });

                if ($prefillType === 'booking' && $prefillId) {
                    $query->orWhere('id', $prefillId);
                }
            })
            ->latest()
            ->get(['id', 'code', 'customer_id', 'remaining_amount'])
            ->map(fn (Booking $booking) => [
                'id' => $booking->id,
                'code' => $booking->code,
                'remaining_amount' => $booking->remaining_amount,
                'customer_name' => $booking->customer?->name,
                'customer_phone' => $booking->customer?->phone,
            ]);

        $sales = Sale::query()
            ->with('customer:id,name,phone')
            ->where(function ($query) use ($prefillType, $prefillId) {
                $query->where(function ($inner) {
                    $inner->where('remaining_amount', '>', 0)
                        ->whereNotIn('status', ['cancelled']);
                });

                if ($prefillType === 'sale' && $prefillId) {
                    $query->orWhere('id', $prefillId);
                }
            })
            ->latest()
            ->get(['id', 'code', 'customer_id', 'remaining_amount'])
            ->map(fn (Sale $sale) => [
                'id' => $sale->id,
                'code' => $sale->code,
                'remaining_amount' => $sale->remaining_amount,
                'customer_name' => $sale->customer?->name,
                'customer_phone' => $sale->customer?->phone,
            ]);

        return Inertia::render('Admin/Payments/Index', [
            'payments' => $payments,
            'bookings' => $bookings,
            'sales' => $sales,
            'filters' => $request->only('q'),
            'prefill' => [
                'source_type' => $prefillType,
                'source_id' => $prefillId,
            ],
        ]);
    }

    public function store(PaymentRequest $request): RedirectResponse
    {
        $this->requirePermission('payments.create');

        if ($request->source_type === 'booking') {
            $booking = Booking::query()->findOrFail($request->source_id);
            $this->service->recordBookingPayment($booking, $request->validated());
        } else {
            $sale = Sale::query()->findOrFail($request->source_id);
            $this->service->recordSalePayment($sale, $request->validated());
        }

        return back()->with('success', 'تم تسجيل الدفعة بنجاح.');
    }

    private function mapPayment($payment, string $kind, ?string $reference): array
    {
        return [
            'id' => $payment->id,
            'code' => $payment->code,
            'kind' => $kind,
            'reference' => $reference,
            'customer' => $payment->customer?->name,
            'property' => $payment->property?->name,
            'amount' => $payment->amount,
            'paid_at' => $payment->paid_at?->format('Y-m-d'),
            'payment_method' => $payment->payment_method,
            'reference_number' => $payment->reference_number,
            'creator' => $payment->creator?->name,
            'notes' => $payment->notes,
        ];
    }
}
