<script setup>
import { Link, useForm } from '@inertiajs/vue3';
import PublicLayout from '../../Layouts/PublicLayout.vue';
import PropertyCard from '../../Components/PropertyCard.vue';
import SeoHead from '../../Components/SeoHead.vue';
import {
    DEFAULT_KEYWORDS,
    organizationJsonLd,
    uniqueKeywords,
    useSeo,
    websiteJsonLd,
} from '../../composables/useSeo';

defineOptions({ layout: PublicLayout });

const props = defineProps({
    featured: Array,
    apartments: Array,
    cities: Array,
});

const form = useForm({
    q: '',
    city: '',
});

const search = () => form.get(route('properties.index'));
const { appUrl } = useSeo();
</script>

<template>
    <SeoHead
        title="شقق للبيع والإيجار"
        description="بشرة خير — منصة عربية لتصفّح الشقق للبيع والإيجار. اكتشف وحدات سكنية بصور وتفاصيل كاملة وتواصل معنا للاستفسار والمعاينة."
        :keywords="uniqueKeywords(DEFAULT_KEYWORDS, ['شقق للبيع', 'شقق للإيجار', 'عقارات سكنية', 'شقة مفروشة'])"
        :json-ld="[organizationJsonLd(appUrl), websiteJsonLd(appUrl)]"
    />

    <!-- Hero Section -->
    <section class="relative mb-16 overflow-hidden rounded-[2.5rem] border border-slate-200/80 bg-gradient-to-br from-emerald-50/80 via-white to-amber-50/60 px-6 py-16 text-slate-900 shadow-lg md:px-12 md:py-20">
        <div class="absolute -right-20 -top-20 h-80 w-80 rounded-full bg-amber-400/10 blur-3xl pointer-events-none"></div>
        <div class="absolute -left-20 -bottom-20 h-80 w-80 rounded-full bg-emerald-400/10 blur-3xl pointer-events-none"></div>

        <div class="relative z-10 mx-auto max-w-3xl text-center">
            <span class="inline-flex items-center gap-2 rounded-full bg-forest/10 px-4 py-1.5 text-xs font-extrabold text-forest border border-forest/20 backdrop-blur-md mb-4">
                <span class="h-2 w-2 rounded-full bg-forest animate-pulse"></span>
                اكتشف شقتك المناسبة
            </span>
            <h1 class="mb-5 text-4xl font-black leading-tight tracking-tight text-slate-900 md:text-6xl">
                تصفّح الشقق <br />
                <span class="text-forest">بكل سهولة</span>
            </h1>
            <p class="mb-10 text-base text-slate-600 md:text-lg leading-relaxed max-w-2xl mx-auto font-medium">
                نعرض لك الشقق المتاحة بتفاصيلها وصورها. تصفّح الوحدات واطّلع على المواصفات، ثم تواصل معنا للاستفسار.
            </p>
        </div>

        <!-- Search -->
        <div class="relative z-10 mx-auto max-w-3xl">
            <form class="grid gap-3 rounded-3xl bg-white p-4 text-slate-900 shadow-xl border border-slate-200/80 md:grid-cols-12" @submit.prevent="search">
                <div class="relative md:col-span-5">
                    <input
                        v-model="form.q"
                        placeholder="ابحث باسم الشقة، الحي، أو المنطقة..."
                        class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3.5 pr-10 text-sm font-medium focus:bg-white"
                    />
                    <svg class="absolute right-3.5 top-4 h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>

                <div class="relative md:col-span-5">
                    <select v-model="form.city" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3.5 text-sm font-medium focus:bg-white">
                        <option value="">كل المدن</option>
                        <option v-for="city in cities" :key="city" :value="city">{{ city }}</option>
                    </select>
                </div>

                <div class="md:col-span-2">
                    <button class="flex h-full w-full items-center justify-center gap-2 rounded-2xl bg-forest py-3.5 font-extrabold text-white shadow-md hover:bg-forest-light transition-all active:scale-95">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        <span>بحث</span>
                    </button>
                </div>
            </form>
        </div>
    </section>

    <!-- Featured Section -->
    <section v-if="featured && featured.length" class="mb-16">
        <div class="mb-8 flex items-end justify-between border-b border-slate-200 pb-4">
            <div>
                <span class="text-xs font-bold text-amber-600 tracking-wider">اخترنا لك</span>
                <h2 class="text-2xl font-black text-slate-900 md:text-3xl">الشقق المميزة</h2>
            </div>
            <Link :href="route('properties.index')" class="inline-flex items-center gap-1 text-sm font-bold text-forest hover:underline">
                <span>عرض جميع الشقق</span>
                <svg class="h-4 w-4 rotate-180" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                </svg>
            </Link>
        </div>
        <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            <PropertyCard v-for="property in featured" :key="property.id" :property="property" />
        </div>
    </section>

    <!-- All Apartments Section -->
    <section v-if="apartments && apartments.length" class="mb-16">
        <div class="mb-8 flex items-end justify-between border-b border-slate-200 pb-4">
            <div>
                <span class="text-xs font-bold text-forest tracking-wider">الوحدات المتاحة</span>
                <h2 class="text-2xl font-black text-slate-900 md:text-3xl">أحدث الشقق</h2>
            </div>
            <Link :href="route('properties.index')" class="inline-flex items-center gap-1 text-sm font-bold text-forest hover:underline">
                <span>عرض الكل</span>
                <svg class="h-4 w-4 rotate-180" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                </svg>
            </Link>
        </div>
        <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            <PropertyCard v-for="property in apartments" :key="property.id" :property="property" />
        </div>
    </section>

    <!-- Features Section -->
    <section class="mb-16 grid gap-6 md:grid-cols-3">
        <div class="rounded-3xl bg-white p-8 shadow-sm border border-slate-100 transition-all hover:shadow-lg">
            <div class="mb-4 flex h-12 w-12 items-center justify-center rounded-2xl bg-emerald-100 text-forest">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                </svg>
            </div>
            <h3 class="mb-2 text-lg font-extrabold text-slate-900">عرض واضح</h3>
            <p class="text-sm leading-7 text-slate-500">صور عالية الجودة وتفاصيل شاملة لكل شقة: المساحة، الغرف، الحمامات، والمميزات.</p>
        </div>

        <div class="rounded-3xl bg-white p-8 shadow-sm border border-slate-100 transition-all hover:shadow-lg">
            <div class="mb-4 flex h-12 w-12 items-center justify-center rounded-2xl bg-amber-100 text-amber-700">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                </svg>
            </div>
            <h3 class="mb-2 text-lg font-extrabold text-slate-900">تواصل مباشر</h3>
            <p class="text-sm leading-7 text-slate-500">تواصل معنا عبر الهاتف أو واتساب للاستفسار عن أي شقة تناسبك.</p>
        </div>

        <div class="rounded-3xl bg-white p-8 shadow-sm border border-slate-100 transition-all hover:shadow-lg">
            <div class="mb-4 flex h-12 w-12 items-center justify-center rounded-2xl bg-sky-100 text-sky-700">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z" />
                </svg>
            </div>
            <h3 class="mb-2 text-lg font-extrabold text-slate-900">سهولة التصفح</h3>
            <p class="text-sm leading-7 text-slate-500">واجهة عربية متجاوبة تناسب الجوال والحاسوب لتصفّح مريح.</p>
        </div>
    </section>

    <!-- CTA -->
    <section class="relative overflow-hidden rounded-[2.5rem] bg-gradient-to-r from-emerald-50 via-teal-50/60 to-amber-50/70 border border-emerald-200/80 p-8 text-slate-900 shadow-sm md:p-12">
        <div class="relative z-10 max-w-2xl">
            <span class="inline-block text-xs font-extrabold text-forest mb-2 tracking-wider">هل لديك استفسار؟</span>
            <h2 class="mb-3 text-2xl font-black text-slate-900 md:text-3xl">تواصل معنا الآن</h2>
            <p class="mb-8 text-slate-600 text-sm md:text-base leading-relaxed font-medium">
                فريقنا جاهز للرد على استفساراتك ومساعدتك في اختيار الشقة المناسبة.
            </p>
            <Link
                :href="route('contact')"
                class="inline-flex items-center gap-2 rounded-2xl bg-forest px-8 py-3.5 font-extrabold text-white shadow-md hover:bg-forest-light transition-all active:scale-95"
            >
                <span>تواصل معنا</span>
                <svg class="h-5 w-5 rotate-180" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                </svg>
            </Link>
        </div>
    </section>
</template>
