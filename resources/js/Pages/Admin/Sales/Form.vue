<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import { computed } from 'vue';
import AdminLayout from '../../../Layouts/AdminLayout.vue';
import PageHeader from '../../../Components/PageHeader.vue';

defineOptions({ layout: AdminLayout });
const props = defineProps({ sale: { type: Object, default: null }, properties: Array, customers: Array });
const form = useForm({
    property_id: props.sale?.property_id || '',
    customer_id: props.sale?.customer_id || '',
    sale_price: props.sale?.sale_price || '',
    discount: props.sale?.discount || 0,
    sale_date: props.sale?.sale_date || '',
    payment_method: props.sale?.payment_method || 'cash',
    status: props.sale?.status || 'pending',
    notes: props.sale?.notes || '',
    initial_payment: '',
});
const finalPrice = computed(() => Number(form.sale_price || 0) - Number(form.discount || 0));
const remaining = computed(() => finalPrice.value - Number(form.initial_payment || props.sale?.paid_amount || 0));
const submit = () => props.sale ? form.put(route('admin.sales.update', props.sale.id)) : form.post(route('admin.sales.store'));
</script>

<template>
    <Head :title="sale ? 'تعديل بيع' : 'عملية بيع'" />

    <PageHeader :title="sale ? 'تعديل عملية البيع' : 'عملية بيع جديدة'" subtitle="تسجيل بيانات البيع والمبالغ المستحقة" />

    <form class="ui-panel grid gap-4 md:grid-cols-2" @submit.prevent="submit">
        <div>
            <label class="ui-label">العقار</label>
            <select v-model="form.property_id" class="ui-select" required>
                <option value="">اختر العقار</option>
                <option v-for="item in properties" :key="item.id" :value="item.id">{{ item.name }}</option>
            </select>
        </div>
        <div>
            <label class="ui-label">العميل</label>
            <select v-model="form.customer_id" class="ui-select" required>
                <option value="">اختر العميل</option>
                <option v-for="item in customers" :key="item.id" :value="item.id">{{ item.name }}</option>
            </select>
        </div>
        <input v-model="form.sale_price" placeholder="سعر البيع" class="ui-input" required />
        <input v-model="form.discount" placeholder="الخصم" class="ui-input" />
        <input v-model="form.sale_date" type="date" class="ui-input" required />
        <input v-if="!sale" v-model="form.initial_payment" placeholder="المبلغ المدفوع" class="ui-input" />
        <select v-model="form.payment_method" class="ui-select">
            <option v-for="(label, value) in $page.props.labels.paymentMethods" :key="value" :value="value">{{ label }}</option>
        </select>
        <select v-model="form.status" class="ui-select">
            <option v-for="(label, value) in $page.props.labels.saleStatuses" :key="value" :value="value">{{ label }}</option>
        </select>
        <textarea v-model="form.notes" placeholder="الملاحظات" class="ui-textarea md:col-span-2" />
        <div class="rounded-2xl border border-emerald-100 bg-emerald-50/50 p-4 text-sm md:col-span-2">
            <p>السعر النهائي: <strong>{{ finalPrice }}</strong></p>
            <p>المبلغ المتبقي: <strong>{{ remaining }}</strong></p>
        </div>
        <button class="ui-btn ui-btn-primary w-full md:col-span-2">حفظ عملية البيع</button>
    </form>
</template>
