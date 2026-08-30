<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import PublicLayout from '../../Layouts/PublicLayout.vue';
import PropertyCard from '../../Components/PropertyCard.vue';
import Pagination from '../../Components/Pagination.vue';

defineOptions({ layout: PublicLayout });

const props = defineProps({
    properties: Object,
    filters: Object,
    cities: Array,
});

const form = useForm({
    q: props.filters.q || '',
    city: props.filters.city || '',
    district: props.filters.district || '',
    area_from: props.filters.area_from || '',
    area_to: props.filters.area_to || '',
    rooms: props.filters.rooms || '',
    bathrooms: props.filters.bathrooms || '',
    status: props.filters.status || '',
});

const search = () => form.get(route('properties.index'), { preserveState: true });
</script>

<template>
    <Head title="الشقق" />

    <div class="mb-8">
        <span class="text-xs font-bold tracking-wider text-amber-600">استكشف الشقق</span>
        <h1 class="text-3xl font-black text-slate-900 md:text-4xl">جميع الشقق</h1>
        <p class="mt-1 text-sm text-slate-500">تصفح الشقق المتاحة واطّلع على تفاصيل كل وحدة.</p>
    </div>

    <form class="ui-panel mb-8 grid gap-3 md:grid-cols-4" @submit.prevent="search">
        <input v-model="form.q" placeholder="كلمة البحث" class="ui-input" />
        <select v-model="form.city" class="ui-select">
            <option value="">المنطقة / المدينة</option>
            <option v-for="city in cities" :key="city" :value="city">{{ city }}</option>
        </select>
        <input v-model="form.area_from" placeholder="المساحة من" class="ui-input" />
        <input v-model="form.area_to" placeholder="المساحة إلى" class="ui-input" />
        <input v-model="form.rooms" placeholder="عدد الغرف" class="ui-input" />
        <input v-model="form.bathrooms" placeholder="عدد الحمامات" class="ui-input" />
        <select v-model="form.status" class="ui-select">
            <option value="">حالة الشقة</option>
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
    <p v-if="!properties.data.length" class="ui-empty">لا توجد شقق مطابقة لخيارات البحث.</p>
    <Pagination :links="properties.links" />
</template>
