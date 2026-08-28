<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\BookingPayment;
use App\Models\Sale;
use App\Models\SalePayment;
use App\Models\SystemNotification;
use App\Support\CodeGenerator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PaymentService
{
    public function __construct(
        private readonly ActivityLogger $logger,
        private readonly FinancialService $financial,
    ) {}

    public function recordBookingPayment(Booking $booking, array $data): BookingPayment
    {
        $amount = (float) $data['amount'];

        if ($amount <= 0) {
            throw ValidationException::withMessages(['amount' => 'المبلغ يجب أن يكون أكبر من صفر.']);
        }

        if ($amount > (float) $booking->remaining_amount + 0.009) {
            throw ValidationException::withMessages(['amount' => 'المبلغ أكبر من المتبقي على الحجز.']);
        }

        return DB::transaction(function () use ($booking, $data, $amount) {
            $payment = BookingPayment::query()->create([
                'code' => CodeGenerator::next('دفعة-', BookingPayment::class),
                'booking_id' => $booking->id,
                'customer_id' => $booking->customer_id,
                'property_id' => $booking->property_id,
                'amount' => $amount,
                'paid_at' => $data['paid_at'],
                'payment_method' => $data['payment_method'],
                'reference_number' => $data['reference_number'] ?? null,
                'notes' => $data['notes'] ?? null,
                'created_by' => Auth::id(),
            ]);

            $paid = (float) $booking->payments()->sum('amount');
            $booking->update([
                'paid_amount' => $paid,
                'remaining_amount' => max(0, (float) $booking->total - $paid),
                'payment_method' => $data['payment_method'],
            ]);

            $this->financial->record(
                'customer_payment',
                $amount,
                $data['paid_at'],
                'دفعة على الحجز '.$booking->code,
                $payment,
                $booking->property_id,
                $booking->customer_id,
                $data['notes'] ?? null,
            );

            $this->logger->log('payment.created', 'تم تسجيل دفعة على الحجز '.$booking->code, $payment, [
                'المبلغ' => $amount,
            ]);

            SystemNotification::query()->create([
                'type' => 'payment',
                'title' => 'تم تسجيل عملية دفع جديدة',
                'message' => 'تم تسجيل دفعة بقيمة '.$amount.' على الحجز '.$booking->code,
                'link' => '/admin/payments',
            ]);

            return $payment;
        });
    }

    public function recordSalePayment(Sale $sale, array $data): SalePayment
    {
        $amount = (float) $data['amount'];

        if ($amount <= 0) {
            throw ValidationException::withMessages(['amount' => 'المبلغ يجب أن يكون أكبر من صفر.']);
        }

        if ($amount > (float) $sale->remaining_amount + 0.009) {
            throw ValidationException::withMessages(['amount' => 'المبلغ أكبر من المتبقي على عملية البيع.']);
        }

        return DB::transaction(function () use ($sale, $data, $amount) {
            $payment = SalePayment::query()->create([
                'code' => CodeGenerator::next('دفعةبيع-', SalePayment::class),
                'sale_id' => $sale->id,
                'customer_id' => $sale->customer_id,
                'property_id' => $sale->property_id,
                'amount' => $amount,
                'paid_at' => $data['paid_at'],
                'payment_method' => $data['payment_method'],
                'reference_number' => $data['reference_number'] ?? null,
                'notes' => $data['notes'] ?? null,
                'created_by' => Auth::id(),
            ]);

            $paid = (float) $sale->payments()->sum('amount');
            $sale->update([
                'paid_amount' => $paid,
                'remaining_amount' => max(0, (float) $sale->final_price - $paid),
                'payment_method' => $data['payment_method'],
            ]);

            $this->financial->record(
                'customer_payment',
                $amount,
                $data['paid_at'],
                'دفعة على عملية البيع '.$sale->code,
                $payment,
                $sale->property_id,
                $sale->customer_id,
                $data['notes'] ?? null,
            );

            $this->logger->log('payment.created', 'تم تسجيل دفعة على البيع '.$sale->code, $payment, [
                'المبلغ' => $amount,
            ]);

            SystemNotification::query()->create([
                'type' => 'payment',
                'title' => 'تم تسجيل عملية دفع جديدة',
                'message' => 'تم تسجيل دفعة بقيمة '.$amount.' على عملية البيع '.$sale->code,
                'link' => '/admin/payments',
            ]);

            return $payment;
        });
    }
}
