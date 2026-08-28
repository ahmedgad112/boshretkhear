<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SaleRequest extends FormRequest
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
            'sale_price' => ['required', 'numeric', 'min:0'],
            'discount' => ['nullable', 'numeric', 'min:0'],
            'sale_date' => ['required', 'date'],
            'payment_method' => ['nullable', Rule::in(['cash', 'bank_transfer', 'card', 'other'])],
            'status' => ['required', Rule::in(['pending', 'completed', 'cancelled'])],
            'notes' => ['nullable', 'string'],
            'initial_payment' => ['nullable', 'numeric', 'min:0'],
        ];
    }

    public function attributes(): array
    {
        return [
            'property_id' => 'العقار',
            'customer_id' => 'العميل',
            'sale_price' => 'سعر البيع',
            'discount' => 'الخصم',
            'sale_date' => 'تاريخ البيع',
            'status' => 'حالة البيع',
            'initial_payment' => 'المبلغ المدفوع',
        ];
    }
}
