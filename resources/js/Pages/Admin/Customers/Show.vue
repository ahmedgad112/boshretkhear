<script setup>
import { Head } from '@inertiajs/vue3';
import AdminLayout from '../../../Layouts/AdminLayout.vue';
import PageHeader from '../../../Components/PageHeader.vue';
import StatCard from '../../../Components/StatCard.vue';

defineOptions({ layout: AdminLayout });
defineProps({
    customer: Object,
    rented: Array,
    purchased: Array,
    due: Number,
    total_paid: Number,
    total_deals: Number,
});
</script>

<template>
    <Head :title="customer.name" />

    <PageHeader :title="customer.name" subtitle="ملخص التعاملات والحجوزات والمدفوعات" />

    <div class="mb-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <StatCard label="المبالغ المستحقة" :value="due" tone="danger" />
        <StatCard label="إجمالي المدفوعات" :value="total_paid" tone="success" />
        <StatCard label="إجمالي التعاملات المالية" :value="total_deals" />
        <StatCard label="الهاتف" :value="customer.phone || '-'" tone="forest" />
    </div>

    <div v-if="customer.id_card_url" class="ui-panel mb-6">
        <h2 class="ui-section-title">صورة بطاقة العميل</h2>
        <img :src="customer.id_card_url" alt="صورة بطاقة العميل" class="max-h-72 rounded-2xl border border-slate-200 object-contain" />
    </div>

    <div class="grid gap-6 lg:grid-cols-2">
        <section class="ui-panel">
            <h2 class="ui-section-title">العقارات التي استأجرها</h2>
            <p v-for="item in rented" :key="item.id" class="ui-list-item">{{ item.property?.name }} - {{ item.code }}</p>
            <p v-if="!rented.length" class="text-sm text-slate-500">لا توجد إيجارات مسجلة.</p>
        </section>
        <section class="ui-panel">
            <h2 class="ui-section-title">العقارات التي اشتراها</h2>
            <p v-for="item in purchased" :key="item.id" class="ui-list-item">{{ item.property?.name }} - {{ item.code }}</p>
            <p v-if="!purchased.length" class="text-sm text-slate-500">لا توجد مبيعات مسجلة.</p>
        </section>
        <section class="ui-panel">
            <h2 class="ui-section-title">الحجوزات</h2>
            <p v-for="item in customer.bookings" :key="item.id" class="ui-list-item">{{ item.code }} - المتبقي {{ item.remaining_amount }}</p>
            <p v-if="!customer.bookings?.length" class="text-sm text-slate-500">لا توجد حجوزات.</p>
        </section>
        <section class="ui-panel">
            <h2 class="ui-section-title">المدفوعات</h2>
            <p v-for="item in [...customer.booking_payments, ...customer.sale_payments]" :key="item.id" class="ui-list-item">{{ item.code }} - {{ item.amount }}</p>
            <p v-if="!customer.booking_payments?.length && !customer.sale_payments?.length" class="text-sm text-slate-500">لا توجد مدفوعات.</p>
        </section>
    </div>
</template>
