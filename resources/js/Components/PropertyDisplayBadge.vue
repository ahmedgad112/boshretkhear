<script setup>
import { computed } from 'vue';
import StatusBadge from './StatusBadge.vue';
import { getPropertyDisplayBadge } from '../composables/usePropertyBadges';

const props = defineProps({
    property: {
        type: Object,
        required: true,
    },
    purposeClass: {
        type: String,
        default: 'inline-flex items-center gap-1 rounded-full bg-amber-500/95 px-3 py-1 text-xs font-bold text-slate-950 shadow-sm backdrop-blur-md',
    },
});

const badge = computed(() => getPropertyDisplayBadge(props.property));
</script>

<template>
    <StatusBadge
        v-if="badge.kind === 'status'"
        :value="badge.value"
        :label="badge.label"
    />
    <span v-else :class="purposeClass">
        {{ badge.label }}
    </span>
</template>
