<script setup>
import { Head } from '@inertiajs/vue3';
import { computed, reactive, ref } from 'vue';
import PublicLayout from '../../Layouts/PublicLayout.vue';
import PropertyCard from '../../Components/PropertyCard.vue';
import PropertyDisplayBadge from '../../Components/PropertyDisplayBadge.vue';

defineOptions({ layout: PublicLayout });

const props = defineProps({
    property: Object,
    similar: Array,
});

const WHATSAPP_NUMBER = '201210072971';
const PHONE_NUMBER = '+201515789505';

const current = ref(0);
const currentMedia = computed(() => props.property.images?.[current.value]);
const form = reactive({
    name: '',
    phone: '',
    type: 'contact',
    message: '',
});

const typeLabels = {
    viewing: 'طلب معاينة',
    contact: 'استفسار عام',
};

const submit = () => {
    const lines = [
        'استفسار عن عقار',
        '',
        `العقار: ${props.property.name}`,
        props.property.code ? `الكود: ${props.property.code}` : null,
        `الاسم: ${form.name}`,
        `الهاتف: ${form.phone}`,
        `نوع الطلب: ${typeLabels[form.type] || form.type}`,
        '',
        'الرسالة:',
        form.message,
    ].filter(Boolean);

    const url = `https://wa.me/${WHATSAPP_NUMBER}?text=${encodeURIComponent(lines.join('\n'))}`;
    window.open(url, '_blank', 'noopener,noreferrer');
};
</script>

