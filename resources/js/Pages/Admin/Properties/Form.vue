<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import { computed, onBeforeUnmount, ref } from 'vue';
import AdminLayout from '../../../Layouts/AdminLayout.vue';
import PageHeader from '../../../Components/PageHeader.vue';

defineOptions({ layout: AdminLayout });

const props = defineProps({
    property: { type: Object, default: null },
    types: Array,
    features: Array,
});

const MAX_MEDIA = 10;
const mediaInput = ref(null);
const selectedMedia = ref([]);
const mediaError = ref('');
let mediaId = 0;

const isVideoFile = (file) => file.type.startsWith('video/');
const isImageFile = (file) => file.type.startsWith('image/');
const isMediaFile = (file) => isVideoFile(file) || isImageFile(file);

const defaultTypeId = props.property?.property_type_id
    || props.types?.find((t) => t.name === 'شقة')?.id
    || '';

const form = useForm({
    name: props.property?.name || '',
    code: props.property?.code || '',
    property_type_id: defaultTypeId,
    purpose: props.property?.purpose || 'rent',
    price: props.property?.price || '',
    rent_price: props.property?.rent_price || '',
    rent_period: props.property?.rent_period || 'nightly',
    district: props.property?.district || '',
    city: props.property?.city || '',
    address: props.property?.address || '',
    area: props.property?.area || '',
    rooms: props.property?.rooms || '',
    bathrooms: props.property?.bathrooms || '',
    floors: props.property?.floors || '',
    floor_number: props.property?.floor_number || '',
    description: props.property?.description || '',
    status: props.property?.status || 'available',
    notes: props.property?.notes || '',
    is_featured: props.property?.is_featured || false,
    is_published: props.property?.is_published ?? true,
    feature_ids: props.property?.feature_ids || [],
    images: [],
});

const isEditing = computed(() => !!props.property);
const canAddMore = computed(() => selectedMedia.value.length < MAX_MEDIA);
const remainingSlots = computed(() => MAX_MEDIA - selectedMedia.value.length);

const toggleFeature = (id) => {
    const index = form.feature_ids.indexOf(id);
    if (index === -1) {
        form.feature_ids.push(id);
    } else {
        form.feature_ids.splice(index, 1);
    }
};

const syncFormImages = () => {
    form.images = selectedMedia.value.map((item) => item.file);
};

const onMediaChange = (event) => {
    mediaError.value = '';
    const files = Array.from(event.target.files || []).filter(isMediaFile);

    if (files.length === 0) {
        mediaError.value = 'يُسمح فقط بصور أو فيديو (MP4, WEBM, MOV).';
        event.target.value = '';
        return;
    }

    const slots = remainingSlots.value;

    if (slots <= 0) {
        mediaError.value = `يمكن اختيار ${MAX_MEDIA} ملفات كحد أقصى.`;
        event.target.value = '';
        return;
    }

    if (files.length > slots) {
        mediaError.value = `تم إضافة ${slots} ${slots === 1 ? 'ملف' : 'ملفات'} فقط. الحد الأقصى ${MAX_MEDIA} ملفات.`;
    }

    files.slice(0, slots).forEach((file) => {
        selectedMedia.value.push({
            id: mediaId++,
            file,
            preview: URL.createObjectURL(file),
            mediaType: isVideoFile(file) ? 'video' : 'image',
        });
    });

    syncFormImages();
    event.target.value = '';
};

const removeMedia = (id) => {
    const index = selectedMedia.value.findIndex((item) => item.id === id);

    if (index === -1) {
        return;
    }

    URL.revokeObjectURL(selectedMedia.value[index].preview);
    selectedMedia.value.splice(index, 1);
    syncFormImages();
    mediaError.value = '';
};

const openMediaPicker = () => {
    if (!canAddMore.value) {
        mediaError.value = `يمكن اختيار ${MAX_MEDIA} ملفات كحد أقصى.`;
        return;
    }

    mediaInput.value?.click();
};

const mediaLabel = (item, index) => {
    if (item.mediaType === 'video') {
        return `فيديو ${index + 1}`;
    }

    return `صورة ${index + 1}`;
};

