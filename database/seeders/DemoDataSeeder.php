<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\BookingPayment;
use App\Models\Customer;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\FinancialTransaction;
use App\Models\Property;
use App\Models\PropertyFeature;
use App\Models\PropertyType;
use App\Models\Sale;
use App\Models\SalePayment;
use App\Models\User;
use App\Support\CodeGenerator;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::query()->where('email', 'admin@boshret.test')->first();

        $types = [
            ['name' => 'شقة', 'slug' => 'apartment'],
            ['name' => 'فيلا', 'slug' => 'villa'],
            ['name' => 'مكتب', 'slug' => 'office'],
            ['name' => 'محل تجاري', 'slug' => 'shop'],
            ['name' => 'أرض', 'slug' => 'land'],
            ['name' => 'استوديو', 'slug' => 'studio'],
        ];

        $typeModels = [];
        foreach ($types as $type) {
            $typeModels[$type['slug']] = PropertyType::query()->updateOrCreate(
                ['slug' => $type['slug']],
                ['name' => $type['name'], 'is_active' => true]
            );
        }

        $featureNames = ['مسبح', 'حديقة', 'موقف سيارات', 'أمن وحراسة', 'مصعد', 'تكييف', 'مفروش', 'شرفة', 'مخزن', 'إطلالة مميزة'];
        $features = collect($featureNames)->map(fn ($name) => PropertyFeature::query()->updateOrCreate(['name' => $name], ['is_active' => true]));

        $categories = [
            'صيانة' => 'maintenance',
            'كهرباء' => 'electricity',
            'مياه' => 'water',
            'نظافة' => 'cleaning',
            'رواتب' => 'salaries',
            'تسويق' => 'marketing',
            'عمولات' => 'commissions',
            'نقل' => 'transport',
            'مصروفات إدارية' => 'admin',
            'مصروفات أخرى' => 'other',
        ];

        $categoryModels = [];
        foreach ($categories as $name => $slug) {
            $categoryModels[$slug] = ExpenseCategory::query()->updateOrCreate(['slug' => $slug], ['name' => $name, 'is_active' => true]);
        }

        $customers = [
            ['name' => 'محمد عبد الرحمن', 'phone' => '01111111111', 'email' => 'mohamed@example.com', 'address' => 'المعادي، القاهرة', 'national_id' => '29001011234567'],
            ['name' => 'فاطمة حسن', 'phone' => '01222222222', 'email' => 'fatma@example.com', 'address' => 'سموحة، الإسكندرية'],
            ['name' => 'يوسف إبراهيم', 'phone' => '01333333333', 'phone_secondary' => '01555555555', 'address' => 'النرجس، القاهرة الجديدة'],
            ['name' => 'ليلى محمود', 'phone' => '01444444444', 'email' => 'laila@example.com', 'address' => 'حي الزهراء، جدة'],
            ['name' => 'عمر خالد', 'phone' => '01666666666', 'address' => 'العليا، الرياض'],
        ];

        $customerModels = [];
        foreach ($customers as $item) {
            $customerModels[] = Customer::query()->updateOrCreate(
                ['phone' => $item['phone']],
                [...$item, 'created_by' => $admin?->id]
            );
        }

        $properties = [
            [
                'code' => 'عقار-00001',
                'name' => 'شقة فاخرة في التجمع الخامس',
                'type' => 'apartment',
                'purpose' => 'rent',
                'price' => null,
                'rent_price' => 2500,
                'rent_period' => 'nightly',
                'district' => 'التجمع الخامس',
                'city' => 'القاهرة',
                'address' => 'شارع التسعين الشمالي',
                'area' => 180,
                'rooms' => 3,
                'bathrooms' => 2,
                'floors' => 8,
                'floor_number' => 5,
                'status' => 'available',
                'is_featured' => true,
                'description' => 'شقة واسعة بتشطيب حديث وإطلالة مفتوحة، مناسبة للسكن العائلي أو الإيجار اليومي.',
            ],
            [
                'code' => 'عقار-00002',
                'name' => 'فيلا مستقلة في الشيخ زايد',
                'type' => 'villa',
                'purpose' => 'sale',
                'price' => 12500000,
                'rent_price' => null,
                'district' => 'الشيخ زايد',
                'city' => 'الجيزة',
                'address' => 'حي الياسمين',
                'area' => 420,
                'rooms' => 5,
                'bathrooms' => 4,
                'floors' => 2,
                'status' => 'available',
                'is_featured' => true,
                'description' => 'فيلا مستقلة بحديقة خاصة ومسبح، تصميم عصري وموقع هادئ.',
            ],
            [
                'code' => 'عقار-00003',
                'name' => 'مكتب إداري في وسط البلد',
                'type' => 'office',
                'purpose' => 'rent',
                'rent_price' => 18000,
                'rent_period' => 'monthly',
                'district' => 'وسط البلد',
                'city' => 'القاهرة',
                'address' => 'شارع قصر النيل',
                'area' => 95,
                'rooms' => 2,
                'bathrooms' => 1,
                'floors' => 10,
                'floor_number' => 7,
                'status' => 'available',
                'is_featured' => false,
                'description' => 'مكتب جاهز للعمل في موقع حيوي قريب من المصالح والبنوك.',
            ],
            [
                'code' => 'عقار-00004',
                'name' => 'محل تجاري على البحر',
                'type' => 'shop',
                'purpose' => 'both',
                'price' => 4800000,
                'rent_price' => 22000,
                'rent_period' => 'monthly',
                'district' => 'ستانلي',
                'city' => 'الإسكندرية',
                'address' => 'كورنيش الإسكندرية',
                'area' => 70,
                'rooms' => 1,
                'bathrooms' => 1,
                'status' => 'available',
                'is_featured' => true,
                'description' => 'محل بواجهة مباشرة على الكورنيش، مناسب للمطاعم والكافيهات.',
            ],
            [
                'code' => 'عقار-00005',
                'name' => 'استوديو مفروش في المهندسين',
                'type' => 'studio',
                'purpose' => 'rent',
                'rent_price' => 1200,
                'rent_period' => 'nightly',
                'district' => 'المهندسين',
                'city' => 'الجيزة',
                'address' => 'شارع جامعة الدول العربية',
                'area' => 55,
                'rooms' => 1,
                'bathrooms' => 1,
                'floors' => 6,
                'floor_number' => 3,
                'status' => 'available',
                'is_featured' => false,
                'description' => 'استوديو مفروش بالكامل مناسب للإقامة القصيرة.',
            ],
            [
                'code' => 'عقار-00006',
                'name' => 'أرض سكنية في العبور',
                'type' => 'land',
                'purpose' => 'sale',
                'price' => 3200000,
                'district' => 'العبور',
                'city' => 'القليوبية',
                'address' => 'المجاورة السابعة',
                'area' => 600,
                'status' => 'available',
                'description' => 'قطعة أرض سكنية بموقع متميز وصالح للبناء فورًا.',
            ],
        ];

        $propertyModels = [];
        foreach ($properties as $item) {
            $type = $typeModels[$item['type']];
            unset($item['type']);
            $property = Property::query()->updateOrCreate(
                ['code' => $item['code']],
                [
                    ...$item,
                    'property_type_id' => $type->id,
                    'is_published' => true,
                    'created_by' => $admin?->id,
                    'updated_by' => $admin?->id,
                    'notes' => 'بيانات تجريبية للمراجعة.',
                ]
            );
            $property->features()->sync($features->random(4)->pluck('id'));
            $this->createPlaceholderImages($property);
            $propertyModels[] = $property->fresh();
        }

        $rentProperty = $propertyModels[0];
        $saleProperty = $propertyModels[1];
        $customer = $customerModels[0];
        $buyer = $customerModels[1];

        $booking = Booking::query()->updateOrCreate(
            ['code' => 'حجز-00001'],
            [
                'property_id' => $rentProperty->id,
                'customer_id' => $customer->id,
                'start_date' => now()->subDays(2)->toDateString(),
                'end_date' => now()->addDays(5)->toDateString(),
                'nights' => 7,
                'nightly_rate' => 2500,
                'discount' => 500,
                'extra_amount' => 300,
                'rent_amount' => 17500,
                'total' => 17300,
                'paid_amount' => 8000,
                'remaining_amount' => 9300,
                'payment_method' => 'cash',
                'status' => 'active',
                'notes' => 'حجز تجريبي ساري',
                'created_by' => $admin?->id,
            ]
        );

        $rentProperty->update(['status' => 'rented']);

        $payment = BookingPayment::query()->updateOrCreate(
            ['code' => 'دفعة-00001'],
            [
                'booking_id' => $booking->id,
                'customer_id' => $customer->id,
                'property_id' => $rentProperty->id,
                'amount' => 8000,
                'paid_at' => now()->subDays(2)->toDateString(),
                'payment_method' => 'cash',
                'reference_number' => 'نقد-001',
                'notes' => 'دفعة مقدمة',
                'created_by' => $admin?->id,
            ]
        );

        FinancialTransaction::query()->updateOrCreate(
            ['code' => 'مالية-00001'],
            [
                'type' => 'customer_payment',
                'amount' => 8000,
                'transaction_date' => now()->subDays(2)->toDateString(),
                'property_id' => $rentProperty->id,
                'customer_id' => $customer->id,
                'user_id' => $admin?->id,
                'description' => 'دفعة على الحجز '.$booking->code,
                'reference_type' => BookingPayment::class,
                'reference_id' => $payment->id,
            ]
        );

        $completedBooking = Booking::query()->updateOrCreate(
            ['code' => 'حجز-00002'],
            [
                'property_id' => $propertyModels[4]->id,
                'customer_id' => $customerModels[2]->id,
                'start_date' => now()->subDays(20)->toDateString(),
                'end_date' => now()->subDays(15)->toDateString(),
                'nights' => 5,
                'nightly_rate' => 1200,
                'discount' => 0,
                'extra_amount' => 0,
                'rent_amount' => 6000,
                'total' => 6000,
                'paid_amount' => 6000,
                'remaining_amount' => 0,
                'payment_method' => 'card',
                'status' => 'completed',
                'created_by' => $admin?->id,
            ]
        );

        BookingPayment::query()->updateOrCreate(
            ['code' => 'دفعة-00002'],
            [
                'booking_id' => $completedBooking->id,
                'customer_id' => $customerModels[2]->id,
                'property_id' => $propertyModels[4]->id,
                'amount' => 6000,
                'paid_at' => now()->subDays(20)->toDateString(),
                'payment_method' => 'card',
                'created_by' => $admin?->id,
            ]
        );

        FinancialTransaction::query()->updateOrCreate(
            ['code' => 'مالية-00002'],
            [
                'type' => 'rent_income',
                'amount' => 6000,
                'transaction_date' => now()->subDays(20)->toDateString(),
                'property_id' => $propertyModels[4]->id,
                'customer_id' => $customerModels[2]->id,
                'user_id' => $admin?->id,
                'description' => 'إيراد إيجار '.$completedBooking->code,
                'reference_type' => Booking::class,
                'reference_id' => $completedBooking->id,
            ]
        );

        $sale = Sale::query()->updateOrCreate(
            ['code' => 'بيع-00001'],
            [
                'property_id' => $saleProperty->id,
                'customer_id' => $buyer->id,
                'sale_price' => 12500000,
                'discount' => 200000,
                'final_price' => 12300000,
                'paid_amount' => 4000000,
                'remaining_amount' => 8300000,
                'sale_date' => now()->subDays(8)->toDateString(),
                'payment_method' => 'bank_transfer',
                'status' => 'completed',
                'notes' => 'تم إتمام البيع مع جدولة باقي المبلغ',
                'created_by' => $admin?->id,
            ]
        );

        $saleProperty->update(['status' => 'sold']);

        $salePayment = SalePayment::query()->updateOrCreate(
            ['code' => 'دفعةبيع-00001'],
            [
                'sale_id' => $sale->id,
                'customer_id' => $buyer->id,
                'property_id' => $saleProperty->id,
                'amount' => 4000000,
                'paid_at' => now()->subDays(8)->toDateString(),
                'payment_method' => 'bank_transfer',
                'reference_number' => 'تحويل-8891',
                'created_by' => $admin?->id,
            ]
        );

        FinancialTransaction::query()->updateOrCreate(
            ['code' => 'مالية-00003'],
            [
                'type' => 'sale_income',
                'amount' => 12300000,
                'transaction_date' => now()->subDays(8)->toDateString(),
                'property_id' => $saleProperty->id,
                'customer_id' => $buyer->id,
                'user_id' => $admin?->id,
                'description' => 'إيراد بيع العقار '.$saleProperty->name,
                'reference_type' => Sale::class,
                'reference_id' => $sale->id,
            ]
        );

        FinancialTransaction::query()->updateOrCreate(
            ['code' => 'مالية-00004'],
            [
                'type' => 'customer_payment',
                'amount' => 4000000,
                'transaction_date' => now()->subDays(8)->toDateString(),
                'property_id' => $saleProperty->id,
                'customer_id' => $buyer->id,
                'user_id' => $admin?->id,
                'description' => 'دفعة على عملية البيع '.$sale->code,
                'reference_type' => SalePayment::class,
                'reference_id' => $salePayment->id,
            ]
        );

        $expense = Expense::query()->updateOrCreate(
            ['code' => 'مصروف-00001'],
            [
                'expense_category_id' => $categoryModels['maintenance']->id,
                'amount' => 1500,
                'expense_date' => now()->subDays(3)->toDateString(),
                'property_id' => $rentProperty->id,
                'description' => 'صيانة تكييف الشقة',
                'notes' => 'تم الإصلاح في نفس اليوم',
                'created_by' => $admin?->id,
            ]
        );

        FinancialTransaction::query()->updateOrCreate(
            ['code' => 'مالية-00005'],
            [
                'type' => 'expense',
                'amount' => 1500,
                'transaction_date' => now()->subDays(3)->toDateString(),
                'property_id' => $rentProperty->id,
                'user_id' => $admin?->id,
                'description' => 'صيانة تكييف الشقة',
                'reference_type' => Expense::class,
                'reference_id' => $expense->id,
            ]
        );

        Expense::query()->updateOrCreate(
            ['code' => 'مصروف-00002'],
            [
                'expense_category_id' => $categoryModels['marketing']->id,
                'amount' => 3000,
                'expense_date' => now()->subDays(10)->toDateString(),
                'description' => 'حملة إعلانية للعقارات المميزة',
                'created_by' => $admin?->id,
            ]
        );
    }

    private function createPlaceholderImages(Property $property): void
    {
        if ($property->images()->exists()) {
            return;
        }

        $colors = ['0f4c3a', '1b6b4f', 'c9a227', '8b6914'];
        foreach ($colors as $index => $color) {
            $filename = Str::uuid().'.svg';
            $path = 'properties/'.$property->id.'/'.$filename;
            $svg = <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" width="1200" height="800" viewBox="0 0 1200 800">
  <rect width="1200" height="800" fill="#{$color}"/>
  <text x="50%" y="50%" dominant-baseline="middle" text-anchor="middle" fill="#f7f3ea" font-size="48" font-family="Tahoma">{$property->name}</text>
</svg>
SVG;
            Storage::disk('public')->put($path, $svg);
            $property->images()->create([
                'path' => $path,
                'original_name' => $filename,
                'is_primary' => $index === 0,
                'sort_order' => $index,
            ]);
        }
    }
}
