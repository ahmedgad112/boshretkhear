<script setup>
defineProps({
    label: { type: String, required: true },
    value: { type: [String, Number], required: true },
    tone: { type: String, default: 'default' },
});

const tones = {
    default: 'text-slate-900',
    success: 'text-emerald-700',
    danger: 'text-rose-600',
    forest: 'text-forest',
};
</script>

<template>
    <div class="ui-stat-card">
        <p class="ui-stat-label">{{ label }}</p>
        <p class="ui-stat-value" :class="tones[tone] || tones.default">{{ value }}</p>
    </div>
</template>
