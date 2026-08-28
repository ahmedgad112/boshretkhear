<script setup>
import { computed, nextTick, onMounted, ref, watch } from 'vue';
import { Head, useForm, usePage } from '@inertiajs/vue3';
import AdminLayout from '../../../Layouts/AdminLayout.vue';
import SearchableSelect from '../../../Components/SearchableSelect.vue';
import { useCan } from '../../../composables/useCan';

defineOptions({ layout: AdminLayout });

const props = defineProps({
    payments: Array,
    bookings: Array,
    sales: Array,
    filters: Object,
    prefill: { type: Object, default: () => ({}) },
});

const { can } = useCan();
const page = usePage();
const amountInput = ref(null);

const searchForm = useForm({
    q: props.filters?.q || '',
});

const initialType = props.prefill?.source_type || 'booking';
const initialId = props.prefill?.source_id || '';

const form = useForm({
    source_type: initialType,
    source_id: initialId,
    amount: '',
    paid_at: new Date().toISOString().slice(0, 10),
    payment_method: 'cash',
    reference_number: '',
    notes: '',
});

const formatMoney = (value) => Number(value || 0).toLocaleString('ar-EG', {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2,
});

const paymentMethodLabel = (value) => page.props.labels?.paymentMethods?.[value] || value || '—';

const sources = computed(() =>
    form.source_type === 'booking' ? props.bookings : props.sales,
);

const selectedSource = computed(() =>
    sources.value.find((item) => String(item.id) === String(form.source_id)) || null,
);

const isPrefillMode = computed(() => Boolean(props.prefill?.source_type && props.prefill?.source_id));

const needsReference = computed(() => form.payment_method !== 'cash');

const stats = computed(() => {
    const list = props.payments || [];
    const total = list.reduce((sum, item) => sum + Number(item.amount || 0), 0);
    const bookingTotal = list
        .filter((item) => item.kind === 'حجز')
        .reduce((sum, item) => sum + Number(item.amount || 0), 0);
    const saleTotal = list
        .filter((item) => item.kind === 'بيع')
        .reduce((sum, item) => sum + Number(item.amount || 0), 0);

    return {
        count: list.length,
        total,
        bookingTotal,
        saleTotal,
        dueBookings: props.bookings?.length || 0,
        dueSales: props.sales?.length || 0,
    };
});

watch(() => form.source_type, (type, oldType) => {
    if (type === oldType) return;
    form.source_id = '';
    form.amount = '';
});

watch(() => form.payment_method, (method) => {
    if (method === 'cash') {
        form.reference_number = '';
    }
});

const submit = () => {
    form.post(route('admin.payments.store'), {
        preserveScroll: true,
        onSuccess: () => {
            form.reset('amount', 'reference_number', 'notes');
            form.paid_at = new Date().toISOString().slice(0, 10);
            form.payment_method = 'cash';

            if (!isPrefillMode.value) {
                form.source_type = 'booking';
                form.source_id = '';
            }
        },
    });
};

onMounted(async () => {
    if (isPrefillMode.value) {
        await nextTick();
        amountInput.value?.focus();
    }
});
</script>

