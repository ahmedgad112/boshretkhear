<script setup>
const props = defineProps({
    value: String,
    label: String,
});

const configs = {
    available: { bg: 'bg-emerald-50 text-emerald-700 border-emerald-200/80', dot: 'bg-emerald-500' },
    reserved: { bg: 'bg-amber-50 text-amber-700 border-amber-200/80', dot: 'bg-amber-500' },
    rented: { bg: 'bg-sky-50 text-sky-700 border-sky-200/80', dot: 'bg-sky-500' },
    sold: { bg: 'bg-purple-50 text-purple-700 border-purple-200/80', dot: 'bg-purple-500' },
    unavailable: { bg: 'bg-rose-50 text-rose-700 border-rose-200/80', dot: 'bg-rose-500' },
    pending: { bg: 'bg-amber-50 text-amber-700 border-amber-200/80', dot: 'bg-amber-500' },
    confirmed: { bg: 'bg-teal-50 text-teal-700 border-teal-200/80', dot: 'bg-teal-500' },
    active: { bg: 'bg-emerald-50 text-emerald-700 border-emerald-200/80', dot: 'bg-emerald-500' },
    completed: { bg: 'bg-indigo-50 text-indigo-700 border-indigo-200/80', dot: 'bg-indigo-500' },
    cancelled: { bg: 'bg-rose-50 text-rose-700 border-rose-200/80', dot: 'bg-rose-500' },
};

const getConfig = (val) => configs[val] || { bg: 'bg-slate-100 text-slate-700 border-slate-200', dot: 'bg-slate-400' };
</script>

<template>
    <span
        class="inline-flex items-center gap-1.5 rounded-full border px-2.5 py-1 text-xs font-semibold shadow-2xs backdrop-blur-xs"
        :class="getConfig(value).bg"
    >
        <span class="h-1.5 w-1.5 rounded-full" :class="getConfig(value).dot"></span>
        <span>{{ label || value }}</span>
    </span>
</template>

