<script setup>
import { usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

const page = usePage();
const dismissed = ref(false);
const success = computed(() => page.props.flash?.success);
const error = computed(() => page.props.flash?.error);
const hasErrors = computed(() => page.props.errors && Object.keys(page.props.errors).length > 0);
</script>

<template>
    <div v-if="!dismissed" class="space-y-3 mb-6">
        <!-- Success Alert -->
        <div
            v-if="success"
            class="flex items-center justify-between gap-3 rounded-2xl border border-emerald-200/80 bg-emerald-50/90 p-4 text-sm text-emerald-900 shadow-sm backdrop-blur-md transition-all"
        >
            <div class="flex items-center gap-3">
                <span class="flex h-8 w-8 items-center justify-center rounded-full bg-emerald-500 text-white shrink-0">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                    </svg>
                </span>
                <span class="font-bold">{{ success }}</span>
            </div>
            <button @click="dismissed = true" class="rounded-lg p-1 text-emerald-600 hover:bg-emerald-100 transition-colors">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <!-- Error Alert -->
        <div
            v-if="error"
            class="flex items-center justify-between gap-3 rounded-2xl border border-rose-200/80 bg-rose-50/90 p-4 text-sm text-rose-900 shadow-sm backdrop-blur-md transition-all"
        >
            <div class="flex items-center gap-3">
                <span class="flex h-8 w-8 items-center justify-center rounded-full bg-rose-500 text-white shrink-0">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </span>
                <span class="font-bold">{{ error }}</span>
            </div>
            <button @click="dismissed = true" class="rounded-lg p-1 text-rose-600 hover:bg-rose-100 transition-colors">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <!-- Form Validation Errors -->
        <div
            v-if="hasErrors && !error"
            class="flex items-center justify-between gap-3 rounded-2xl border border-amber-200/80 bg-amber-50/90 p-4 text-sm text-amber-900 shadow-sm backdrop-blur-md transition-all"
        >
            <div class="flex items-center gap-3">
                <span class="flex h-8 w-8 items-center justify-center rounded-full bg-amber-500 text-white shrink-0">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </span>
                <span class="font-bold">يرجى مراجعة الحقول المطلوبة وتصحيح الأخطاء الظاهرة في النموذج.</span>
            </div>
            <button @click="dismissed = true" class="rounded-lg p-1 text-amber-600 hover:bg-amber-100 transition-colors">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
    </div>
</template>

