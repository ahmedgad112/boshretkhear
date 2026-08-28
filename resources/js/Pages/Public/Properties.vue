<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import PublicLayout from '../../Layouts/PublicLayout.vue';
import PropertyCard from '../../Components/PropertyCard.vue';
import Pagination from '../../Components/Pagination.vue';

defineOptions({ layout: PublicLayout });

const props = defineProps({
    properties: Object,
    filters: Object,
    types: Array,
    cities: Array,
});

const form = useForm({
    q: props.filters.q || '',
    purpose: props.filters.purpose || '',
    property_type_id: props.filters.property_type_id || '',
    city: props.filters.city || '',
    district: props.filters.district || '',
    price_from: props.filters.price_from || '',
    price_to: props.filters.price_to || '',
    area_from: props.filters.area_from || '',
    area_to: props.filters.area_to || '',
    rooms: props.filters.rooms || '',
    bathrooms: props.filters.bathrooms || '',
    status: props.filters.status || '',
});

const search = () => form.get(route('properties.index'), { preserveState: true });
</script>

<template>
    <Head title="العقارات" />

    <div class="mb-8">
        <span class="text-xs font-bold tracking-wider text-amber-600">استكشف العقارات</span>
        <h1 class="text-3xl font-black text-slate-900 md:text-4xl">جميع العقارات</h1>
        <p class="mt-1 text-sm text-slate-500">استخدم التصفية للوصول إلى العقار المناسب بسرعة.</p>
    </div>

    <form class="ui-panel mb-8 grid gap-3 md:grid-cols-4" @submit.prevent="search">
        <input v-model="form.q" placeholder="كلمة البحث" class="ui-input" />
        <select v-model="form.purpose" class="ui-select">
            <option value="">للبيع أو للإيجار</option>
            <option value="sale">للبيع</option>
            <option value="rent">للإيجار</option>
        </select>
        <select v-model="form.property_type_id" class="ui-select">
            <option value="">نوع العقار</option>
            <option v-for="type in types" :key="type.id" :value="type.id">{{ type.name }}</option>
        </select>
        <select v-model="form.city" class="ui-select">
            <option value="">المنطقة / المدينة</option>
            <option v-for="city in cities" :key="city" :value="city">{{ city }}</option>
        </select>
        <input v-model="form.price_from" placeholder="السعر من" class="ui-input" />
        <input v-model="form.price_to" placeholder="السعر إلى" class="ui-input" />
        <input v-model="form.area_from" placeholder="المساحة من" class="ui-input" />
        <input v-model="form.area_to" placeholder="المساحة إلى" class="ui-input" />
        <input v-model="form.rooms" placeholder="عدد الغرف" class="ui-input" />
        <input v-model="form.bathrooms" placeholder="عدد الحمامات" class="ui-input" />
        <select v-model="form.status" class="ui-select">
            <option value="">حالة العقار</option>
            <option value="available">متاح</option>
            <option value="reserved">محجوز</option>
            <option value="rented">مؤجر</option>
            <option value="sold">مباع</option>
        </select>
        <button class="ui-btn ui-btn-primary md:col-span-4">تطبيق التصفية</button>
    </form>

    <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-3">
        <PropertyCard v-for="property in properties.data" :key="property.id" :property="property" />
    </div>
    <p v-if="!properties.data.length" class="ui-empty">لا توجد عقارات مطابقة لخيارات البحث.</p>
    <Pagination :links="properties.links" />
</template>
