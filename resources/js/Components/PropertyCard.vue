<script setup>
import { Link } from '@inertiajs/vue3';
import PropertyDisplayBadge from './PropertyDisplayBadge.vue';
import { useShare } from '../composables/useShare';

defineProps({
    property: Object,
});

const { copied, shareProperty } = useShare();
</script>

<template>
    <article class="group relative flex flex-col overflow-hidden rounded-3xl bg-white border border-slate-100 shadow-sm transition-all duration-300 hover:-translate-y-1.5 hover:shadow-xl">
        <!-- Image Container -->
        <div class="relative h-56 w-full overflow-hidden bg-slate-900">
            <video
                v-if="property.image && property.image_type === 'video'"
                :src="property.image"
                class="h-full w-full object-cover transition-transform duration-700 ease-out group-hover:scale-105"
                muted
                playsinline
                preload="metadata"
            />
            <img
                v-else-if="property.image"
                :src="property.image"
                :alt="property.name"
                class="h-full w-full object-cover transition-transform duration-700 ease-out group-hover:scale-105"
            />
            <div v-else class="flex h-full w-full items-center justify-center bg-gradient-to-br from-forest-dark to-forest text-amber-100/40">
                <svg class="h-16 w-16" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5m0 0h4m-4 0V11m0 0h4m-4 0H7" />
                </svg>
            </div>
            
            <div class="absolute inset-0 bg-gradient-to-t from-black/50 via-transparent to-black/20 opacity-80"></div>

            <!-- Top Badges -->
            <div class="absolute right-3.5 top-3.5 flex flex-wrap gap-2">
                <PropertyDisplayBadge :property="property" />
            </div>

            <!-- Type badge on left -->
            <div v-if="property.type" class="absolute left-3.5 top-3.5">
                <span class="rounded-full bg-slate-900/70 px-3 py-1 text-xs font-medium text-white backdrop-blur-md">
                    {{ property.type }}
                </span>
            </div>
        </div>

        <!-- Content -->
        <div class="flex flex-1 flex-col justify-between space-y-4 p-5">
            <div class="space-y-1.5">
                <div class="flex items-center gap-1.5 text-xs text-slate-500">
                    <svg class="h-4 w-4 shrink-0 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    <span class="truncate font-medium">{{ property.location || 'غير محدد' }}</span>
                </div>

                <h3 class="text-lg font-extrabold text-slate-900 line-clamp-1 group-hover:text-forest transition-colors">
                    {{ property.name }}
                </h3>
            </div>

            <!-- Specs Grid -->
            <div class="grid grid-cols-3 gap-2 rounded-2xl bg-slate-50 p-2.5 text-center text-xs text-slate-600">
                <div class="flex flex-col items-center justify-center space-y-0.5">
                    <span class="text-[10px] text-slate-400">المساحة</span>
                    <div class="flex items-center gap-1 font-bold text-slate-800">
                        <svg class="h-3.5 w-3.5 text-forest" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4" />
                        </svg>
                        <span>{{ property.area || '-' }}</span>
                    </div>
                </div>
                <div class="flex flex-col items-center justify-center space-y-0.5 border-r border-slate-200">
                    <span class="text-[10px] text-slate-400">الغرف</span>
                    <div class="flex items-center gap-1 font-bold text-slate-800">
                        <svg class="h-3.5 w-3.5 text-forest" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>
                        <span>{{ property.rooms || '-' }}</span>
                    </div>
                </div>
                <div class="flex flex-col items-center justify-center space-y-0.5 border-r border-slate-200">
                    <span class="text-[10px] text-slate-400">الحمامات</span>
                    <div class="flex items-center gap-1 font-bold text-slate-800">
                        <svg class="h-3.5 w-3.5 text-forest" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        <span>{{ property.bathrooms || '-' }}</span>
                    </div>
                </div>
            </div>

            <!-- Footer CTA -->
            <div class="flex gap-2 pt-1 border-t border-slate-100">
                <button
                    type="button"
                    class="inline-flex shrink-0 items-center justify-center gap-1.5 rounded-2xl border border-slate-200 bg-white px-4 py-2.5 text-xs font-bold text-slate-700 shadow-xs transition-all hover:border-forest hover:bg-forest/5 hover:text-forest active:scale-95"
                    :title="copied ? 'تم نسخ الرابط' : 'مشاركة'"
                    @click="shareProperty(property)"
                >
                    <svg v-if="!copied" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z" />
                    </svg>
                    <svg v-else class="h-4 w-4 text-forest" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    <span>{{ copied ? 'تم النسخ' : 'مشاركة' }}</span>
                </button>
                <Link
                    :href="route('properties.show', property.id)"
                    class="inline-flex flex-1 items-center justify-center gap-1.5 rounded-2xl bg-forest px-4 py-2.5 text-xs font-bold text-white shadow-xs transition-all hover:bg-forest-light hover:shadow-md active:scale-95"
                >
                    <span>التفاصيل</span>
                    <svg class="h-4 w-4 rotate-180" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                    </svg>
                </Link>
            </div>
        </div>
    </article>
</template>

