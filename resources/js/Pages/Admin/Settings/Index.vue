<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import AdminLayout from '../../../Layouts/AdminLayout.vue';
import PageHeader from '../../../Components/PageHeader.vue';

defineOptions({ layout: AdminLayout });
const props = defineProps({ values: Object });
const form = useForm({
    business_name: props.values.business_name || '',
    phone: props.values.phone || '',
    email: props.values.email || '',
    address: props.values.address || '',
    contact_info: props.values.contact_info || '',
    currency: props.values.currency || 'جنيه',
    default_rent_period: props.values.default_rent_period || 'nightly',
    notify_due_amounts: props.values.notify_due_amounts === '1',
    notify_bookings: props.values.notify_bookings === '1',
    logo: null,
});
</script>

<template>
    <Head title="الإعدادات" />

    <PageHeader title="إعدادات النظام" subtitle="بيانات النشاط والإشعارات والعملة الافتراضية" />

    <form class="ui-panel grid max-w-3xl gap-4" @submit.prevent="form.transform((data) => ({ ...data, _method: 'put' })).post(route('admin.settings.update'), { forceFormData: true })">
        <input v-model="form.business_name" placeholder="اسم النشاط" class="ui-input" required />
        <input v-model="form.phone" placeholder="رقم الهاتف" class="ui-input" />
        <input v-model="form.email" placeholder="البريد الإلكتروني" class="ui-input" />
        <input v-model="form.address" placeholder="العنوان" class="ui-input" />
        <textarea v-model="form.contact_info" placeholder="بيانات التواصل" class="ui-textarea" />
        <input v-model="form.currency" placeholder="العملة" class="ui-input" />
        <select v-model="form.default_rent_period" class="ui-select">
            <option v-for="(label, value) in $page.props.labels.rentPeriods" :key="value" :value="value">إعدادات الإيجار: {{ label }}</option>
        </select>
        <label class="ui-check-label"><input v-model="form.notify_due_amounts" type="checkbox" class="ui-check" />تنبيه المبالغ المستحقة</label>
        <label class="ui-check-label"><input v-model="form.notify_bookings" type="checkbox" class="ui-check" />تنبيه الحجوزات والإيجارات</label>
        <div>
            <label class="ui-label">شعار النشاط</label>
            <input type="file" accept="image/*" class="ui-input" @input="form.logo = $event.target.files[0]" />
        </div>
        <button class="ui-btn ui-btn-primary w-full sm:w-auto">حفظ الإعدادات</button>
    </form>
</template>
