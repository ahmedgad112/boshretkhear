<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import AdminLayout from '../../../Layouts/AdminLayout.vue';
import PageHeader from '../../../Components/PageHeader.vue';
import StatCard from '../../../Components/StatCard.vue';

defineOptions({ layout: AdminLayout });
const props = defineProps({ sale: Object });
</script>

<template>
    <Head :title="sale.code" />

    <PageHeader :title="`عملية البيع ${sale.code}`" subtitle="تفاصيل العقد والمدفوعات المرتبطة">
        <template #actions>
            <Link :href="route('admin.sales.edit', sale.id)" class="ui-btn ui-btn-secondary">تعديل</Link>
        </template>
    </PageHeader>

    <div class="mb-6 grid gap-4 sm:grid-cols-3">
        <StatCard label="السعر النهائي" :value="sale.final_price" />
        <StatCard label="المدفوع" :value="sale.paid_amount" tone="success" />
        <StatCard label="المتبقي" :value="sale.remaining_amount" tone="danger" />
    </div>

    <div class="grid gap-6 lg:grid-cols-2">
        <section class="ui-panel space-y-2 text-sm text-slate-700">
            <p>العقار: <strong class="text-forest">{{ sale.property?.name }}</strong></p>
            <p>العميل: <strong class="text-slate-900">{{ sale.customer?.name }}</strong></p>
            <button v-if="sale.status !== 'completed'" class="ui-btn ui-btn-primary mt-4" @click="router.post(route('admin.sales.complete', sale.id))">إتمام البيع</button>
        </section>
        <section class="ui-panel">
            <h2 class="ui-section-title">المدفوعات</h2>
            <p v-for="item in sale.payments" :key="item.id" class="ui-list-item">{{ item.code }} - {{ item.amount }}</p>
            <p v-if="!sale.payments?.length" class="text-sm text-slate-500">لا توجد مدفوعات بعد.</p>
        </section>
    </div>
</template>
