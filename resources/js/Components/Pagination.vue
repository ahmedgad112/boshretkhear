<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
    links: { type: Array, default: () => [] },
});
</script>

<template>
    <div v-if="links.length > 3" class="mt-6 flex flex-wrap justify-center gap-2">
        <Link
            v-for="(link, index) in links"
            :key="index"
            :href="link.url || ''"
            class="rounded-2xl px-4 py-2 text-sm font-bold transition-all"
            :class="link.active ? 'bg-forest text-white shadow-xs' : 'border border-slate-200 bg-white text-slate-700 hover:bg-slate-50'"
            :preserve-scroll="true"
            v-html="link.label.replace('Previous', 'السابق').replace('Next', 'التالي')"
        />
    </div>
</template>
