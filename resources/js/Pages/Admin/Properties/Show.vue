<script setup>
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import AdminLayout from '../../../Layouts/AdminLayout.vue';
import PageHeader from '../../../Components/PageHeader.vue';
import StatusBadge from '../../../Components/StatusBadge.vue';
import { useCan } from '../../../composables/useCan';

defineOptions({ layout: AdminLayout });
const { can } = useCan();
const props = defineProps({ property: Object });
const statusForm = useForm({ status: props.property.status });

const changeStatus = () => statusForm.post(route('admin.properties.status', props.property.id));
const removeImage = (image) => router.delete(route('admin.properties.images.destroy', [props.property.id, image.id]));
const setPrimary = (image) => router.post(route('admin.properties.images.primary', [props.property.id, image.id]));
</script>

<template>
    <Head :title="property.name" />

    <PageHeader :title="property.name" :subtitle="`${property.code} • ${property.city || ''}`">
        <template #actions>
            <Link v-if="can('properties.update')" :href="route('admin.properties.edit', property.id)" class="ui-btn ui-btn-primary">تعديل العقار</Link>
        </template>
    </PageHeader>

    <div class="grid gap-6 lg:grid-cols-3">
        <div class="ui-panel lg:col-span-2">
            <div class="mb-4 flex flex-wrap items-center gap-2">
                <StatusBadge :value="property.status" :label="property.status_label" />
                <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-bold text-slate-700">{{ property.purpose_label }}</span>
            </div>
            <div class="grid gap-3 text-sm md:grid-cols-2">
                <p><span class="text-slate-500">الرمز:</span> <strong>{{ property.code }}</strong></p>
                <p><span class="text-slate-500">النوع:</span> <strong>{{ property.type?.name }}</strong></p>
                <p><span class="text-slate-500">المدينة:</span> <strong>{{ property.city }}</strong></p>
                <p><span class="text-slate-500">المنطقة:</span> <strong>{{ property.district }}</strong></p>
                <p class="md:col-span-2"><span class="text-slate-500">العنوان:</span> <strong>{{ property.address }}</strong></p>
                <p><span class="text-slate-500">المساحة:</span> <strong>{{ property.area }}</strong></p>
                <p><span class="text-slate-500">الغرف:</span> <strong>{{ property.rooms }}</strong></p>
                <p><span class="text-slate-500">الحمامات:</span> <strong>{{ property.bathrooms }}</strong></p>
                <p><span class="text-slate-500">السعر:</span> <strong class="text-forest">{{ property.price }}</strong></p>
                <p><span class="text-slate-500">سعر الإيجار:</span> <strong class="text-forest">{{ property.rent_price }}</strong></p>
            </div>
            <p class="mt-4 leading-8 text-slate-600">{{ property.description }}</p>
            <div class="mt-6 grid gap-3 md:grid-cols-3">
                <div v-for="image in property.images" :key="image.id" class="rounded-2xl border border-slate-100 bg-slate-50 p-2">
                    <video v-if="image.media_type === 'video'" :src="image.url" class="mb-2 h-28 w-full rounded-xl bg-slate-900 object-cover" controls playsinline preload="metadata" />
                    <img v-else :src="image.url" class="mb-2 h-28 w-full rounded-xl object-cover" />
                    <div class="flex justify-between text-xs">
                        <button v-if="image.media_type !== 'video'" class="font-bold text-forest" @click="setPrimary(image)">تعيين رئيسية</button>
                        <span v-else class="text-slate-400">فيديو</span>
                        <button class="font-bold text-rose-600" @click="removeImage(image)">حذف</button>
                    </div>
                </div>
            </div>
        </div>
        <div v-if="can('properties.change_status')" class="ui-panel h-fit">
            <h3 class="ui-section-title">تغيير حالة العقار</h3>
            <select v-model="statusForm.status" class="ui-select mb-3">
                <option v-for="(label, value) in $page.props.labels.propertyStatuses" :key="value" :value="value">{{ label }}</option>
            </select>
            <button class="ui-btn ui-btn-primary w-full" @click="changeStatus">حفظ الحالة</button>
        </div>
    </div>
</template>
