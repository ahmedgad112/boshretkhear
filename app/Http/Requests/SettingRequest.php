<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SettingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'business_name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email'],
            'address' => ['nullable', 'string', 'max:500'],
            'contact_info' => ['nullable', 'string'],
            'currency' => ['required', 'string', 'max:20'],
            'default_rent_period' => ['nullable', 'string'],
            'notify_due_amounts' => ['nullable', 'boolean'],
            'notify_bookings' => ['nullable', 'boolean'],
            'logo' => ['nullable', 'image', 'max:2048'],
        ];
    }

    public function attributes(): array
    {
        return [
            'business_name' => 'اسم النشاط',
            'phone' => 'رقم الهاتف',
            'email' => 'البريد الإلكتروني',
            'address' => 'العنوان',
            'currency' => 'العملة',
            'logo' => 'الشعار',
        ];
    }
}
