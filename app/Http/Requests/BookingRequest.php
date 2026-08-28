<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BookingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'property_id' => ['required', 'exists:properties,id'],
            'customer_id' => ['required', 'exists:customers,id'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after:start_date'],
            'nightly_rate' => ['required', 'numeric', 'min:0'],
            'discount_type' => ['nullable', Rule::in(['amount', 'percent'])],
            'discount_value' => ['nullable', 'numeric', 'min:0'],
            'extra_type' => ['nullable', Rule::in(['amount', 'percent'])],
            'extra_value' => ['nullable', 'numeric', 'min:0'],
            'payment_method' => ['nullable', Rule::in(['cash', 'bank_transfer', 'card', 'other'])],
            'status' => ['required', Rule::in(['pending', 'confirmed', 'active', 'completed', 'cancelled'])],
            'notes' => ['nullable', 'string'],
            'initial_payment' => ['nullable', 'numeric', 'min:0'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            if (($this->input('discount_type') === 'percent') && (float) $this->input('discount_value', 0) > 100) {
                $validator->errors()->add('discount_value', 'نسبة الخصم يجب ألا تتجاوز 100%.');
            }

            if (($this->input('extra_type') === 'percent') && (float) $this->input('extra_value', 0) > 100) {
                $validator->errors()->add('extra_value', 'النسبة المئوية يجب ألا تتجاوز 100%.');
            }
        });
    }

    public function attributes(): array
    {
        return [
            'property_id' => 'العقار',
            'customer_id' => 'العميل',
            'start_date' => 'تاريخ بداية الإيجار',
            'end_date' => 'تاريخ نهاية الإيجار',
            'nightly_rate' => 'سعر الليلة أو اليوم',
            'discount_type' => 'نوع الخصم',
            'discount_value' => 'الخصم',
            'extra_type' => 'نوع المبلغ الإضافي',
            'extra_value' => 'المبلغ الإضافي',
            'status' => 'حالة الحجز',
            'initial_payment' => 'المبلغ المدفوع',
        ];
    }
}
