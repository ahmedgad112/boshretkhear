<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ExpenseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'expense_category_id' => ['required', 'exists:expense_categories,id'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'expense_date' => ['required', 'date'],
            'property_id' => ['nullable', 'exists:properties,id'],
            'description' => ['nullable', 'string', 'max:500'],
            'notes' => ['nullable', 'string'],
        ];
    }

    public function attributes(): array
    {
        return [
            'expense_category_id' => 'نوع المصروف',
            'amount' => 'المبلغ',
            'expense_date' => 'التاريخ',
            'property_id' => 'العقار',
            'description' => 'الوصف',
        ];
    }
}
