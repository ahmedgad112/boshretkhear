<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable([
    'name', 'phone', 'phone_secondary', 'email', 'address',
    'national_id', 'id_card_path', 'notes', 'created_by',
])]
class Customer extends Model
{
    use SoftDeletes;

    public function getIdCardUrlAttribute(): ?string
    {
        return $this->id_card_path ? '/storage/'.$this->id_card_path : null;
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function sales(): HasMany
    {
        return $this->hasMany(Sale::class);
    }

    public function bookingPayments(): HasMany
    {
        return $this->hasMany(BookingPayment::class);
    }

    public function salePayments(): HasMany
    {
        return $this->hasMany(SalePayment::class);
    }

    public function financialTransactions(): HasMany
    {
        return $this->hasMany(FinancialTransaction::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getDueAmountAttribute(): float
    {
        $bookingDue = (float) $this->bookings()->whereNotIn('status', ['cancelled'])->sum('remaining_amount');
        $saleDue = (float) $this->sales()->whereNotIn('status', ['cancelled'])->sum('remaining_amount');

        return $bookingDue + $saleDue;
    }

    public function getTotalPaidAttribute(): float
    {
        return (float) $this->bookingPayments()->sum('amount') + (float) $this->salePayments()->sum('amount');
    }
}