<template>
    <Head title="المدفوعات" />

    <div class="mb-6">
        <h1 class="text-2xl font-black text-slate-900">المدفوعات</h1>
        <p class="mt-1 text-xs text-slate-500">تسجيل ومتابعة دفعات الحجوزات والمبيعات</p>
    </div>

    <div class="mb-6 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <div class="rounded-3xl border border-slate-200/80 bg-white p-5 shadow-xs">
            <p class="text-xs font-bold text-slate-500">إجمالي المدفوعات</p>
            <p class="mt-2 text-2xl font-black text-emerald-700">{{ formatMoney(stats.total) }}</p>
            <p class="mt-1 text-xs text-slate-500">{{ stats.count }} عملية</p>
        </div>
        <div class="rounded-3xl border border-slate-200/80 bg-white p-5 shadow-xs">
            <p class="text-xs font-bold text-slate-500">دفعات الحجوزات</p>
            <p class="mt-2 text-2xl font-black text-forest">{{ formatMoney(stats.bookingTotal) }}</p>
            <p class="mt-1 text-xs text-slate-500">مستحقات مفتوحة: {{ stats.dueBookings }}</p>
        </div>
        <div class="rounded-3xl border border-slate-200/80 bg-white p-5 shadow-xs">
            <p class="text-xs font-bold text-slate-500">دفعات المبيعات</p>
            <p class="mt-2 text-2xl font-black text-sky-700">{{ formatMoney(stats.saleTotal) }}</p>
            <p class="mt-1 text-xs text-slate-500">مستحقات مفتوحة: {{ stats.dueSales }}</p>
        </div>
        <div class="rounded-3xl border border-slate-200/80 bg-white p-5 shadow-xs">
            <p class="text-xs font-bold text-slate-500">المستحقات المفتوحة</p>
            <p class="mt-2 text-2xl font-black text-amber-600">{{ stats.dueBookings + stats.dueSales }}</p>
            <p class="mt-1 text-xs text-slate-500">حجوزات ومبيعات عليها متبقي</p>
        </div>
    </div>

    <form
        v-if="can('payments.create')"
        class="mb-6 rounded-3xl border border-slate-200/80 bg-white p-6 shadow-xs"
        @submit.prevent="submit"
    >
        <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
            <div>
                <h2 class="text-sm font-black text-slate-900">
                    {{ isPrefillMode ? 'تسجيل دفعة على الحجز المحدد' : 'تسجيل دفعة جديدة' }}
                </h2>
                <p class="mt-1 text-xs text-slate-500">
                    <template v-if="isPrefillMode && selectedSource">
                        الحجز {{ selectedSource.code }} — {{ selectedSource.customer_name || 'بدون عميل' }}
                        • المتبقي {{ formatMoney(selectedSource.remaining_amount) }} — اكتب المبلغ فقط
                    </template>
                    <template v-else>
                        اختر الحجز أو البيع ثم سجّل المبلغ المستلم
                    </template>
                </p>
            </div>
            <div v-if="selectedSource" class="rounded-2xl bg-emerald-50 px-3 py-2 text-xs font-bold text-forest">
                المتبقي: {{ formatMoney(selectedSource.remaining_amount) }}
            </div>
        </div>

        <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
            <div>
                <label class="mb-1.5 block text-xs font-bold text-slate-600">نوع العملية</label>
                <select
                    v-model="form.source_type"
                    class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-2.5 text-sm disabled:bg-slate-50 disabled:text-slate-500"
                    :disabled="isPrefillMode"
                >
                    <option value="booking">حجز / إيجار</option>
                    <option value="sale">عملية بيع</option>
                </select>
            </div>

            <SearchableSelect
                v-model="form.source_id"
                :items="sources"
                :label="form.source_type === 'booking' ? 'الحجز' : 'عملية البيع'"
                :placeholder="form.source_type === 'booking' ? 'ابحث برمز الحجز أو اسم العميل...' : 'ابحث برمز البيع أو اسم العميل...'"
                :empty-text="form.source_type === 'booking' ? 'لا يوجد حجز بهذا البحث' : 'لا توجد عملية بيع بهذا البحث'"
                required
                :get-label="(item) => `${item.code} — ${item.customer_name || 'بدون عميل'}`"
                :get-sub-label="(item) => `المتبقي: ${formatMoney(item.remaining_amount)}`"
                :get-search-text="(item) => `${item.code || ''} ${item.customer_name || ''} ${item.customer_phone || ''}`"
            />

            <div>
                <label class="mb-1.5 block text-xs font-bold text-slate-600">المبلغ</label>
                <input
                    ref="amountInput"
                    v-model="form.amount"
                    type="number"
                    min="0.01"
                    step="0.01"
                    placeholder="اكتب المبلغ هنا"
                    class="w-full rounded-2xl border border-forest/30 bg-white px-4 py-2.5 text-sm ring-2 ring-forest/10 focus:border-forest focus:outline-none focus:ring-forest/20"
                    required
                />
            </div>

            <div>
                <label class="mb-1.5 block text-xs font-bold text-slate-600">تاريخ الدفع</label>
                <input
                    v-model="form.paid_at"
                    type="date"
                    class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-2.5 text-sm"
                    required
                />
            </div>

            <div>
                <label class="mb-1.5 block text-xs font-bold text-slate-600">طريقة الدفع</label>
                <select v-model="form.payment_method" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-2.5 text-sm">
                    <option v-for="(label, value) in $page.props.labels.paymentMethods" :key="value" :value="value">{{ label }}</option>
                </select>
            </div>

            <div v-if="needsReference">
                <label class="mb-1.5 block text-xs font-bold text-slate-600">رقم العملية / المرجع</label>
                <input
                    v-model="form.reference_number"
                    placeholder="رقم التحويل أو العملية"
                    class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-2.5 text-sm"
                />
            </div>

            <div class="md:col-span-2 xl:col-span-3">
                <label class="mb-1.5 block text-xs font-bold text-slate-600">ملاحظات</label>
                <input
                    v-model="form.notes"
                    placeholder="ملاحظات إضافية"
                    class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-2.5 text-sm"
                />
            </div>
        </div>

        <div class="mt-4 flex flex-wrap items-center gap-3">
            <button class="rounded-2xl bg-forest px-5 py-2.5 text-xs font-extrabold text-white hover:bg-forest-light">
                تسجيل الدفعة
            </button>
            <p v-if="form.errors.amount" class="text-sm text-rose-600">{{ form.errors.amount }}</p>
            <p v-if="form.errors.source_id" class="text-sm text-rose-600">{{ form.errors.source_id }}</p>
        </div>
    </form>

    <form class="mb-6 flex max-w-xl gap-3" @submit.prevent="searchForm.get(route('admin.payments.index'))">
        <input
            v-model="searchForm.q"
            type="search"
            placeholder="بحث برمز الدفعة..."
            class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-2.5 text-sm"
        />
        <button class="shrink-0 rounded-2xl bg-slate-900 px-5 text-xs font-extrabold text-white hover:bg-slate-800">
            بحث
        </button>
    </form>

    <div class="hidden overflow-hidden rounded-3xl border border-slate-200/80 bg-white shadow-xs md:block">
        <table class="min-w-full text-right text-sm">
            <thead class="border-b border-slate-200/80 bg-slate-50 text-xs font-black text-slate-500">
                <tr>
                    <th class="p-4">الرمز</th>
                    <th class="p-4">النوع</th>
                    <th class="p-4">العميل</th>
                    <th class="p-4">العقار</th>
                    <th class="p-4">المبلغ</th>
                    <th class="p-4">طريقة الدفع</th>
                    <th class="p-4">التاريخ</th>
                    <th class="p-4">بواسطة</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                <tr v-for="item in payments" :key="`${item.kind}-${item.id}`" class="hover:bg-slate-50/60 transition-colors">
                    <td class="p-4 font-black text-slate-900">{{ item.code }}</td>
                    <td class="p-4">
                        <span
                            class="inline-flex rounded-full border px-2.5 py-1 text-[11px] font-bold"
                            :class="item.kind === 'حجز'
                                ? 'border-emerald-200 bg-emerald-50 text-emerald-700'
                                : 'border-sky-200 bg-sky-50 text-sky-700'"
                        >
                            {{ item.kind }} / {{ item.reference }}
                        </span>
                    </td>
                    <td class="p-4 font-bold text-slate-900">{{ item.customer || '—' }}</td>
                    <td class="p-4 font-semibold text-forest">{{ item.property || '—' }}</td>
                    <td class="p-4 font-black text-emerald-700">{{ formatMoney(item.amount) }}</td>
                    <td class="p-4 text-xs font-bold text-slate-600">{{ paymentMethodLabel(item.payment_method) }}</td>
                    <td class="p-4 text-xs text-slate-500">{{ item.paid_at }}</td>
                    <td class="p-4 text-xs text-slate-500">{{ item.creator || '—' }}</td>
                </tr>
            </tbody>
        </table>
        <p v-if="!payments.length" class="p-8 text-center text-sm text-slate-500">لا توجد مدفوعات مسجلة.</p>
    </div>

    <div class="grid gap-4 md:hidden">
        <div
            v-for="item in payments"
            :key="`${item.kind}-${item.id}`"
            class="space-y-3 rounded-3xl border border-slate-200/80 bg-white p-5 shadow-xs"
        >
            <div class="flex items-center justify-between border-b border-slate-100 pb-2">
                <span class="text-sm font-black text-slate-900">{{ item.code }}</span>
                <span class="rounded-full border border-emerald-200 bg-emerald-50 px-2.5 py-1 text-xs font-black text-emerald-700">
                    {{ formatMoney(item.amount) }}
                </span>
            </div>
            <div class="space-y-1 text-xs text-slate-600">
                <p>
                    النوع:
                    <strong class="text-slate-900">{{ item.kind }} ({{ item.reference }})</strong>
                </p>
                <p>العميل: <strong class="text-slate-900">{{ item.customer || '—' }}</strong></p>
                <p>العقار: <strong class="text-forest">{{ item.property || '—' }}</strong></p>
                <p>طريقة الدفع: <strong>{{ paymentMethodLabel(item.payment_method) }}</strong></p>
                <p>التاريخ: <span>{{ item.paid_at }}</span></p>
                <p>بواسطة: <span>{{ item.creator || '—' }}</span></p>
            </div>
        </div>
        <p v-if="!payments.length" class="rounded-3xl border border-dashed border-slate-200 bg-white p-8 text-center text-sm text-slate-500">
            لا توجد مدفوعات مسجلة.
        </p>
    </div>
</template>
