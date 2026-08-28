<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { computed } from 'vue';
import AdminLayout from '../../Layouts/AdminLayout.vue';
import SimpleBars from '../../Components/SimpleBars.vue';

defineOptions({ layout: AdminLayout });

const props = defineProps({
    stats: { type: Object, default: () => ({}) },
    charts: { type: Object, default: () => ({ monthly: [], topProperties: [] }) },
});

const cards = computed(() => [
    { label: 'إجمالي العقارات', val: props.stats.properties_total ?? 0, icon: 'building', color: 'bg-emerald-50 text-emerald-700 border-emerald-200' },
    { label: 'العقارات المتاحة', val: props.stats.properties_available ?? 0, icon: 'check-circle', color: 'bg-teal-50 text-teal-700 border-teal-200' },
    { label: 'العقارات المؤجرة', val: props.stats.properties_rented ?? 0, icon: 'key', color: 'bg-sky-50 text-sky-700 border-sky-200' },
    { label: 'العقارات المباعة', val: props.stats.properties_sold ?? 0, icon: 'tag', color: 'bg-purple-50 text-purple-700 border-purple-200' },
    { label: 'العقارات المحجوزة', val: props.stats.properties_reserved ?? 0, icon: 'bookmark', color: 'bg-amber-50 text-amber-700 border-amber-200' },
    { label: 'إجمالي العملاء', val: props.stats.customers ?? 0, icon: 'users', color: 'bg-indigo-50 text-indigo-700 border-indigo-200' },
    { label: 'إجمالي الحجوزات', val: props.stats.bookings ?? 0, icon: 'calendar', color: 'bg-blue-50 text-blue-700 border-blue-200' },
    { label: 'إجمالي الإيرادات', val: props.stats.revenues ?? 0, icon: 'cash', color: 'bg-emerald-100 text-forest border-emerald-300 font-black' },
    { label: 'إجمالي المصروفات', val: props.stats.expenses ?? 0, icon: 'credit-card', color: 'bg-amber-50 text-amber-800 border-amber-200' },
    { label: 'صافي الربح', val: props.stats.net ?? 0, icon: 'trending-up', color: 'bg-emerald-50 text-emerald-900 border-emerald-300 font-black' },
    { label: 'المبالغ المستحقة', val: props.stats.due ?? 0, icon: 'exclamation-circle', color: 'bg-rose-50 text-rose-700 border-rose-200' },
]);

const monthly = computed(() => props.charts?.monthly || []);
const revenues = computed(() => monthly.value.map((item) => ({ label: item.label, value: item.revenues })));
const expenses = computed(() => monthly.value.map((item) => ({ label: item.label, value: item.expenses })));
const rentals = computed(() => monthly.value.map((item) => ({ label: item.label, value: item.rentals })));
const sales = computed(() => monthly.value.map((item) => ({ label: item.label, value: item.sales })));
const topProperties = computed(() =>
    (props.charts?.topProperties || []).map((item) => ({ label: item.name, value: item.income })),
);
</script>

<template>
    <Head title="لوحة التحكم الإدارية" />

    <!-- Top Action Header -->
    <div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <span class="text-xs font-bold text-amber-600 tracking-wider">مرحباً بك مجدداً</span>
            <h1 class="text-2xl font-black text-slate-900 md:text-3xl">نظرة عامة على الأداء</h1>
        </div>

        <div class="flex flex-wrap items-center gap-3">
            <Link
                :href="route('admin.properties.create')"
                class="inline-flex items-center gap-2 rounded-2xl bg-forest px-4 py-2.5 text-xs font-extrabold text-white shadow-sm hover:bg-forest-light transition-all active:scale-95"
            >
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                </svg>
                <span>إضافة عقار جديد</span>
            </Link>

            <Link
                :href="route('admin.bookings.create')"
                class="inline-flex items-center gap-2 rounded-2xl bg-slate-900 px-4 py-2.5 text-xs font-extrabold text-white shadow-sm hover:bg-slate-800 transition-all active:scale-95"
            >
                <svg class="h-4 w-4 text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                </svg>
                <span>تسجيل حجز</span>
            </Link>
        </div>
    </div>

    <!-- KPI Cards Grid -->
    <div class="mb-10 grid gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
        <div
            v-for="card in cards"
            :key="card.label"
            class="flex items-center justify-between rounded-3xl bg-white p-5 shadow-xs border border-slate-100 transition-all hover:shadow-md hover:-translate-y-1"
        >
            <div class="space-y-1">
                <p class="text-xs font-bold text-slate-500">{{ card.label }}</p>
                <p class="text-2xl font-black text-slate-900 tracking-tight">{{ card.val }}</p>
            </div>

            <div class="flex h-12 w-12 items-center justify-center rounded-2xl border" :class="card.color">
                <svg v-if="card.icon === 'building'" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5m0 0h4m-4 0V11m0 0h4m-4 0H7" />
                </svg>
                <svg v-else-if="card.icon === 'check-circle'" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <svg v-else-if="card.icon === 'key'" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                </svg>
                <svg v-else-if="card.icon === 'tag'" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                </svg>
                <svg v-else-if="card.icon === 'cash'" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
                <svg v-else-if="card.icon === 'trending-up'" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                </svg>
                <svg v-else class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
        </div>
    </div>

    <!-- Analytics Visual Bars Grid -->
    <div class="grid gap-6 xl:grid-cols-2">
        <SimpleBars title="الإيرادات الشهرية" :items="revenues" color="#0f4c3a" />
        <SimpleBars title="المصروفات الشهرية" :items="expenses" color="#d97706" />
        <SimpleBars title="عدد عمليات الإيجار" :items="rentals" color="#18634d" />
        <SimpleBars title="عدد عمليات البيع" :items="sales" color="#7c2d12" />
        <div class="xl:col-span-2">
            <SimpleBars title="أكثر العقارات تحقيقًا للإيرادات" :items="topProperties" color="#0f4c3a" />
        </div>
    </div>
</template>

