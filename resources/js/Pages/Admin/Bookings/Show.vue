<script setup>
import { computed } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AdminLayout from '../../../Layouts/AdminLayout.vue';
import StatusBadge from '../../../Components/StatusBadge.vue';
import { useCan } from '../../../composables/useCan';
import { usePage } from '@inertiajs/vue3';

defineOptions({ layout: AdminLayout });

const props = defineProps({ booking: Object });
const { can } = useCan();
const page = usePage();

const labels = computed(() => page.props.labels || {});
const statusLabel = computed(() => labels.value.bookingStatuses?.[props.booking.status] || props.booking.status);
const paymentMethodLabel = computed(() => labels.value.paymentMethods?.[props.booking.payment_method] || props.booking.payment_method || '—');

const formatDate = (value) => {
    if (!value) return '—';
    return String(value).slice(0, 10);
};

const formatMoney = (value) => Number(value || 0).toLocaleString('ar-EG', {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2,
});

const discountText = computed(() => {
    const amount = formatMoney(props.booking.discount);
    if (props.booking.discount_type === 'percent' && Number(props.booking.discount_value) > 0) {
        return `${amount} (${props.booking.discount_value}%)`;
    }
    return amount;
});

const extraText = computed(() => {
    const amount = formatMoney(props.booking.extra_amount);
    if (props.booking.extra_type === 'percent' && Number(props.booking.extra_value) > 0) {
        return `${amount} (${props.booking.extra_value}%)`;
    }
    return amount;
});

const change = (status) => {
    if (props.booking.status === status) return;
    router.post(route('admin.bookings.status', props.booking.id), { status }, { preserveScroll: true });
};

const statusOptions = computed(() => ([
    { status: 'pending', label: 'قيد الانتظار', class: 'bg-amber-500 text-white hover:bg-amber-600' },
    { status: 'confirmed', label: 'مؤكد', class: 'bg-forest text-white hover:bg-forest-light' },
    { status: 'active', label: 'ساري', class: 'bg-sky-600 text-white hover:bg-sky-700' },
    { status: 'completed', label: 'منتهي', class: 'bg-indigo-600 text-white hover:bg-indigo-700' },
    { status: 'cancelled', label: 'ملغى', class: 'bg-rose-600 text-white hover:bg-rose-700' },
]));

const actions = computed(() => {
    const status = props.booking.status;

    // من ملغي أو منتهي: رجوع لأي حالة أخرى
    if (['cancelled', 'completed'].includes(status)) {
        return statusOptions.value.filter((item) => item.status !== status);
    }

    const items = [];

    if (status === 'pending') {
        items.push(
            { status: 'confirmed', label: 'تأكيد الحجز', class: 'bg-forest text-white hover:bg-forest-light' },
            { status: 'active', label: 'بدء الإيجار', class: 'bg-sky-600 text-white hover:bg-sky-700' },
        );
    }

    if (status === 'confirmed') {
        items.push(
            { status: 'active', label: 'بدء الإيجار', class: 'bg-sky-600 text-white hover:bg-sky-700' },
            { status: 'completed', label: 'إنهاء الإيجار', class: 'bg-amber-500 text-white hover:bg-amber-600' },
            { status: 'pending', label: 'إرجاع لقيد الانتظار', class: 'bg-slate-700 text-white hover:bg-slate-800' },
        );
    }

    if (status === 'active') {
        items.push(
            { status: 'completed', label: 'إنهاء الإيجار', class: 'bg-amber-500 text-white hover:bg-amber-600' },
            { status: 'confirmed', label: 'إرجاع لمؤكد', class: 'bg-teal-600 text-white hover:bg-teal-700' },
        );
    }

    if (!['cancelled', 'completed'].includes(status)) {
        items.push({ status: 'cancelled', label: 'إلغاء الحجز', class: 'bg-rose-600 text-white hover:bg-rose-700' });
    }

    return items;
});

const restoreHint = computed(() =>
    ['cancelled', 'completed'].includes(props.booking.status)
        ? 'يمكنك إرجاع الحجز لأي حالة من الحالات التالية:'
        : 'اختر الإجراء المناسب حسب مرحلة الحجز:',
);
</script>

