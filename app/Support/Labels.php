<?php

namespace App\Support;

class Labels
{
    public static function propertyStatus(?string $value): string
    {
        return match ($value) {
            'available' => 'متاح',
            'reserved' => 'محجوز',
            'rented' => 'مؤجر',
            'sold' => 'مباع',
            'unavailable' => 'غير متاح',
            default => $value ?: 'غير محدد',
        };
    }

    public static function propertyPurpose(?string $value): string
    {
        return match ($value) {
            'sale' => 'للبيع',
            'rent' => 'للإيجار',
            'both' => 'بيع وإيجار',
            default => $value ?: 'غير محدد',
        };
    }

    public static function rentPeriod(?string $value): string
    {
        return match ($value) {
            'nightly' => 'لليلة',
            'daily' => 'لليوم',
            'monthly' => 'للشهر',
            'yearly' => 'للسنة',
            default => $value ?: 'لليلة',
        };
    }

    public static function bookingStatus(?string $value): string
    {
        return match ($value) {
            'pending' => 'قيد الانتظار',
            'confirmed' => 'مؤكد',
            'active' => 'ساري',
            'completed' => 'منتهي',
            'cancelled' => 'ملغى',
            default => $value ?: 'غير محدد',
        };
    }

    public static function saleStatus(?string $value): string
    {
        return match ($value) {
            'pending' => 'قيد الإتمام',
            'completed' => 'مكتمل',
            'cancelled' => 'ملغى',
            default => $value ?: 'غير محدد',
        };
    }

    public static function paymentMethod(?string $value): string
    {
        return match ($value) {
            'cash' => 'نقدي',
            'bank_transfer' => 'تحويل بنكي',
            'card' => 'بطاقة',
            'other' => 'طريقة دفع أخرى',
            default => $value ?: 'غير محدد',
        };
    }

    public static function transactionType(?string $value): string
    {
        return match ($value) {
            'rent_income' => 'إيراد إيجار',
            'sale_income' => 'إيراد بيع',
            'customer_payment' => 'دفعة من عميل',
            'expense' => 'مصروف',
            'refund' => 'استرداد مبلغ',
            'other' => 'عملية مالية أخرى',
            default => $value ?: 'غير محدد',
        };
    }

    public static function inquiryType(?string $value): string
    {
        return match ($value) {
            'booking' => 'طلب حجز',
            'viewing' => 'طلب معاينة',
            'contact' => 'طلب تواصل',
            default => $value ?: 'تواصل',
        };
    }

    public static function inquiryStatus(?string $value): string
    {
        return match ($value) {
            'new' => 'جديد',
            'in_progress' => 'قيد المتابعة',
            'closed' => 'مغلق',
            default => $value ?: 'جديد',
        };
    }

    public static function propertyStatuses(): array
    {
        return [
            'available' => 'متاح',
            'reserved' => 'محجوز',
            'rented' => 'مؤجر',
            'sold' => 'مباع',
            'unavailable' => 'غير متاح',
        ];
    }

    public static function propertyPurposes(): array
    {
        return [
            'sale' => 'للبيع',
            'rent' => 'للإيجار',
            'both' => 'بيع وإيجار',
        ];
    }

    public static function rentPeriods(): array
    {
        return [
            'nightly' => 'لليلة',
            'daily' => 'لليوم',
            'monthly' => 'للشهر',
            'yearly' => 'للسنة',
        ];
    }

    public static function bookingStatuses(): array
    {
        return [
            'pending' => 'قيد الانتظار',
            'confirmed' => 'مؤكد',
            'active' => 'ساري',
            'completed' => 'منتهي',
            'cancelled' => 'ملغى',
        ];
    }

    public static function saleStatuses(): array
    {
        return [
            'pending' => 'قيد الإتمام',
            'completed' => 'مكتمل',
            'cancelled' => 'ملغى',
        ];
    }

    public static function paymentMethods(): array
    {
        return [
            'cash' => 'نقدي',
            'bank_transfer' => 'تحويل بنكي',
            'card' => 'بطاقة',
            'other' => 'طريقة دفع أخرى',
        ];
    }

    public static function transactionTypes(): array
    {
        return [
            'rent_income' => 'إيراد إيجار',
            'sale_income' => 'إيراد بيع',
            'customer_payment' => 'دفعة من عميل',
            'expense' => 'مصروف',
            'refund' => 'استرداد مبلغ',
            'other' => 'عملية مالية أخرى',
        ];
    }
}
