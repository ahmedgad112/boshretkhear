<?php

namespace App\Models;

use App\Support\Labels;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable([
    'code', 'type', 'amount', 'transaction_date', 'property_id', 'customer_id',
    'user_id', 'description', 'reference_type', 'reference_id', 'notes',
])]
class FinancialTransaction extends Model
{
    use SoftDeletes;

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'transaction_date' => 'date',
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

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function reference(): MorphTo
    {
        return $this->morphTo();
    }

    public function getTypeLabelAttribute(): string
    {
        return Labels::transactionType($this->type);
    }

    public function isIncome(): bool
    {
        return in_array($this->type, ['rent_income', 'sale_income', 'customer_payment'], true);
    }

    public function isExpense(): bool
    {
        return in_array($this->type, ['expense', 'refund'], true);
    }
}
