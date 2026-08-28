<?php

namespace App\Models;

use App\Support\Labels;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['property_id', 'name', 'phone', 'email', 'type', 'message', 'status'])]
class Inquiry extends Model
{
    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function getTypeLabelAttribute(): string
    {
        return Labels::inquiryType($this->type);
    }

    public function getStatusLabelAttribute(): string
    {
        return Labels::inquiryStatus($this->status);
    }
}