const submit = () => {
    if (props.property) {
        form.transform((data) => ({ ...data, _method: 'put' })).post(route('admin.properties.update', props.property.id), { forceFormData: true });
    } else {
        form.post(route('admin.properties.store'), { forceFormData: true });
    }
};

onBeforeUnmount(() => {
    selectedMedia.value.forEach((item) => URL.revokeObjectURL(item.preview));
});
</script>

<template>
    <Head :title="isEditing ? 'تعديل شقة' : 'إضافة شقة'" />

    <PageHeader
        :title="isEditing ? 'تعديل شقة' : 'إضافة شقة'"
        :subtitle="isEditing ? 'تحديث بيانات الشقة والصور والمميزات' : 'أدخل بيانات الشقة الجديدة لعرضها على الموقع'"
        :eyebrow="isEditing ? 'تعديل' : 'إضافة جديد'"
    >
        <template #actions>
            <Link :href="route('admin.properties.index')" class="ui-btn ui-btn-secondary">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                <span>رجوع للقائمة</span>
            </Link>
        </template>
    </PageHeader>

    <form class="grid gap-6 lg:grid-cols-3" @submit.prevent="submit">
        <!-- Main Column -->
        <div class="space-y-6 lg:col-span-2">
            <!-- Basic Info -->
            <section class="ui-panel space-y-5">
                <div class="flex items-center gap-3 border-b border-slate-100 pb-4">
                    <div class="flex h-10 w-10 items-center justify-center rounded-2xl bg-emerald-50 text-forest">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-sm font-black text-slate-900">البيانات الأساسية</h2>
                        <p class="text-xs text-slate-500">اسم الشقة ونوعها وحالتها</p>
                    </div>
                </div>

                <div class="grid gap-4 md:grid-cols-2">
                    <div class="md:col-span-2">
                        <label class="ui-label">اسم الشقة <span class="text-red-500">*</span></label>
                        <input v-model="form.name" placeholder="مثال: شقة فاخرة في التجمع الخامس" class="ui-input" required />
                        <p v-if="form.errors.name" class="mt-1 text-xs text-red-600">{{ form.errors.name }}</p>
                    </div>

                    <div>
                        <label class="ui-label">رمز الشقة</label>
                        <input v-model="form.code" placeholder="مثال: APT-001" class="ui-input" />
                        <p v-if="form.errors.code" class="mt-1 text-xs text-red-600">{{ form.errors.code }}</p>
                    </div>

                    <div>
                        <label class="ui-label">نوع العقار <span class="text-red-500">*</span></label>
                        <select v-model="form.property_type_id" class="ui-select" required>
                            <option value="">اختر النوع</option>
                            <option v-for="type in types" :key="type.id" :value="type.id">{{ type.name }}</option>
                        </select>
                        <p v-if="form.errors.property_type_id" class="mt-1 text-xs text-red-600">{{ form.errors.property_type_id }}</p>
                    </div>

                    <div>
                        <label class="ui-label">الغرض</label>
                        <select v-model="form.purpose" class="ui-select">
                            <option v-for="(label, value) in $page.props.labels.propertyPurposes" :key="value" :value="value">{{ label }}</option>
                        </select>
                    </div>

                    <div>
                        <label class="ui-label">حالة الشقة</label>
                        <select v-model="form.status" class="ui-select">
                            <option v-for="(label, value) in $page.props.labels.propertyStatuses" :key="value" :value="value">{{ label }}</option>
                        </select>
                    </div>
                </div>
            </section>

            <!-- Location -->
            <section class="ui-panel space-y-5">
                <div class="flex items-center gap-3 border-b border-slate-100 pb-4">
                    <div class="flex h-10 w-10 items-center justify-center rounded-2xl bg-sky-50 text-sky-700">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-sm font-black text-slate-900">الموقع</h2>
                        <p class="text-xs text-slate-500">المدينة والمنطقة والعنوان التفصيلي</p>
                    </div>
                </div>

                <div class="grid gap-4 md:grid-cols-2">
                    <div>
                        <label class="ui-label">المدينة</label>
                        <input v-model="form.city" placeholder="مثال: القاهرة" class="ui-input" />
                    </div>
                    <div>
                        <label class="ui-label">المنطقة / الحي</label>
                        <input v-model="form.district" placeholder="مثال: التجمع الخامس" class="ui-input" />
                    </div>
                    <div class="md:col-span-2">
                        <label class="ui-label">العنوان التفصيلي</label>
                        <input v-model="form.address" placeholder="الشارع، رقم المبنى، معالم قريبة..." class="ui-input" />
                    </div>
                </div>
            </section>

            <!-- Specs -->
            <section class="ui-panel space-y-5">
                <div class="flex items-center gap-3 border-b border-slate-100 pb-4">
                    <div class="flex h-10 w-10 items-center justify-center rounded-2xl bg-amber-50 text-amber-700">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-sm font-black text-slate-900">المواصفات</h2>
                        <p class="text-xs text-slate-500">المساحة وعدد الغرف والحمامات والطوابق</p>
                    </div>
                </div>

                <div class="grid gap-4 sm:grid-cols-2 md:grid-cols-3">
                    <div>
                        <label class="ui-label">المساحة (م²)</label>
                        <input v-model="form.area" type="number" min="0" step="0.01" placeholder="120" class="ui-input" />
                    </div>
                    <div>
                        <label class="ui-label">عدد الغرف</label>
                        <input v-model="form.rooms" type="number" min="0" step="1" placeholder="3" class="ui-input" />
                    </div>
                    <div>
                        <label class="ui-label">عدد الحمامات</label>
                        <input v-model="form.bathrooms" type="number" min="0" step="1" placeholder="2" class="ui-input" />
                    </div>
                    <div>
                        <label class="ui-label">عدد الطوابق</label>
                        <input v-model="form.floors" type="number" min="0" step="1" placeholder="10" class="ui-input" />
                    </div>
                    <div>
                        <label class="ui-label">رقم الطابق</label>
                        <input v-model="form.floor_number" type="number" min="0" step="1" placeholder="5" class="ui-input" />
                    </div>
                </div>
            </section>

            <!-- Description -->
            <section class="ui-panel space-y-5">
                <div class="flex items-center gap-3 border-b border-slate-100 pb-4">
                    <div class="flex h-10 w-10 items-center justify-center rounded-2xl bg-violet-50 text-violet-700">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-sm font-black text-slate-900">الوصف والملاحظات</h2>
                        <p class="text-xs text-slate-500">تفاصيل الشقة وملاحظات داخلية</p>
                    </div>
                </div>

                <div class="space-y-4">
                    <div>
                        <label class="ui-label">وصف الشقة</label>
                        <textarea
                            v-model="form.description"
                            placeholder="اكتب وصفًا تفصيليًا للشقة: التشطيب، الإطلالة، القرب من الخدمات..."
                            class="ui-textarea"
                            rows="5"
                        />
                    </div>
                    <div>
                        <label class="ui-label">ملاحظات داخلية <span class="font-normal text-slate-400">(لا تظهر للزوار)</span></label>
                        <textarea
                            v-model="form.notes"
                            placeholder="ملاحظات للفريق الداخلي..."
                            class="ui-textarea"
                            rows="3"
                        />
                    </div>
                </div>
            </section>

            <!-- Features -->
            <section v-if="features?.length" class="ui-panel space-y-5">
                <div class="flex items-center gap-3 border-b border-slate-100 pb-4">
                    <div class="flex h-10 w-10 items-center justify-center rounded-2xl bg-teal-50 text-teal-700">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-sm font-black text-slate-900">المميزات والخدمات</h2>
                        <p class="text-xs text-slate-500">اختر المميزات المتوفرة في الشقة</p>
                    </div>
                </div>

                <div class="flex flex-wrap gap-2.5">
                    <button
                        v-for="feature in features"
                        :key="feature.id"
                        type="button"
                        class="inline-flex items-center gap-2 rounded-2xl border px-4 py-2.5 text-xs font-bold transition-all"
                        :class="form.feature_ids.includes(feature.id)
                            ? 'border-forest bg-emerald-50 text-forest shadow-xs'
                            : 'border-slate-200 bg-slate-50 text-slate-600 hover:border-slate-300 hover:bg-white'"
                        @click="toggleFeature(feature.id)"
                    >
                        <svg
                            class="h-3.5 w-3.5"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                            :class="form.feature_ids.includes(feature.id) ? 'opacity-100' : 'opacity-0'"
                        >
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                        </svg>
                        <span>{{ feature.name }}</span>
                    </button>
                </div>
            </section>

            <!-- Media Upload -->
            <section class="ui-panel space-y-5">
                <div class="flex items-center gap-3 border-b border-slate-100 pb-4">
                    <div class="flex h-10 w-10 items-center justify-center rounded-2xl bg-rose-50 text-rose-700">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-sm font-black text-slate-900">الصور والفيديوهات</h2>
                        <p class="text-xs text-slate-500">ارفع حتى {{ MAX_MEDIA }} ملفات لعرض الشقة</p>
                    </div>
                </div>

                <input
                    ref="mediaInput"
                    type="file"
                    multiple
                    accept="image/*,video/mp4,video/webm,video/quicktime,.mov"
                    class="hidden"
                    @change="onMediaChange"
                />

                <div
                    class="ui-file-drop cursor-pointer transition-colors hover:border-forest/40 hover:bg-emerald-50/30"
                    :class="!canAddMore && 'cursor-not-allowed opacity-60'"
                    @click="openMediaPicker"
                >
                    <div class="flex flex-col items-center gap-4 py-4 text-center">
                        <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-white shadow-xs border border-slate-200">
                            <svg class="h-7 w-7 text-forest" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                            </svg>
                        </div>
                        <div class="space-y-1">
                            <p class="text-sm font-bold text-slate-800">اسحب الملفات هنا أو انقر للاختيار</p>
                            <p class="text-xs text-slate-500">صور (JPG, PNG, WEBP) حتى 5 ميغابايت — فيديو (MP4, WEBM, MOV) حتى 50 ميغابايت</p>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="rounded-full bg-forest/10 px-3 py-1 text-xs font-bold text-forest">
                                {{ selectedMedia.length }} / {{ MAX_MEDIA }} ملف
                            </span>
                            <button
                                type="button"
                                class="ui-btn ui-btn-primary"
                                :disabled="!canAddMore"
                                @click.stop="openMediaPicker"
                            >
                                {{ canAddMore ? 'اختيار الملفات' : 'اكتمل العدد' }}
                            </button>
                        </div>
                        <p v-if="mediaError" class="text-xs font-bold text-red-600">{{ mediaError }}</p>
                    </div>
                </div>

                <div v-if="selectedMedia.length" class="grid grid-cols-2 gap-3 sm:grid-cols-3 md:grid-cols-4">
                    <div
                        v-for="(item, index) in selectedMedia"
                        :key="item.id"
                        class="group relative overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-xs"
                    >
                        <video
                            v-if="item.mediaType === 'video'"
                            :src="item.preview"
                            class="h-32 w-full object-cover bg-slate-900"
                            muted
                            playsinline
                            preload="metadata"
                        />
                        <img v-else :src="item.preview" :alt="item.file.name" class="h-32 w-full object-cover" />
                        <span
                            v-if="item.mediaType === 'video'"
                            class="pointer-events-none absolute inset-0 flex items-center justify-center bg-black/25 text-white"
                        >
                            <svg class="h-8 w-8" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M8 5v14l11-7z" />
                            </svg>
                        </span>
                        <button
                            type="button"
                            class="absolute top-2 right-2 flex h-7 w-7 items-center justify-center rounded-full bg-red-600 text-sm font-bold text-white shadow-md opacity-0 transition-opacity group-hover:opacity-100 hover:bg-red-700"
                            title="حذف الملف"
                            @click="removeMedia(item.id)"
                        >
                            ×
                        </button>
                        <div class="space-y-0.5 px-2.5 py-2">
                            <p class="truncate text-[11px] font-bold text-slate-800">{{ item.file.name }}</p>
                            <p class="text-[10px] text-muted">{{ mediaLabel(item, index) }}</p>
                        </div>
                    </div>
                </div>

                <!-- Existing media on edit -->
                <div v-if="property?.images?.length" class="space-y-3 border-t border-slate-100 pt-5">
                    <p class="text-xs font-bold text-slate-600">الملفات الحالية على الشقة</p>
                    <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 md:grid-cols-4">
                        <div v-for="image in property.images" :key="image.id" class="overflow-hidden rounded-2xl border border-slate-200 bg-cream">
                            <video
                                v-if="image.media_type === 'video'"
                                :src="image.url"
                                class="h-28 w-full object-cover bg-slate-900"
                                controls
                                playsinline
                                preload="metadata"
                            />
                            <img v-else :src="image.url" class="h-28 w-full object-cover" />
                            <p class="px-2 py-1.5 text-[10px] font-bold text-slate-600">
                                {{ image.media_type === 'video' ? 'فيديو' : (image.is_primary ? 'صورة رئيسية' : 'صورة إضافية') }}
                            </p>
                        </div>
                    </div>
                </div>
            </section>
        </div>

        <!-- Sidebar -->
        <aside class="space-y-6">
            <section class="ui-panel sticky top-24 space-y-5">
                <div class="border-b border-slate-100 pb-4">
                    <h2 class="text-sm font-black text-slate-900">النشر والعرض</h2>
                    <p class="mt-0.5 text-xs text-slate-500">تحكم في ظهور الشقة على الموقع</p>
                </div>

                <div class="space-y-3">
                    <label
                        class="flex cursor-pointer items-center justify-between rounded-2xl border p-4 transition-all"
                        :class="form.is_published ? 'border-forest bg-emerald-50/60' : 'border-slate-200 bg-slate-50'"
                    >
                        <div>
                            <p class="text-sm font-bold text-slate-900">نشر على الموقع</p>
                            <p class="text-[11px] text-slate-500">إظهار الشقة للزوار</p>
                        </div>
                        <input v-model="form.is_published" type="checkbox" class="ui-check h-5 w-5" />
                    </label>

                    <label
                        class="flex cursor-pointer items-center justify-between rounded-2xl border p-4 transition-all"
                        :class="form.is_featured ? 'border-amber-400 bg-amber-50/60' : 'border-slate-200 bg-slate-50'"
                    >
                        <div>
                            <p class="text-sm font-bold text-slate-900">شقة مميزة</p>
                            <p class="text-[11px] text-slate-500">تظهر في قسم الشقق المميزة</p>
                        </div>
                        <input v-model="form.is_featured" type="checkbox" class="ui-check h-5 w-5" />
                    </label>
                </div>

                <div class="space-y-2 border-t border-slate-100 pt-4">
                    <button type="submit" class="ui-btn ui-btn-primary w-full py-3" :disabled="form.processing">
                        <svg v-if="form.processing" class="h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" />
                        </svg>
                        <span>{{ form.processing ? 'جاري الحفظ...' : (isEditing ? 'حفظ التعديلات' : 'إضافة الشقة') }}</span>
                    </button>
                    <Link :href="route('admin.properties.index')" class="ui-btn ui-btn-secondary w-full">
                        إلغاء
                    </Link>
                </div>
            </section>

            <section class="ui-panel space-y-3">
                <h3 class="text-xs font-black text-slate-700">نصائح سريعة</h3>
                <ul class="space-y-2.5 text-xs leading-6 text-slate-500">
                    <li class="flex gap-2">
                        <span class="mt-1.5 h-1.5 w-1.5 shrink-0 rounded-full bg-forest"></span>
                        <span>اختر نوع <strong class="text-slate-700">شقة</strong> حتى تظهر على الموقع.</span>
                    </li>
                    <li class="flex gap-2">
                        <span class="mt-1.5 h-1.5 w-1.5 shrink-0 rounded-full bg-forest"></span>
                        <span>أضف 3 صور على الأقل لعرض أفضل.</span>
                    </li>
                    <li class="flex gap-2">
                        <span class="mt-1.5 h-1.5 w-1.5 shrink-0 rounded-full bg-forest"></span>
                        <span>الوصف التفصيلي يساعد الزوار على اتخاذ القرار.</span>
                    </li>
                </ul>
            </section>
        </aside>
    </form>
</template>
