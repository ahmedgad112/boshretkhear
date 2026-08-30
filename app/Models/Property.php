<?php

namespace App\Models;

use App\Support\Labels;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable([
    'code', 'name', 'property_type_id', 'purpose', 'price', 'rent_price', 'rent_period',
    'district', 'city', 'address', 'latitude', 'longitude', 'area', 'rooms', 'bathrooms',
    'floors', 'floor_number', 'description', 'status', 'notes', 'is_featured',
    'is_published', 'created_by', 'updated_by',
])]
class Property extends Model
{
    use SoftDeletes;

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'rent_price' => 'decimal:2',
            'area' => 'decimal:2',
            'latitude' => 'decimal:7',
            'longitude' => 'decimal:7',
            'is_featured' => 'boolean',
            'is_published' => 'boolean',
        ];
    }

    public function type(): BelongsTo
    {
        return $this->belongsTo(PropertyType::class, 'property_type_id');
    }

    public function images(): HasMany
    {
        return $this->hasMany(PropertyImage::class)->orderBy('sort_order');
    }

    public function cardImage(): ?PropertyImage
    {
        $images = $this->relationLoaded('images') ? $this->images : $this->images()->get();

        return $images->first(fn (PropertyImage $image) => $image->is_primary && $image->isImage())
            ?? $images->first(fn (PropertyImage $image) => $image->isImage())
            ?? $images->first();
    }

    public function primaryImage(): HasMany
    {
        return $this->hasMany(PropertyImage::class)->where('is_primary', true);
    }

    public function features(): BelongsToMany
    {
        return $this->belongsToMany(PropertyFeature::class, 'property_feature_property');
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function sales(): HasMany
    {
        return $this->hasMany(Sale::class);
    }

    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class);
    }

    public function financialTransactions(): HasMany
    {
        return $this->hasMany(FinancialTransaction::class);
    }

    public function inquiries(): HasMany
    {
        return $this->hasMany(Inquiry::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getStatusLabelAttribute(): string
    {
        return Labels::propertyStatus($this->status);
    }

    public function getPurposeLabelAttribute(): string
    {
        return Labels::propertyPurpose($this->purpose);
    }

    public function getRentPeriodLabelAttribute(): string
    {
        return Labels::rentPeriod($this->rent_period);
    }

    public function getLocationAttribute(): string
    {
        return collect([$this->district, $this->city])->filter()->implode('، ');
    }

    public function getDisplayPriceAttribute(): ?string
    {
        if (in_array($this->purpose, ['rent', 'both'], true) && $this->rent_price) {
            return $this->rent_price;
        }

        return $this->price;
    }

    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true)->where('is_published', true);
    }

    public function scopeForSale($query)
    {
        return $query->whereIn('purpose', ['sale', 'both']);
    }

    public function scopeForRent($query)
    {
        return $query->whereIn('purpose', ['rent', 'both']);
    }

    public function scopeApartments($query)
    {
        return $query->whereHas('type', fn ($q) => $q->where('slug', 'apartment'));
    }
}
