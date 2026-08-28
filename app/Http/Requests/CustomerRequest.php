<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CustomerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:30'],
            'phone_secondary' => ['nullable', 'string', 'max:30'],
            'address' => ['nullable', 'string', 'max:500'],
            'national_id' => ['nullable', 'string', 'max:30'],
            'notes' => ['nullable', 'string'],
            'id_card_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'remove_id_card' => ['nullable', 'boolean'],
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => 'الاسم',
            'phone' => 'رقم الهاتف',
            'phone_secondary' => 'رقم هاتف إضافي',
            'address' => 'العنوان',
            'national_id' => 'الرقم القومي',
            'notes' => 'الملاحظات',
            'id_card_image' => 'صورة بطاقة العميل',
        ];
    }
}
