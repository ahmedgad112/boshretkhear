<?php

namespace App\Models;

use App\Support\Labels;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable([
    'code', 'property_id', 'customer_id', 'start_date', 'end_date', 'nights',
    'nightly_rate', 'discount_type', 'discount_value', 'discount',
    'extra_type', 'extra_value', 'extra_amount',
    'rent_amount', 'total', 'paid_amount', 'remaining_amount', 'payment_method',
    'status', 'notes', 'created_by', 'updated_by',
])]
class Booking extends Model
{
    use SoftDeletes;

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'nightly_rate' => 'decimal:2',
            'discount_value' => 'decimal:2',
            'discount' => 'decimal:2',
            'extra_value' => 'decimal:2',
            'extra_amount' => 'decimal:2',
            'rent_amount' => 'decimal:2',
            'total' => 'decimal:2',
            'paid_amount' => 'decimal:2',
            'remaining_amount' => 'decimal:2',
        ];
    }

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(BookingPayment::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getStatusLabelAttribute(): string
    {
        return Labels::bookingStatus($this->status);
    }

    public function isActivePeriod(): bool
    {
        return in_array($this->status, ['pending', 'confirmed', 'active'], true);
    }
}
