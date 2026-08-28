<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import { computed, watch } from 'vue';
import AdminLayout from '../../../Layouts/AdminLayout.vue';
import PageHeader from '../../../Components/PageHeader.vue';
import SearchableSelect from '../../../Components/SearchableSelect.vue';

defineOptions({ layout: AdminLayout });
const props = defineProps({ booking: { type: Object, default: null }, properties: Array, customers: Array });

const form = useForm({
    property_id: props.booking?.property_id || '',
    customer_id: props.booking?.customer_id || '',
    start_date: props.booking?.start_date || '',
    end_date: props.booking?.end_date || '',
    nightly_rate: props.booking?.nightly_rate || '',
    discount_type: props.booking?.discount_type || 'amount',
    discount_value: props.booking?.discount_value ?? props.booking?.discount ?? 0,
    extra_type: props.booking?.extra_type || 'amount',
    extra_value: props.booking?.extra_value ?? props.booking?.extra_amount ?? 0,
    payment_method: props.booking?.payment_method || 'cash',
    status: props.booking?.status || 'pending',
    notes: props.booking?.notes || '',
    initial_payment: '',
});

const nights = computed(() => {
    if (!form.start_date || !form.end_date) return 0;
    const start = new Date(form.start_date);
    const end = new Date(form.end_date);
    const diff = Math.round((end - start) / 86400000);
    return diff > 0 ? diff : 0;
});

const rentAmount = computed(() => nights.value * Number(form.nightly_rate || 0));

const resolveAmount = (type, value) => {
    const amount = Number(value || 0);

    if (type === 'percent') {
        return Math.round(rentAmount.value * (amount / 100) * 100) / 100;
    }

    return amount;
};

const discountAmount = computed(() => resolveAmount(form.discount_type, form.discount_value));
const extraAmount = computed(() => resolveAmount(form.extra_type, form.extra_value));
const total = computed(() => rentAmount.value + extraAmount.value - discountAmount.value);
const remaining = computed(() => total.value - Number(form.initial_payment || props.booking?.paid_amount || 0));

const discountPlaceholder = computed(() =>
    form.discount_type === 'percent' ? 'مثال: 10 = 10%' : 'مثال: 100 جنيه',
);

const extraPlaceholder = computed(() =>
    form.extra_type === 'percent' ? 'مثال: 10 = 10%' : 'مثال: 200 جنيه',
);

const onPropertySelect = (property) => {
    if (!property) return;

    if (!props.booking || !form.nightly_rate) {
        form.nightly_rate = property.rent_price ?? form.nightly_rate;
    }
};

watch(() => form.property_id, (id) => {
    if (!id || props.booking) return;
    const property = props.properties.find((item) => String(item.id) === String(id));
    if (property?.rent_price && !form.nightly_rate) {
        form.nightly_rate = property.rent_price;
    }
});

const submit = () => {
    props.booking
        ? form.put(route('admin.bookings.update', props.booking.id))
        : form.post(route('admin.bookings.store'));
};
</script>

