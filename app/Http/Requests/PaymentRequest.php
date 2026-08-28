<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'source_type' => ['required', Rule::in(['booking', 'sale'])],
            'source_id' => ['required', 'integer'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'paid_at' => ['required', 'date'],
            'payment_method' => ['required', Rule::in(['cash', 'bank_transfer', 'card', 'other'])],
            'reference_number' => ['nullable', 'string', 'max:100'],
            'notes' => ['nullable', 'string'],
        ];
    }

    public function attributes(): array
    {
        return [
            'source_type' => 'المرجع',
            'source_id' => 'الحجز أو عملية البيع',
            'amount' => 'المبلغ',
            'paid_at' => 'التاريخ',
            'payment_method' => 'طريقة الدفع',
            'reference_number' => 'رقم العملية',
        ];
    }
}
