<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import { onBeforeUnmount, ref } from 'vue';
import AdminLayout from '../../../Layouts/AdminLayout.vue';
import PageHeader from '../../../Components/PageHeader.vue';

defineOptions({ layout: AdminLayout });

const props = defineProps({ customer: { type: Object, default: null } });

const idCardInput = ref(null);
const idCardPreview = ref(props.customer?.id_card_url || null);

const form = useForm({
    name: props.customer?.name || '',
    phone: props.customer?.phone || '',
    phone_secondary: props.customer?.phone_secondary || '',
    address: props.customer?.address || '',
    national_id: props.customer?.national_id || '',
    notes: props.customer?.notes || '',
    id_card_image: null,
    remove_id_card: false,
});

const revokePreview = () => {
    if (idCardPreview.value?.startsWith('blob:')) {
        URL.revokeObjectURL(idCardPreview.value);
    }
};

const onIdCardChange = (event) => {
    const file = event.target.files?.[0];
    if (!file) return;

    revokePreview();
    form.id_card_image = file;
    form.remove_id_card = false;
    idCardPreview.value = URL.createObjectURL(file);
    event.target.value = '';
};

const openIdCardPicker = () => idCardInput.value?.click();

const clearIdCard = () => {
    revokePreview();
    form.id_card_image = null;
    form.remove_id_card = true;
    idCardPreview.value = null;
    if (idCardInput.value) idCardInput.value.value = '';
};

const submit = () => {
    const options = { forceFormData: true };
    if (props.customer) {
        form.transform((data) => ({ ...data, _method: 'put' })).post(route('admin.customers.update', props.customer.id), options);
    } else {
        form.post(route('admin.customers.store'), options);
    }
};

onBeforeUnmount(revokePreview);
</script>

<template>
    <Head :title="customer ? 'تعديل عميل' : 'إضافة عميل'" />

    <PageHeader :title="customer ? 'تعديل عميل' : 'إضافة عميل'" subtitle="بيانات التواصل والهوية والملاحظات" />

    <form class="ui-panel grid max-w-3xl gap-4 md:grid-cols-2" @submit.prevent="submit">
        <input v-model="form.name" placeholder="الاسم" class="ui-input" required />
        <input v-model="form.phone" placeholder="رقم الهاتف" class="ui-input" required />
        <input v-model="form.phone_secondary" placeholder="رقم هاتف إضافي" class="ui-input" />
        <input v-model="form.national_id" placeholder="الرقم القومي" class="ui-input" />
        <input v-model="form.address" placeholder="العنوان" class="ui-input md:col-span-2" />
        <textarea v-model="form.notes" placeholder="الملاحظات" class="ui-textarea md:col-span-2" />

        <div class="md:col-span-2">
            <label class="ui-label">صورة بطاقة العميل <span class="font-normal text-slate-400">(اختياري)</span></label>
            <input ref="idCardInput" type="file" accept="image/*" class="hidden" @change="onIdCardChange" />
            <div class="ui-file-drop">
                <div class="flex flex-col items-center gap-3 text-center sm:flex-row sm:justify-between sm:text-right">
                    <div class="space-y-1">
                        <p class="text-sm font-semibold text-slate-800">ارفع صورة البطاقة أو الهوية</p>
                        <p class="text-xs text-slate-500">صور فقط (JPG, PNG, WEBP) — الحد الأقصى 5 ميغابايت.</p>
                    </div>
                    <div class="flex shrink-0 gap-2">
                        <button type="button" class="ui-btn ui-btn-primary" @click="openIdCardPicker">
                            {{ idCardPreview ? 'تغيير الصورة' : 'اختيار صورة' }}
                        </button>
                        <button v-if="idCardPreview" type="button" class="ui-btn ui-btn-secondary text-rose-600" @click="clearIdCard">حذف</button>
                    </div>
                </div>
                <div v-if="idCardPreview" class="mt-4 border-t border-slate-100 pt-4">
                    <img :src="idCardPreview" alt="صورة بطاقة العميل" class="mx-auto max-h-56 rounded-2xl border border-slate-200 bg-white object-contain" />
                </div>
            </div>
        </div>

        <button class="ui-btn ui-btn-primary w-full md:col-span-2" :disabled="form.processing">حفظ</button>
    </form>
</template>