<template>
    <Head :title="booking ? 'تعديل حجز' : 'إنشاء حجز'" />
    <PageHeader :title="booking ? 'تعديل حجز' : 'إنشاء حجز'" :subtitle="booking ? 'تحديث بيانات الحجز والمبالغ' : 'تسجيل حجز جديد للعقار والعميل'" />
    <form class="ui-panel grid gap-4 md:grid-cols-2" @submit.prevent="submit">
        <SearchableSelect
            v-model="form.property_id"
            :items="properties"
            label="العقار"
            placeholder="ابحث باسم الشقة أو كود العقار..."
            empty-text="لا يوجد عقار بهذا الاسم"
            required
            :get-label="(item) => item.name"
            :get-sub-label="(item) => [item.code, item.rent_price ? `سعر الإيجار: ${item.rent_price}` : null].filter(Boolean).join(' • ')"
            :get-search-text="(item) => `${item.name || ''} ${item.code || ''}`"
            @select="onPropertySelect"
        />

        <SearchableSelect
            v-model="form.customer_id"
            :items="customers"
            label="العميل"
            placeholder="ابحث باسم العميل أو رقم الهاتف..."
            empty-text="لا يوجد عميل بهذا الاسم أو الرقم"
            required
            :get-label="(item) => item.name"
            :get-sub-label="(item) => item.phone || ''"
            :get-search-text="(item) => `${item.name || ''} ${item.phone || ''} ${(item.phone || '').replace(/\s+/g, '')}`"
        />

        <div>
            <label class="ui-label">تاريخ البداية</label>
            <input v-model="form.start_date" type="date" class="ui-input" required />
        </div>
        <div>
            <label class="ui-label">تاريخ النهاية</label>
            <input v-model="form.end_date" type="date" class="ui-input" required />
        </div>
        <div class="md:col-span-2">
            <label class="ui-label">سعر الليلة أو اليوم</label>
            <input v-model="form.nightly_rate" type="number" min="0" step="0.01" placeholder="سعر الليلة أو اليوم" class="ui-input" required />
        </div>

        <div class="md:col-span-2">
            <label class="mb-1.5 block text-xs font-bold text-slate-600">الخصم</label>
            <div class="grid gap-3 sm:grid-cols-[180px_1fr]">
                <select v-model="form.discount_type" class="ui-select">
                    <option value="amount">مبلغ ثابت</option>
                    <option value="percent">نسبة مئوية %</option>
                </select>
                <div class="relative">
                    <input
                        v-model="form.discount_value"
                        type="number"
                        min="0"
                        :max="form.discount_type === 'percent' ? 100 : undefined"
                        step="0.01"
                        :placeholder="discountPlaceholder"
                        class="ui-input pe-12"
                    />
                    <span class="pointer-events-none absolute inset-y-0 end-0 flex items-center pe-3 text-xs font-bold text-slate-500">
                        {{ form.discount_type === 'percent' ? '%' : 'ج.م' }}
                    </span>
                </div>
            </div>
            <p v-if="form.discount_type === 'percent' && Number(form.discount_value) > 0" class="mt-2 text-xs text-slate-500">
                يعادل خصمًا: <strong class="text-forest">{{ discountAmount }}</strong> جنيه
            </p>
            <p v-if="form.errors.discount_value" class="mt-1 text-sm text-red-600">{{ form.errors.discount_value }}</p>
        </div>

        <div class="md:col-span-2">
            <label class="mb-1.5 block text-xs font-bold text-slate-600">المبلغ الإضافي</label>
            <div class="grid gap-3 sm:grid-cols-[180px_1fr]">
                <select v-model="form.extra_type" class="ui-select">
                    <option value="amount">مبلغ ثابت</option>
                    <option value="percent">نسبة مئوية %</option>
                </select>
                <div class="relative">
                    <input
                        v-model="form.extra_value"
                        type="number"
                        min="0"
                        :max="form.extra_type === 'percent' ? 100 : undefined"
                        step="0.01"
                        :placeholder="extraPlaceholder"
                        class="ui-input pe-12"
                    />
                    <span class="pointer-events-none absolute inset-y-0 end-0 flex items-center pe-3 text-xs font-bold text-slate-500">
                        {{ form.extra_type === 'percent' ? '%' : 'ج.م' }}
                    </span>
                </div>
            </div>
            <p v-if="form.extra_type === 'percent' && Number(form.extra_value) > 0" class="mt-2 text-xs text-slate-500">
                يعادل مبلغًا إضافيًا: <strong class="text-forest">{{ extraAmount }}</strong> جنيه
            </p>
            <p v-if="form.errors.extra_value" class="mt-1 text-sm text-red-600">{{ form.errors.extra_value }}</p>
        </div>

        <div v-if="!booking">
            <label class="mb-1.5 block text-xs font-bold text-slate-600">المبلغ المدفوع</label>
            <input v-model="form.initial_payment" type="number" min="0" step="0.01" placeholder="المبلغ المدفوع" class="ui-input" />
        </div>
        <div>
            <label class="mb-1.5 block text-xs font-bold text-slate-600">طريقة الدفع</label>
            <select v-model="form.payment_method" class="ui-select">
                <option v-for="(label, value) in $page.props.labels.paymentMethods" :key="value" :value="value">{{ label }}</option>
            </select>
        </div>
        <div>
            <label class="mb-1.5 block text-xs font-bold text-slate-600">حالة الحجز</label>
            <select v-model="form.status" class="ui-select">
                <option v-for="(label, value) in $page.props.labels.bookingStatuses" :key="value" :value="value">{{ label }}</option>
            </select>
        </div>
        <div class="md:col-span-2">
            <label class="mb-1.5 block text-xs font-bold text-slate-600">الملاحظات</label>
            <textarea v-model="form.notes" placeholder="الملاحظات" class="ui-textarea" />
        </div>
        <div class="rounded-2xl border border-emerald-100 bg-emerald-50/50 p-4 text-sm md:col-span-2 space-y-1">
            <p>عدد الأيام أو الليالي: <strong>{{ nights }}</strong></p>
            <p>قيمة الإيجار: <strong>{{ rentAmount }}</strong></p>
            <p>
                الخصم:
                <strong>{{ discountAmount }}</strong>
                <span v-if="form.discount_type === 'percent' && Number(form.discount_value)" class="text-slate-500">
                    ({{ form.discount_value }}% من الإيجار)
                </span>
            </p>
            <p>
                المبلغ الإضافي:
                <strong>{{ extraAmount }}</strong>
                <span v-if="form.extra_type === 'percent' && Number(form.extra_value)" class="text-slate-500">
                    ({{ form.extra_value }}% من الإيجار)
                </span>
            </p>
            <p>الإجمالي: <strong>{{ total }}</strong></p>
            <p>المبلغ المتبقي: <strong>{{ remaining }}</strong></p>
        </div>
        <p v-if="form.errors.property_id" class="text-sm text-red-600 md:col-span-2">{{ form.errors.property_id }}</p>
        <p v-if="form.errors.customer_id" class="text-sm text-red-600 md:col-span-2">{{ form.errors.customer_id }}</p>
        <p v-if="form.errors.start_date" class="text-sm text-red-600 md:col-span-2">{{ form.errors.start_date }}</p>
        <button class="ui-btn ui-btn-primary w-full md:col-span-2">حفظ الحجز</button>
    </form>
</template>
