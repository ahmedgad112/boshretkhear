<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Property;
use App\Support\CodeGenerator;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class BookingService
{
    public function __construct(
        private readonly ActivityLogger $logger,
        private readonly PaymentService $payments,
    ) {}

    public function calculate(array $data): array
    {
        $start = Carbon::parse($data['start_date'])->startOfDay();
        $end = Carbon::parse($data['end_date'])->startOfDay();

        if ($end->lte($start)) {
            throw ValidationException::withMessages([
                'end_date' => 'تاريخ النهاية يجب أن يكون بعد تاريخ البداية.',
            ]);
        }

        $nights = $start->diffInDays($end);
        $rate = (float) ($data['nightly_rate'] ?? 0);
        $discountType = $data['discount_type'] ?? 'amount';
        $discountValue = (float) ($data['discount_value'] ?? $data['discount'] ?? 0);
        $extraType = $data['extra_type'] ?? 'amount';
        $extraValue = (float) ($data['extra_value'] ?? $data['extra_amount'] ?? 0);
        $rentAmount = $nights * $rate;

        if ($discountType === 'percent') {
            if ($discountValue > 100) {
                throw ValidationException::withMessages([
                    'discount_value' => 'نسبة الخصم يجب ألا تتجاوز 100%.',
                ]);
            }

            $discount = $rentAmount * ($discountValue / 100);
        } else {
            $discount = $discountValue;
        }

        if ($extraType === 'percent') {
            if ($extraValue > 100) {
                throw ValidationException::withMessages([
                    'extra_value' => 'النسبة المئوية يجب ألا تتجاوز 100%.',
                ]);
            }

            $extra = $rentAmount * ($extraValue / 100);
        } else {
            $extra = $extraValue;
        }

        $total = $rentAmount + $extra - $discount;

        if ($total < 0) {
            throw ValidationException::withMessages([
                'discount_value' => 'الخصم أكبر من قيمة الإيجار والمبلغ الإضافي.',
            ]);
        }

        return [
            'nights' => $nights,
            'rent_amount' => round($rentAmount, 2),
            'discount_type' => $discountType,
            'discount_value' => round($discountValue, 2),
            'discount' => round($discount, 2),
            'extra_type' => $extraType,
            'extra_value' => round($extraValue, 2),
            'extra_amount' => round($extra, 2),
            'total' => round($total, 2),
        ];
    }

    public function assertNoOverlap(int $propertyId, string $start, string $end, ?int $ignoreId = null): void
    {
        $exists = Booking::query()
            ->where('property_id', $propertyId)
            ->whereIn('status', ['pending', 'confirmed', 'active'])
            ->where('start_date', '<', $end)
            ->where('end_date', '>', $start)
            ->when($ignoreId, fn ($query) => $query->where('id', '!=', $ignoreId))
            ->exists();

        if ($exists) {
            throw ValidationException::withMessages([
                'start_date' => 'يوجد حجز آخر على نفس العقار في هذه الفترة.',
            ]);
        }
    }

    public function create(array $data): Booking
    {
        $calc = $this->calculate($data);
        $this->assertNoOverlap((int) $data['property_id'], $data['start_date'], $data['end_date']);

        return DB::transaction(function () use ($data, $calc) {
            $booking = Booking::query()->create([
                'code' => CodeGenerator::next('حجز-', Booking::class),
                'property_id' => $data['property_id'],
                'customer_id' => $data['customer_id'],
                'start_date' => $data['start_date'],
                'end_date' => $data['end_date'],
                'nights' => $calc['nights'],
                'nightly_rate' => $data['nightly_rate'],
                'discount_type' => $calc['discount_type'],
                'discount_value' => $calc['discount_value'],
                'discount' => $calc['discount'],
                'extra_type' => $calc['extra_type'],
                'extra_value' => $calc['extra_value'],
                'extra_amount' => $calc['extra_amount'],
                'rent_amount' => $calc['rent_amount'],
                'total' => $calc['total'],
                'paid_amount' => 0,
                'remaining_amount' => $calc['total'],
                'payment_method' => $data['payment_method'] ?? null,
                'status' => $data['status'] ?? 'pending',
                'notes' => $data['notes'] ?? null,
                'created_by' => Auth::id(),
                'updated_by' => Auth::id(),
            ]);

            $this->syncPropertyStatus($booking);
            $this->logger->log('booking.created', 'تم إنشاء حجز جديد: '.$booking->code, $booking, [
                'العقار' => $booking->property_id,
                'العميل' => $booking->customer_id,
                'الإجمالي' => $booking->total,
            ]);

            if (! empty($data['initial_payment']) && (float) $data['initial_payment'] > 0) {
                $this->payments->recordBookingPayment($booking, [
                    'amount' => $data['initial_payment'],
                    'paid_at' => $data['start_date'],
                    'payment_method' => $data['payment_method'] ?? 'cash',
                    'notes' => 'دفعة عند إنشاء الحجز',
                ]);
            }

            return $booking->fresh(['property', 'customer', 'payments']);
        });
    }

    public function update(Booking $booking, array $data): Booking
    {
        $calc = $this->calculate($data);
        $this->assertNoOverlap((int) $data['property_id'], $data['start_date'], $data['end_date'], $booking->id);

        return DB::transaction(function () use ($booking, $data, $calc) {
            $oldPropertyId = $booking->property_id;

            $booking->update([
                'property_id' => $data['property_id'],
                'customer_id' => $data['customer_id'],
                'start_date' => $data['start_date'],
                'end_date' => $data['end_date'],
                'nights' => $calc['nights'],
                'nightly_rate' => $data['nightly_rate'],
                'discount_type' => $calc['discount_type'],
                'discount_value' => $calc['discount_value'],
                'discount' => $calc['discount'],
                'extra_type' => $calc['extra_type'],
                'extra_value' => $calc['extra_value'],
                'extra_amount' => $calc['extra_amount'],
                'rent_amount' => $calc['rent_amount'],
                'total' => $calc['total'],
                'remaining_amount' => max(0, $calc['total'] - (float) $booking->paid_amount),
                'payment_method' => $data['payment_method'] ?? $booking->payment_method,
                'status' => $data['status'] ?? $booking->status,
                'notes' => $data['notes'] ?? $booking->notes,
                'updated_by' => Auth::id(),
            ]);

            if ($oldPropertyId !== $booking->property_id) {
                $this->refreshPropertyAvailability((int) $oldPropertyId);
            }

            $this->syncPropertyStatus($booking->fresh());
            $this->logger->log('booking.updated', 'تم تعديل الحجز: '.$booking->code, $booking);

            return $booking->fresh(['property', 'customer', 'payments']);
        });
    }

    public function changeStatus(Booking $booking, string $status): Booking
    {
        if (in_array($status, ['pending', 'confirmed', 'active'], true)) {
            $this->assertNoOverlap(
                (int) $booking->property_id,
                $booking->start_date->toDateString(),
                $booking->end_date->toDateString(),
                $booking->id,
            );
        }

        $booking->update([
            'status' => $status,
            'updated_by' => Auth::id(),
        ]);

        $this->syncPropertyStatus($booking->fresh());
        $this->logger->log('booking.status', 'تم تغيير حالة الحجز '.$booking->code.' إلى '.$status, $booking);

        return $booking->fresh();
    }

    public function delete(Booking $booking): void
    {
        DB::transaction(function () use ($booking) {
            $propertyId = $booking->property_id;
            $booking->delete();
            $this->refreshPropertyAvailability((int) $propertyId);
            $this->logger->log('booking.deleted', 'تم حذف الحجز: '.$booking->code, $booking);
        });
    }

    public function syncPropertyStatus(Booking $booking): void
    {
        $property = $booking->property;

        if (! $property || $property->status === 'sold') {
            return;
        }

        if (in_array($booking->status, ['cancelled', 'completed'], true)) {
            $this->refreshPropertyAvailability((int) $property->id);

            return;
        }

        $today = now()->toDateString();

        if (in_array($booking->status, ['confirmed', 'active'], true)) {
            if ($booking->start_date->toDateString() <= $today && $booking->end_date->toDateString() > $today) {
                $property->update(['status' => 'rented']);
                if ($booking->status !== 'active') {
                    $booking->update(['status' => 'active']);
                }
            } else {
                $property->update(['status' => 'reserved']);
            }
        }
    }

    public function refreshPropertyAvailability(int $propertyId): void
    {
        $property = Property::query()->find($propertyId);

        if (! $property || $property->status === 'sold') {
            return;
        }

        $today = now()->toDateString();

        $active = Booking::query()
            ->where('property_id', $propertyId)
            ->whereIn('status', ['confirmed', 'active'])
            ->where('start_date', '<=', $today)
            ->where('end_date', '>', $today)
            ->exists();

        if ($active) {
            $property->update(['status' => 'rented']);

            return;
        }

        $upcoming = Booking::query()
            ->where('property_id', $propertyId)
            ->whereIn('status', ['pending', 'confirmed', 'active'])
            ->where('end_date', '>', $today)
            ->exists();

        $property->update(['status' => $upcoming ? 'reserved' : 'available']);
    }

    public function refreshExpiredBookings(): int
    {
        $today = now()->toDateString();
        $count = 0;

        Booking::query()
            ->whereIn('status', ['confirmed', 'active'])
            ->whereDate('end_date', '<=', $today)
            ->each(function (Booking $booking) use (&$count) {
                $booking->update(['status' => 'completed']);
                $this->refreshPropertyAvailability((int) $booking->property_id);
                $count++;
            });

        Booking::query()
            ->where('status', 'confirmed')
            ->whereDate('start_date', '<=', $today)
            ->whereDate('end_date', '>', $today)
            ->each(function (Booking $booking) {
                $booking->update(['status' => 'active']);
                $this->syncPropertyStatus($booking->fresh());
            });

        return $count;
    }
}
