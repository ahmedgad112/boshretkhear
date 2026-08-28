<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PropertyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $property = $this->route('property');

        return [
            'name' => ['required', 'string', 'max:255'],
            'code' => ['nullable', 'string', 'max:50', Rule::unique('properties', 'code')->ignore($property)],
            'property_type_id' => ['required', 'exists:property_types,id'],
            'purpose' => ['required', Rule::in(['sale', 'rent', 'both'])],
            'price' => ['nullable', 'numeric', 'min:0'],
            'rent_price' => ['nullable', 'numeric', 'min:0'],
            'rent_period' => ['nullable', Rule::in(['nightly', 'daily', 'monthly', 'yearly'])],
            'district' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string', 'max:500'],
            'latitude' => ['nullable', 'numeric'],
            'longitude' => ['nullable', 'numeric'],
            'area' => ['nullable', 'numeric', 'min:0'],
            'rooms' => ['nullable', 'integer', 'min:0'],
            'bathrooms' => ['nullable', 'integer', 'min:0'],
            'floors' => ['nullable', 'integer', 'min:0'],
            'floor_number' => ['nullable', 'integer', 'min:0'],
            'description' => ['nullable', 'string'],
            'status' => ['required', Rule::in(['available', 'reserved', 'rented', 'sold', 'unavailable'])],
            'notes' => ['nullable', 'string'],
            'is_featured' => ['nullable', 'boolean'],
            'is_published' => ['nullable', 'boolean'],
            'feature_ids' => ['nullable', 'array'],
            'feature_ids.*' => ['exists:property_features,id'],
            'images' => ['nullable', 'array', 'max:10'],
            'images.*' => ['file', 'mimetypes:image/jpeg,image/png,image/webp,image/gif,video/mp4,video/webm,video/quicktime', 'max:51200'],
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => 'اسم العقار',
            'code' => 'رمز العقار',
            'property_type_id' => 'نوع العقار',
            'purpose' => 'الغرض من العقار',
            'price' => 'السعر',
            'rent_price' => 'سعر الإيجار',
            'status' => 'حالة العقار',
            'images.*' => 'الصور والفيديوهات',
        ];
    }
}
