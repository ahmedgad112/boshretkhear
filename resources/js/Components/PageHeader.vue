<script setup>
defineProps({
    title: { type: String, required: true },
    subtitle: { type: String, default: '' },
    eyebrow: { type: String, default: '' },
});
</script>

<template>
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <span v-if="eyebrow" class="text-xs font-bold tracking-wider text-amber-600">{{ eyebrow }}</span>
            <h1 class="text-2xl font-black text-slate-900 md:text-3xl">{{ title }}</h1>
            <p v-if="subtitle" class="mt-1 text-xs text-slate-500">{{ subtitle }}</p>
        </div>
        <div v-if="$slots.actions" class="flex flex-wrap items-center gap-3">
            <slot name="actions" />
        </div>
        <div v-if="$slots.default && !$slots.actions">
            <slot />
        </div>
    </div>
</template>