<template>
    <Head :title="property.name" />

    <div class="grid gap-8 lg:grid-cols-3">
        <!-- Main Column -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Image Gallery Container -->
            <div class="overflow-hidden rounded-3xl bg-slate-100 shadow-sm border border-slate-200">
                <div class="relative h-80 w-full md:h-[480px]">
                    <video
                        v-if="currentMedia?.media_type === 'video'"
                        :key="currentMedia.id"
                        :src="currentMedia.url"
                        class="h-full w-full object-cover bg-slate-900"
                        controls
                        playsinline
                        preload="metadata"
                    />
                    <img v-else-if="currentMedia" :src="currentMedia.url" class="h-full w-full object-cover transition-all duration-300" />
                    <div v-else class="flex h-full w-full items-center justify-center text-slate-400">
                        <svg class="h-20 w-20" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>

                    <!-- Top Badges -->
                    <div class="absolute top-4 right-4 flex items-center gap-2">
                        <PropertyDisplayBadge
                            :property="property"
                            purpose-class="rounded-full bg-amber-500 px-3.5 py-1 text-xs font-black text-slate-950 shadow-sm"
                        />
                    </div>
                </div>

                <!-- Thumbnails Strip -->
                <div v-if="property.images?.length > 1" class="flex gap-3 overflow-x-auto p-4 bg-slate-200/70 border-t border-slate-200">
                    <button
                        v-for="(image, index) in property.images"
                        :key="image.id"
                        @click="current = index"
                        class="relative shrink-0 overflow-hidden rounded-xl transition-all"
                        :class="current === index ? 'ring-2 ring-forest scale-95 opacity-100' : 'opacity-70 hover:opacity-100'"
                    >
                        <video
                            v-if="image.media_type === 'video'"
                            :src="image.url"
                            class="h-16 w-24 object-cover bg-slate-900 pointer-events-none"
                            muted
                            playsinline
                            preload="metadata"
                        />
                        <img v-else :src="image.url" class="h-16 w-24 object-cover" />
                        <span
                            v-if="image.media_type === 'video'"
                            class="pointer-events-none absolute inset-0 flex items-center justify-center bg-black/30 text-white"
                        >
                            <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M8 5v14l11-7z" />
                            </svg>
                        </span>
                    </button>
                </div>
            </div>


            <!-- Property Main Info Box -->
            <div class="rounded-3xl bg-white p-6 md:p-8 shadow-sm border border-slate-100 space-y-6">
                <div class="flex flex-wrap items-start justify-between gap-4 border-b border-slate-100 pb-6">
                    <div class="space-y-2">
                        <div class="flex items-center gap-2 text-xs font-semibold text-amber-600">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            </svg>
                            <span>{{ property.location }}</span>
                        </div>
                        <h1 class="text-2xl font-black text-slate-900 md:text-3xl">{{ property.name }}</h1>
                        <p v-if="property.address" class="text-xs text-slate-400">{{ property.address }}</p>
                    </div>
                </div>

                <!-- Specs Grid -->
                <div class="grid grid-cols-2 gap-4 sm:grid-cols-4">
                    <div class="rounded-2xl bg-slate-50 p-4 text-center border border-slate-100">
                        <span class="block text-xs text-slate-400 mb-1">المساحة الإجمالية</span>
                        <strong class="text-base font-extrabold text-slate-900">{{ property.area || '-' }}</strong>
                    </div>
                    <div class="rounded-2xl bg-slate-50 p-4 text-center border border-slate-100">
                        <span class="block text-xs text-slate-400 mb-1">عدد الغرف</span>
                        <strong class="text-base font-extrabold text-slate-900">{{ property.rooms || '-' }}</strong>
                    </div>
                    <div class="rounded-2xl bg-slate-50 p-4 text-center border border-slate-100">
                        <span class="block text-xs text-slate-400 mb-1">عدد الحمامات</span>
                        <strong class="text-base font-extrabold text-slate-900">{{ property.bathrooms || '-' }}</strong>
                    </div>
                    <div class="rounded-2xl bg-slate-50 p-4 text-center border border-slate-100">
                        <span class="block text-xs text-slate-400 mb-1">نوع العقار</span>
                        <strong class="text-base font-extrabold text-slate-900">{{ property.type?.name || '-' }}</strong>
                    </div>
                </div>

                <!-- Description -->
                <div v-if="property.description" class="space-y-3">
                    <h2 class="text-lg font-extrabold text-slate-900">وصف العقار التفصيلي</h2>
                    <p class="leading-8 text-slate-600 text-sm whitespace-pre-line bg-slate-50/60 p-5 rounded-2xl border border-slate-100">
                        {{ property.description }}
                    </p>
                </div>

                <!-- Features -->
                <div v-if="property.features && property.features.length" class="space-y-3">
                    <h2 class="text-lg font-extrabold text-slate-900">المميزات والخدمات</h2>
                    <div class="flex flex-wrap gap-2.5">
                        <span
                            v-for="feature in property.features"
                            :key="feature.id"
                            class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 border border-emerald-200/80 px-4 py-2 text-xs font-bold text-emerald-800"
                        >
                            <svg class="h-4 w-4 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                            </svg>
                            <span>{{ feature.name }}</span>
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar Contact Widget -->
        <aside class="space-y-6">
            <div class="sticky top-24 rounded-3xl bg-white p-6 shadow-lg border border-slate-100 space-y-6">
                <div class="border-b border-slate-100 pb-4">
                    <h3 class="text-lg font-black text-slate-900">مهتم بهذه الشقة؟</h3>
                    <p class="text-xs text-slate-500 mt-1">أرسل استفسارك عبر واتساب وسنتواصل معك فوراً</p>
                </div>

                <!-- Contact Direct Buttons -->
                <div class="grid grid-cols-2 gap-3">
                    <a
                        :href="`tel:${PHONE_NUMBER}`"
                        class="flex items-center justify-center gap-2 rounded-2xl bg-emerald-50 border border-emerald-200 py-3 text-xs font-bold text-emerald-800 hover:bg-emerald-100 transition-colors"
                    >
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                        </svg>
                        <span>اتصال هاتفي</span>
                    </a>
                    <a
                        :href="`https://wa.me/${WHATSAPP_NUMBER}`"
                        target="_blank"
                        class="flex items-center justify-center gap-2 rounded-2xl bg-emerald-600 py-3 text-xs font-bold text-white shadow-sm hover:bg-emerald-700 transition-colors"
                    >
                        <span>واتساب</span>
                    </a>
                </div>

                <!-- Form Inquiry -->
                <form class="space-y-3.5" @submit.prevent="submit">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">الاسم الكريم</label>
                        <input v-model="form.name" placeholder="أدخل اسمك بالكامل" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm focus:bg-white" required />
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">رقم الهاتف / الواتساب</label>
                        <input v-model="form.phone" placeholder="05xxxxxxxx" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm focus:bg-white" required />
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">نوع الطلب</label>
                        <select v-model="form.type" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm focus:bg-white">
                            <option value="contact">استفسار عام</option>
                            <option value="viewing">طلب معاينة</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">الرسالة أو الملاحظات</label>
                        <textarea v-model="form.message" rows="3" placeholder="اكتب استفسارك هنا..." class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm focus:bg-white" required />
                    </div>

                    <button type="submit" class="w-full rounded-2xl bg-forest py-3.5 font-extrabold text-white shadow-md hover:bg-forest-light transition-all active:scale-95">
                        إرسال عبر واتساب
                    </button>
                </form>
            </div>
        </aside>
    </div>

    <!-- Similar Properties Section -->
    <section v-if="similar && similar.length" class="mt-16 border-t border-slate-200 pt-12">
        <div class="mb-8">
            <span class="text-xs font-bold text-amber-600 tracking-wider">اقتراحات مشابهة</span>
            <h2 class="text-2xl font-black text-slate-900 md:text-3xl">شقق مشابهة</h2>
        </div>
        <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            <PropertyCard v-for="item in similar" :key="item.id" :property="item" />
        </div>
    </section>
</template>

