<script setup>
defineProps({
    title: { type: String, default: '' },
    items: { type: Array, default: () => [] },
    valueKey: { type: String, default: 'value' },
    labelKey: { type: String, default: 'label' },
    color: { type: String, default: '#0f4c3a' },
});

const maxValue = (items, valueKey) => {
    const values = items.map((item) => Number(item[valueKey] || 0));
    return Math.max(1, ...values);
};

const getPercentage = (val, items, valueKey) => {
    const max = maxValue(items, valueKey);
    return Math.round((Number(val || 0) / max) * 100);
};
</script>

<template>
    <div class="rounded-3xl bg-white p-6 shadow-sm border border-slate-100 transition-all hover:shadow-md">
        <div class="mb-5 flex items-center justify-between">
            <h3 v-if="title" class="text-base font-extrabold text-slate-800 flex items-center gap-2">
                <span class="h-2 w-2 rounded-full" :style="{ backgroundColor: color }"></span>
                {{ title }}
            </h3>
            <span v-if="items.length" class="text-xs font-semibold text-slate-400">
                {{ items.length }} عناصر
            </span>
        </div>

        <div v-if="!items.length" class="flex flex-col items-center justify-center py-8 text-center text-slate-400">
            <svg class="h-10 w-10 mb-2 opacity-50" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
            </svg>
            <span class="text-xs">لا توجد بيانات للعرض حالياً</span>
        </div>

        <div v-else class="space-y-4">
            <div v-for="item in items" :key="item[labelKey]" class="group">
                <div class="mb-1.5 flex items-center justify-between text-xs font-semibold">
                    <span class="text-slate-700 group-hover:text-slate-900 transition-colors">{{ item[labelKey] }}</span>
                    <div class="flex items-center gap-2">
                        <span class="text-[11px] text-slate-400 font-normal">({{ getPercentage(item[valueKey], items, valueKey) }}%)</span>
                        <strong class="text-slate-900 font-extrabold">{{ item[valueKey] }}</strong>
                    </div>
                </div>
                <div class="h-3 overflow-hidden rounded-full bg-slate-100 p-0.5 shadow-inner">
                    <div
                        class="h-full rounded-full transition-all duration-700 ease-out group-hover:brightness-110"
                        :style="{
                            width: `${(Number(item[valueKey] || 0) / maxValue(items, valueKey)) * 100}%`,
                            backgroundColor: color,
                        }"
                    />
                </div>
            </div>
        </div>
    </div>
</template>

