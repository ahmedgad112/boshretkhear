<script setup>
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <div class="flex min-h-screen items-center justify-center bg-hero px-4 py-10">
        <div class="w-full max-w-md rounded-[2rem] border border-slate-200/80 bg-white p-8 shadow-2xl">
            <Link href="/" class="mb-8 flex flex-col items-center gap-2 text-center">
                <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-forest text-amber-400 shadow-sm">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5m0 0h4m-4 0V11m0 0h4m-4 0H7" />
                    </svg>
                </div>
                <span class="text-2xl font-black text-forest">بشرى خير</span>
                <span class="text-xs font-semibold text-amber-600">لوحة الإدارة العقارية</span>
            </Link>
            <slot />
        </div>
    </div>
</template>
