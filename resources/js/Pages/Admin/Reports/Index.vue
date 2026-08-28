<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import AdminLayout from '../../../Layouts/AdminLayout.vue';
import PageHeader from '../../../Components/PageHeader.vue';

defineOptions({ layout: AdminLayout });
const props = defineProps({
    type: String,
    rows: Array,
    filters: Object,
    properties: Array,
    customers: Array,
    users: Array,
});

const form = useForm({
    type: props.type,
    from: props.filters.from || '',
    to: props.filters.to || '',
    property_id: props.filters.property_id || '',
    customer_id: props.filters.customer_id || '',
    user_id: props.filters.user_id || '',
});

const types = {
    properties: 'تقرير العقارات',
    bookings: 'تقرير الإيجارات',
    sales: 'تقرير المبيعات',
    customers: 'تقرير العملاء',
    payments: 'تقرير المدفوعات',
    expenses: 'تقرير المصروفات',
    revenues: 'تقرير الإيرادات',
    profits: 'تقرير صافي الأرباح',
    dues: 'تقرير المبالغ المستحقة',
};

const headers = props.rows?.[0] ? Object.keys(props.rows[0]) : [];
const exportUrl = (format) => {
    const query = new URLSearchParams({ ...form.data(), format }).toString();
    return `${route('admin.reports.export')}?${query}`;
};
</script>

<template>
    <Head title="التقارير" />

    <PageHeader title="التقارير الإحصائية" subtitle="تصفية وتصدير بيانات النشاط بصيغ مختلفة" />

    <form class="ui-panel mb-6 grid gap-3 md:grid-cols-6" @submit.prevent="form.get(route('admin.reports.index'))">
        <select v-model="form.type" class="ui-select">
            <option v-for="(label, value) in types" :key="value" :value="value">{{ label }}</option>
        </select>
        <input v-model="form.from" type="date" class="ui-input" />
        <input v-model="form.to" type="date" class="ui-input" />
        <select v-model="form.property_id" class="ui-select">
            <option value="">العقار</option>
            <option v-for="item in properties" :key="item.id" :value="item.id">{{ item.name }}</option>
        </select>
        <select v-model="form.customer_id" class="ui-select">
            <option value="">العميل</option>
            <option v-for="item in customers" :key="item.id" :value="item.id">{{ item.name }}</option>
        </select>
        <button class="ui-btn ui-btn-dark">عرض التقرير</button>
    </form>

    <div class="mb-4 flex flex-wrap gap-3">
        <a :href="exportUrl('xlsx')" class="ui-btn ui-btn-primary">تصدير ملف إكسل</a>
        <a :href="exportUrl('pdf')" class="ui-btn ui-btn-gold">تصدير ملف بي دي إف</a>
    </div>

    <div class="ui-table-wrap !block overflow-x-auto">
        <table class="ui-table">
            <thead class="ui-table-head">
                <tr>
                    <th v-for="header in headers" :key="header" class="p-4">{{ header }}</th>
                </tr>
            </thead>
            <tbody class="ui-table-body">
                <tr v-for="(row, index) in rows" :key="index" class="ui-table-row">
                    <td v-for="header in headers" :key="header" class="p-4 text-slate-700">{{ row[header] }}</td>
                </tr>
            </tbody>
        </table>
        <p v-if="!rows.length" class="ui-empty border-0">لا توجد بيانات في هذا التقرير.</p>
    </div>
</template>
