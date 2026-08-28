<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import PublicLayout from '../../Layouts/PublicLayout.vue';
import PropertyCard from '../../Components/PropertyCard.vue';

defineOptions({ layout: PublicLayout });

const props = defineProps({
    featured: Array,
    forSale: Array,
    forRent: Array,
    types: Array,
    cities: Array,
});

const activeTab = ref('');

const form = useForm({
    q: '',
    purpose: '',
    city: '',
    property_type_id: '',
});

const setPurpose = (purpose) => {
    activeTab.value = purpose;
    form.purpose = purpose;
};

const search = () => form.get(route('properties.index'));
</script>

<template>
    <Head title="الرئيسية - بشرى خير للعقارات" />

    <!-- Light Hero Section -->
    <section class="relative mb-16 overflow-hidden rounded-[2.5rem] border border-slate-200/80 bg-gradient-to-br from-emerald-50/80 via-white to-amber-50/60 px-6 py-16 text-slate-900 shadow-lg md:px-12 md:py-20">
        <!-- Subtle Backdrop Accent Glows -->
        <div class="absolute -right-20 -top-20 h-80 w-80 rounded-full bg-amber-400/10 blur-3xl pointer-events-none"></div>
        <div class="absolute -left-20 -bottom-20 h-80 w-80 rounded-full bg-emerald-400/10 blur-3xl pointer-events-none"></div>

        <div class="relative z-10 mx-auto max-w-3xl text-center">
            <span class="inline-flex items-center gap-2 rounded-full bg-forest/10 px-4 py-1.5 text-xs font-extrabold text-forest border border-forest/20 backdrop-blur-md mb-4">
                <span class="h-2 w-2 rounded-full bg-forest animate-pulse"></span>
                وجهتك الموثوقة للاستثمار والعقارات
            </span>
            <h1 class="mb-5 text-4xl font-black leading-tight tracking-tight text-slate-900 md:text-6xl">
                اعثر على عقارك المناسب <br />
                <span class="text-forest">بكل سهولة واحترافية</span>
            </h1>
            <p class="mb-10 text-base text-slate-600 md:text-lg leading-relaxed max-w-2xl mx-auto font-medium">
                نجمع لك أفضل العقارات المتاحة للبيع والإيجار في مكان واحد. تصفح الوحدات، اطلب المعاينة، وتواصل مباشرة.
            </p>
        </div>

        <!-- Search Container -->
        <div class="relative z-10 mx-auto max-w-4xl">
            <!-- Tabs -->
            <div class="flex items-center justify-center gap-2 mb-3">
                <button
                    type="button"
                    class="rounded-t-2xl px-6 py-2.5 text-sm font-bold transition-all"
                    :class="activeTab === '' ? 'bg-forest text-white shadow-md' : 'bg-white/80 text-slate-700 hover:bg-white border border-slate-200/80'"
                    @click="setPurpose('')"
                >
                    الكل
                </button>
                <button
                    type="button"
                    class="rounded-t-2xl px-6 py-2.5 text-sm font-bold transition-all"
                    :class="activeTab === 'sale' ? 'bg-forest text-white shadow-md' : 'bg-white/80 text-slate-700 hover:bg-white border border-slate-200/80'"
                    @click="setPurpose('sale')"
                >
                    عقارات للبيع
                </button>
                <button
                    type="button"
                    class="rounded-t-2xl px-6 py-2.5 text-sm font-bold transition-all"
                    :class="activeTab === 'rent' ? 'bg-forest text-white shadow-md' : 'bg-white/80 text-slate-700 hover:bg-white border border-slate-200/80'"
                    @click="setPurpose('rent')"
                >
                    عقارات للإيجار
                </button>
            </div>

            <!-- Form Bar -->
            <form class="grid gap-3 rounded-3xl bg-white p-4 text-slate-900 shadow-xl border border-slate-200/80 md:grid-cols-12" @submit.prevent="search">
                <div class="relative md:col-span-4">
                    <input
                        v-model="form.q"
                        placeholder="ابحث باسم العقار، الحي، أو اسم المنطقة..."
                        class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3.5 pr-10 text-sm font-medium focus:bg-white"
                    />
                    <svg class="absolute right-3.5 top-4 h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>

                <div class="relative md:col-span-3">
                    <select v-model="form.city" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3.5 text-sm font-medium focus:bg-white">
                        <option value="">كل المدن</option>
                        <option v-for="city in cities" :key="city" :value="city">{{ city }}</option>
                    </select>
                </div>

                <div class="relative md:col-span-3">
                    <select v-model="form.property_type_id" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3.5 text-sm font-medium focus:bg-white">
                        <option value="">نوع العقار</option>
                        <option v-for="t in types" :key="t.id" :value="t.id">{{ t.name }}</option>
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

        <!-- Stat Pill Bar -->
        <div class="relative z-10 mt-12 grid grid-cols-2 gap-4 max-w-4xl mx-auto md:grid-cols-4 text-center">
            <div class="rounded-2xl bg-white p-3.5 shadow-xs border border-slate-200/80">
                <span class="block text-2xl font-black text-forest">+500</span>
                <span class="text-xs text-slate-500 font-semibold">عقار متاح</span>
            </div>
            <div class="rounded-2xl bg-white p-3.5 shadow-xs border border-slate-200/80">
                <span class="block text-2xl font-black text-forest">100%</span>
                <span class="text-xs text-slate-500 font-semibold">عقود موثقة</span>
            </div>
            <div class="rounded-2xl bg-white p-3.5 shadow-xs border border-slate-200/80">
                <span class="block text-2xl font-black text-forest">+1200</span>
                <span class="text-xs text-slate-500 font-semibold">عميل سعيد</span>
            </div>
            <div class="rounded-2xl bg-white p-3.5 shadow-xs border border-slate-200/80">
                <span class="block text-2xl font-black text-forest">24/7</span>
                <span class="text-xs text-slate-500 font-semibold">دعم متواصل</span>
            </div>
        </div>
    </section>


    <!-- Featured Section -->
    <section v-if="featured && featured.length" class="mb-16">
        <div class="mb-8 flex items-end justify-between border-b border-slate-200 pb-4">
            <div>
                <span class="text-xs font-bold text-amber-600 tracking-wider">اخترنا لك</span>
                <h2 class="text-2xl font-black text-slate-900 md:text-3xl">العقارات المميزة</h2>
            </div>
            <Link :href="route('properties.index')" class="inline-flex items-center gap-1 text-sm font-bold text-forest hover:underline">
                <span>عرض جميع العقارات</span>
                <svg class="h-4 w-4 rotate-180" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                </svg>
            </Link>
        </div>
        <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            <PropertyCard v-for="property in featured" :key="property.id" :property="property" />
        </div>
    </section>

    <!-- Properties For Sale Section -->
    <section v-if="forSale && forSale.length" class="mb-16">
        <div class="mb-8 flex items-end justify-between border-b border-slate-200 pb-4">
            <div>
                <span class="text-xs font-bold text-forest tracking-wider">فرص استثمارية وتمليك</span>
                <h2 class="text-2xl font-black text-slate-900 md:text-3xl">عقارات للبيع</h2>
            </div>
            <Link :href="route('properties.sale')" class="inline-flex items-center gap-1 text-sm font-bold text-forest hover:underline">
                <span>عرض عقارات البيع</span>
                <svg class="h-4 w-4 rotate-180" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                </svg>
            </Link>
        </div>
        <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            <PropertyCard v-for="property in forSale" :key="property.id" :property="property" />
        </div>
    </section>

    <!-- Properties For Rent Section -->
    <section v-if="forRent && forRent.length" class="mb-16">
        <div class="mb-8 flex items-end justify-between border-b border-slate-200 pb-4">
            <div>
                <span class="text-xs font-bold text-sky-600 tracking-wider">خيارات إيجار متنوعة</span>
                <h2 class="text-2xl font-black text-slate-900 md:text-3xl">عقارات للإيجار</h2>
            </div>
            <Link :href="route('properties.rent')" class="inline-flex items-center gap-1 text-sm font-bold text-forest hover:underline">
                <span>عرض عقارات الإيجار</span>
                <svg class="h-4 w-4 rotate-180" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                </svg>
            </Link>
        </div>
        <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            <PropertyCard v-for="property in forRent" :key="property.id" :property="property" />
        </div>
    </section>

    <!-- Features / Advantages Section -->
    <section class="mb-16 grid gap-6 md:grid-cols-3">
        <div class="rounded-3xl bg-white p-8 shadow-sm border border-slate-100 transition-all hover:shadow-lg">
            <div class="mb-4 flex h-12 w-12 items-center justify-center rounded-2xl bg-emerald-100 text-forest">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                </svg>
            </div>
            <h3 class="mb-2 text-lg font-extrabold text-slate-900">عرض احترافي</h3>
            <p class="text-sm leading-7 text-slate-500">بطاقات واضحة وصور عالية الجودة وتفاصيل شاملة تجعل عملية اتخاذ القرار واضحة وسريعة.</p>
        </div>

        <div class="rounded-3xl bg-white p-8 shadow-sm border border-slate-100 transition-all hover:shadow-lg">
            <div class="mb-4 flex h-12 w-12 items-center justify-center rounded-2xl bg-amber-100 text-amber-700">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                </svg>
            </div>
            <h3 class="mb-2 text-lg font-extrabold text-slate-900">إدارة ودقة</h3>
            <p class="text-sm leading-7 text-slate-500">نظام متكامل لتتبع الحجوزات والمبيعات والمدفوعات لضمان حقوق كافة الأطراف بوضوح.</p>
        </div>

        <div class="rounded-3xl bg-white p-8 shadow-sm border border-slate-100 transition-all hover:shadow-lg">
            <div class="mb-4 flex h-12 w-12 items-center justify-center rounded-2xl bg-sky-100 text-sky-700">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <h3 class="mb-2 text-lg font-extrabold text-slate-900">حسابات وشفافية</h3>
            <p class="text-sm leading-7 text-slate-500">متابعة دقيقة للأقساط والمتبقي مع إتاحة تقارير وإشعار العميل باستمرار.</p>
        </div>
    </section>

    <!-- Bottom Banner CTA (Light Mode) -->
    <section class="relative overflow-hidden rounded-[2.5rem] bg-gradient-to-r from-emerald-50 via-teal-50/60 to-amber-50/70 border border-emerald-200/80 p-8 text-slate-900 shadow-sm md:p-12">
        <div class="relative z-10 max-w-2xl">
            <span class="inline-block text-xs font-extrabold text-forest mb-2 tracking-wider">سجل وحدتك الآن</span>
            <h2 class="mb-3 text-2xl font-black text-slate-900 md:text-3xl">هل ترغب في إضافة أو تسويق عقارك؟</h2>
            <p class="mb-8 text-slate-600 text-sm md:text-base leading-relaxed font-medium">
                انضم إلينا اليوم واعثر على مشتري أو مستأجر لعقارك بأسرع وقت وأعلى درجات الاحترافية.
            </p>
            <Link
                :href="route('contact')"
                class="inline-flex items-center gap-2 rounded-2xl bg-forest px-8 py-3.5 font-extrabold text-white shadow-md hover:bg-forest-light transition-all active:scale-95"
            >
                <span>تواصل معنا الآن</span>
                <svg class="h-5 w-5 rotate-180" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                </svg>
            </Link>
        </div>
    </section>
</template>


