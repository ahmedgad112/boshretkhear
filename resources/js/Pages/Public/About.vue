<script setup>
import { Head } from '@inertiajs/vue3';
import PublicLayout from '../../Layouts/PublicLayout.vue';

defineOptions({ layout: PublicLayout });
</script>

<template>
    <Head title="من نحن" />

    <section class="ui-panel md:p-12">
        <span class="text-xs font-bold tracking-wider text-amber-600">تعرف علينا</span>
        <h1 class="mb-4 text-3xl font-black text-slate-900 md:text-4xl">من نحن</h1>
        <p class="mb-8 max-w-3xl text-base leading-8 text-slate-600">
            بشرة خير منصة عربية لعرض الشقق بتفاصيلها وصورها. نساعدك على تصفّح الوحدات المتاحة والتواصل معنا للاستفسار.
        </p>
        <div class="grid gap-6 md:grid-cols-3">
            <div class="rounded-3xl border border-slate-100 bg-cream/60 p-6 transition-all hover:shadow-md">
                <div class="mb-4 flex h-12 w-12 items-center justify-center rounded-2xl bg-emerald-100 text-forest">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                </div>
                <h3 class="mb-2 text-lg font-extrabold text-slate-900">وضوح</h3>
                <p class="text-sm leading-7 text-slate-500">كل عقار يظهر بتفاصيله وصوره وحالته دون تعقيد.</p>
            </div>
            <div class="rounded-3xl border border-slate-100 bg-cream/60 p-6 transition-all hover:shadow-md">
                <div class="mb-4 flex h-12 w-12 items-center justify-center rounded-2xl bg-amber-100 text-amber-700">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                </div>
                <h3 class="mb-2 text-lg font-extrabold text-slate-900">ثقة</h3>
                <p class="text-sm leading-7 text-slate-500">معلومات دقيقة وواضحة عن كل شقة لمساعدتك في اتخاذ القرار.</p>
            </div>
            <div class="rounded-3xl border border-slate-100 bg-cream/60 p-6 transition-all hover:shadow-md">
                <div class="mb-4 flex h-12 w-12 items-center justify-center rounded-2xl bg-sky-100 text-sky-700">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z" />
                    </svg>
                </div>
                <h3 class="mb-2 text-lg font-extrabold text-slate-900">سهولة</h3>
                <p class="text-sm leading-7 text-slate-500">واجهة عربية متجاوبة تناسب الجوال والحاسوب.</p>
            </div>
        </div>
    </section>
</template>