<template>
    <Head :title="`الحجز ${booking.code}`" />

    <div class="mb-6 flex flex-wrap items-start justify-between gap-4">
        <div>
            <div class="mb-2 flex flex-wrap items-center gap-3">
                <h1 class="text-2xl font-black text-slate-900">الحجز {{ booking.code }}</h1>
                <StatusBadge :value="booking.status" :label="statusLabel" />
            </div>
            <p class="text-sm text-slate-500">
                {{ booking.property?.name || 'عقار غير محدد' }}
                <span class="mx-1">•</span>
                {{ booking.customer?.name || 'عميل غير محدد' }}
            </p>
        </div>

        <div class="flex flex-wrap gap-2">
            <Link
                :href="route('admin.bookings.index')"
                class="rounded-2xl border border-slate-200 bg-white px-4 py-2.5 text-xs font-extrabold text-slate-700 hover:bg-slate-50"
            >
                رجوع
            </Link>
            <a
                :href="route('admin.bookings.contract', booking.id)"
                target="_blank"
                class="rounded-2xl bg-slate-900 px-4 py-2.5 text-xs font-extrabold text-white hover:bg-slate-800"
            >
                طباعة العقد
            </a>
            <Link
                v-if="can('bookings.update')"
                :href="route('admin.bookings.edit', booking.id)"
                class="rounded-2xl bg-amber-500 px-4 py-2.5 text-xs font-extrabold text-white hover:bg-amber-600"
            >
                تعديل
            </Link>
        </div>
    </div>

    <div class="mb-6 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <div class="rounded-3xl border border-slate-200/80 bg-white p-5 shadow-xs">
            <p class="text-xs font-bold text-slate-500">حالة الحجز</p>
            <div class="mt-3">
                <StatusBadge :value="booking.status" :label="statusLabel" />
            </div>
            <p class="mt-3 text-xs text-slate-500">طريقة الدفع: {{ paymentMethodLabel }}</p>
        </div>
        <div class="rounded-3xl border border-slate-200/80 bg-white p-5 shadow-xs">
            <p class="text-xs font-bold text-slate-500">الإجمالي</p>
            <p class="mt-2 text-2xl font-black text-slate-900">{{ formatMoney(booking.total) }}</p>
            <p class="mt-1 text-xs text-slate-500">{{ booking.nights }} ليلة / يوم</p>
        </div>
        <div class="rounded-3xl border border-slate-200/80 bg-white p-5 shadow-xs">
            <p class="text-xs font-bold text-slate-500">المدفوع</p>
            <p class="mt-2 text-2xl font-black text-emerald-700">{{ formatMoney(booking.paid_amount) }}</p>
            <p class="mt-1 text-xs text-slate-500">من أصل {{ formatMoney(booking.total) }}</p>
        </div>
        <div class="rounded-3xl border border-slate-200/80 bg-white p-5 shadow-xs">
            <p class="text-xs font-bold text-slate-500">المتبقي</p>
            <p class="mt-2 text-2xl font-black text-rose-600">{{ formatMoney(booking.remaining_amount) }}</p>
            <p class="mt-1 text-xs text-slate-500">مستحق على العميل</p>
        </div>
    </div>

    <div class="grid gap-6 xl:grid-cols-3">
        <section class="xl:col-span-2 space-y-6">
            <div class="rounded-3xl border border-slate-200/80 bg-white p-6 shadow-xs">
                <h2 class="mb-4 text-sm font-black text-slate-900">بيانات الحجز</h2>
                <div class="grid gap-4 sm:grid-cols-2">
                    <div class="rounded-2xl bg-slate-50 p-4">
                        <p class="text-xs font-bold text-slate-500">العقار</p>
                        <p class="mt-1 font-extrabold text-forest">{{ booking.property?.name || '—' }}</p>
                        <p class="mt-1 text-xs text-slate-500">{{ booking.property?.code || '' }}</p>
                    </div>
                    <div class="rounded-2xl bg-slate-50 p-4">
                        <p class="text-xs font-bold text-slate-500">العميل</p>
                        <p class="mt-1 font-extrabold text-slate-900">{{ booking.customer?.name || '—' }}</p>
                        <p class="mt-1 text-xs text-slate-500">{{ booking.customer?.phone || '' }}</p>
                    </div>
                    <div class="rounded-2xl bg-slate-50 p-4">
                        <p class="text-xs font-bold text-slate-500">تاريخ البداية</p>
                        <p class="mt-1 font-bold text-slate-900">{{ formatDate(booking.start_date) }}</p>
                    </div>
                    <div class="rounded-2xl bg-slate-50 p-4">
                        <p class="text-xs font-bold text-slate-500">تاريخ النهاية</p>
                        <p class="mt-1 font-bold text-slate-900">{{ formatDate(booking.end_date) }}</p>
                    </div>
                </div>
            </div>

            <div class="rounded-3xl border border-slate-200/80 bg-white p-6 shadow-xs">
                <h2 class="mb-4 text-sm font-black text-slate-900">التفاصيل المالية</h2>
                <div class="overflow-hidden rounded-2xl border border-slate-100">
                    <table class="min-w-full text-sm">
                        <tbody class="divide-y divide-slate-100">
                            <tr>
                                <td class="bg-slate-50 px-4 py-3 font-bold text-slate-600">سعر الليلة / اليوم</td>
                                <td class="px-4 py-3 font-extrabold text-slate-900">{{ formatMoney(booking.nightly_rate) }}</td>
                            </tr>
                            <tr>
                                <td class="bg-slate-50 px-4 py-3 font-bold text-slate-600">قيمة الإيجار</td>
                                <td class="px-4 py-3 font-extrabold text-slate-900">{{ formatMoney(booking.rent_amount) }}</td>
                            </tr>
                            <tr>
                                <td class="bg-slate-50 px-4 py-3 font-bold text-slate-600">الخصم</td>
                                <td class="px-4 py-3 font-extrabold text-rose-600">{{ discountText }}</td>
                            </tr>
                            <tr>
                                <td class="bg-slate-50 px-4 py-3 font-bold text-slate-600">المبلغ الإضافي</td>
                                <td class="px-4 py-3 font-extrabold text-sky-700">{{ extraText }}</td>
                            </tr>
                            <tr>
                                <td class="bg-slate-50 px-4 py-3 font-bold text-slate-600">الإجمالي</td>
                                <td class="px-4 py-3 font-black text-forest">{{ formatMoney(booking.total) }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <p v-if="booking.notes" class="mt-4 rounded-2xl bg-amber-50 px-4 py-3 text-sm text-amber-800">
                    <strong>ملاحظات:</strong> {{ booking.notes }}
                </p>
            </div>
        </section>

        <section class="space-y-6">
            <div v-if="can('bookings.update') && actions.length" class="rounded-3xl border border-slate-200/80 bg-white p-6 shadow-xs">
                <h2 class="mb-2 text-sm font-black text-slate-900">تغيير الحالة</h2>
                <p class="mb-2 text-xs text-slate-500">
                    الحالة الحالية:
                    <StatusBadge :value="booking.status" :label="statusLabel" />
                </p>
                <p class="mb-4 text-xs text-slate-500">{{ restoreHint }}</p>
                <div class="flex flex-col gap-2">
                    <button
                        v-for="action in actions"
                        :key="action.status"
                        type="button"
                        class="rounded-2xl px-4 py-2.5 text-xs font-extrabold transition-colors"
                        :class="action.class"
                        @click="change(action.status)"
                    >
                        {{ action.label }}
                    </button>
                </div>
            </div>

            <div class="rounded-3xl border border-slate-200/80 bg-white p-6 shadow-xs">
                <div class="mb-4 flex items-center justify-between gap-3">
                    <h2 class="text-sm font-black text-slate-900">المدفوعات</h2>
                    <span class="rounded-full bg-slate-100 px-2.5 py-1 text-[11px] font-bold text-slate-600">
                        {{ booking.payments?.length || 0 }}
                    </span>
                </div>

                <div v-if="booking.payments?.length" class="space-y-3">
                    <div
                        v-for="item in booking.payments"
                        :key="item.id"
                        class="rounded-2xl border border-slate-100 bg-slate-50/80 p-4"
                    >
                        <div class="flex items-center justify-between gap-3">
                            <p class="text-sm font-black text-slate-900">{{ item.code }}</p>
                            <p class="text-sm font-extrabold text-emerald-700">{{ formatMoney(item.amount) }}</p>
                        </div>
                        <p class="mt-1 text-xs text-slate-500">
                            {{ formatDate(item.paid_at) }}
                            <span v-if="item.payment_method">
                                • {{ labels.paymentMethods?.[item.payment_method] || item.payment_method }}
                            </span>
                        </p>
                    </div>
                </div>
                <p v-else class="rounded-2xl border border-dashed border-slate-200 px-4 py-8 text-center text-sm text-slate-500">
                    لا توجد مدفوعات مسجلة على هذا الحجز.
                </p>

                <Link
                    v-if="can('payments.create') && Number(booking.remaining_amount) > 0"
                    :href="route('admin.payments.index', { source_type: 'booking', source_id: booking.id })"
                    class="mt-4 inline-flex w-full items-center justify-center rounded-2xl border border-forest/20 bg-emerald-50 px-4 py-2.5 text-xs font-extrabold text-forest hover:bg-emerald-100"
                >
                    تسجيل دفعة جديدة
                </Link>
                <p
                    v-else-if="can('payments.create') && Number(booking.remaining_amount) <= 0"
                    class="mt-4 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-2.5 text-center text-xs font-bold text-emerald-700"
                >
                    لا يوجد مبلغ متبقي على هذا الحجز
                </p>
            </div>
        </section>
    </div>
</template>
