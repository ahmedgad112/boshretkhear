<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import AdminLayout from '../../../Layouts/AdminLayout.vue';
import PageHeader from '../../../Components/PageHeader.vue';
import Pagination from '../../../Components/Pagination.vue';
import StatCard from '../../../Components/StatCard.vue';

defineOptions({ layout: AdminLayout });
const props = defineProps({
    transactions: Object,
    summary: Object,
    propertyAccounts: Array,
    filters: Object,
    properties: Array,
    customers: Array,
});
const form = useForm({
    from: props.filters.from || '',
    to: props.filters.to || '',
    property_id: props.filters.property_id || '',
    customer_id: props.filters.customer_id || '',
    type: props.filters.type || '',
});
</script>

<template>
    <Head title="الحسابات المالية" />

    <PageHeader title="الحسابات المالية" subtitle="متابعة الإيرادات والمصروفات والمعاملات" />

    <div class="mb-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <StatCard label="إجمالي الإيرادات" :value="summary.revenues" tone="success" />
        <StatCard label="إجمالي المصروفات" :value="summary.expenses" tone="danger" />
        <StatCard label="صافي الإيرادات" :value="summary.net" tone="forest" />
        <StatCard label="المبالغ المستحقة" :value="summary.due" tone="danger" />
    </div>

    <form class="ui-panel mb-6 grid gap-3 md:grid-cols-6" @submit.prevent="form.get(route('admin.accounts.index'))">
        <input v-model="form.from" type="date" class="ui-input" />
        <input v-model="form.to" type="date" class="ui-input" />
        <select v-model="form.property_id" class="ui-select">
            <option value="">كل العقارات</option>
            <option v-for="item in properties" :key="item.id" :value="item.id">{{ item.name }}</option>
        </select>
        <select v-model="form.customer_id" class="ui-select">
            <option value="">كل العملاء</option>
            <option v-for="item in customers" :key="item.id" :value="item.id">{{ item.name }}</option>
        </select>
        <select v-model="form.type" class="ui-select">
            <option value="">كل أنواع العمليات</option>
            <option v-for="(label, value) in $page.props.labels.transactionTypes" :key="value" :value="value">{{ label }}</option>
        </select>
        <button class="ui-btn ui-btn-dark">تصفية</button>
    </form>

    <div class="ui-table-wrap mb-6">
        <table class="ui-table">
            <thead class="ui-table-head">
                <tr>
                    <th class="p-4">العقار</th>
                    <th class="p-4">الإيرادات</th>
                    <th class="p-4">المصروفات</th>
                    <th class="p-4">صافي الدخل</th>
                </tr>
            </thead>
            <tbody class="ui-table-body">
                <tr v-for="item in propertyAccounts" :key="item.id" class="ui-table-row">
                    <td class="p-4 font-extrabold text-slate-900">{{ item.name }}</td>
                    <td class="p-4 font-bold text-emerald-700">{{ item.income }}</td>
                    <td class="p-4 font-bold text-rose-600">{{ item.costs }}</td>
                    <td class="p-4 font-black" :class="item.net >= 0 ? 'text-forest' : 'text-rose-700'">{{ item.net }}</td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="ui-table-wrap">
        <table class="ui-table">
            <thead class="ui-table-head">
                <tr>
                    <th class="p-4">النوع</th>
                    <th class="p-4">المبلغ</th>
                    <th class="p-4">التاريخ</th>
                    <th class="p-4">العقار</th>
                    <th class="p-4">العميل</th>
                    <th class="p-4">المستخدم</th>
                    <th class="p-4">الوصف</th>
                </tr>
            </thead>
            <tbody class="ui-table-body">
                <tr v-for="item in transactions.data" :key="item.id" class="ui-table-row">
                    <td class="p-4 font-bold text-slate-800">{{ $page.props.labels.transactionTypes[item.type] || item.type }}</td>
                    <td class="p-4 font-black" :class="item.type === 'income' ? 'text-emerald-700' : 'text-rose-600'">{{ item.amount }}</td>
                    <td class="p-4 text-xs text-slate-500">{{ item.transaction_date }}</td>
                    <td class="p-4 font-semibold text-forest">{{ item.property?.name || '-' }}</td>
                    <td class="p-4 text-slate-700">{{ item.customer?.name || '-' }}</td>
                    <td class="p-4 text-xs text-slate-500">{{ item.user?.name }}</td>
                    <td class="p-4 max-w-xs truncate text-xs text-slate-500">{{ item.description || '-' }}</td>
                </tr>
            </tbody>
        </table>
    </div>

    <Pagination :links="transactions.links" />
</template>
