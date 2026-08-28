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

const form = useForm({
    name: props.property?.name || '',
    code: props.property?.code || '',
    property_type_id: props.property?.property_type_id || '',
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

const showSalePrice = computed(() => form.purpose === 'sale' || form.purpose === 'both');
const showRentPrice = computed(() => form.purpose === 'rent' || form.purpose === 'both');
const canAddMore = computed(() => selectedMedia.value.length < MAX_MEDIA);
const remainingSlots = computed(() => MAX_MEDIA - selectedMedia.value.length);

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
    <Head :title="property ? 'تعديل عقار' : 'إضافة عقار'" />
    <PageHeader :title="property ? 'تعديل عقار' : 'إضافة عقار'" subtitle="بيانات العقار والصور والمميزات">
        <template #actions>
            <Link :href="route('admin.properties.index')" class="ui-btn ui-btn-secondary">رجوع</Link>
        </template>
    </PageHeader>
    <form class="ui-panel grid gap-4 md:grid-cols-2" @submit.prevent="submit">
        <input v-model="form.name" placeholder="اسم العقار" class="ui-input" required />
        <input v-model="form.code" placeholder="رمز العقار" class="ui-input" />
        <select v-model="form.property_type_id" class="ui-input" required>
            <option value="">نوع العقار</option>
            <option v-for="type in types" :key="type.id" :value="type.id">{{ type.name }}</option>
        </select>
        <select v-model="form.purpose" class="ui-input">
            <option v-for="(label, value) in $page.props.labels.propertyPurposes" :key="value" :value="value">{{ label }}</option>
        </select>

        <input
            v-if="showSalePrice"
            v-model="form.price"
            type="number"
            min="0"
            step="0.01"
            placeholder="سعر البيع"
            class="ui-input"
        />
        <input
            v-if="showRentPrice"
            v-model="form.rent_price"
            type="number"
            min="0"
            step="0.01"
            placeholder="سعر الإيجار"
            class="ui-input"
        />
        <select v-if="showRentPrice" v-model="form.rent_period" class="ui-input">
            <option v-for="(label, value) in $page.props.labels.rentPeriods" :key="value" :value="value">{{ label }}</option>
        </select>
        <select v-model="form.status" class="ui-input">
            <option v-for="(label, value) in $page.props.labels.propertyStatuses" :key="value" :value="value">{{ label }}</option>
        </select>

        <input v-model="form.district" placeholder="المنطقة" class="ui-input" />
        <input v-model="form.city" placeholder="المدينة" class="ui-input" />
        <input v-model="form.address" placeholder="العنوان" class="ui-input md:col-span-2" />

        <input v-model="form.area" type="number" min="0" step="0.01" placeholder="المساحة (م²)" class="ui-input" />
        <input v-model="form.rooms" type="number" min="0" step="1" placeholder="عدد الغرف" class="ui-input" />
        <input v-model="form.bathrooms" type="number" min="0" step="1" placeholder="عدد الحمامات" class="ui-input" />
        <input v-model="form.floors" type="number" min="0" step="1" placeholder="عدد الطوابق" class="ui-input" />
        <input v-model="form.floor_number" type="number" min="0" step="1" placeholder="رقم الطابق" class="ui-input" />

        <textarea v-model="form.description" placeholder="الوصف" class="ui-textarea md:col-span-2" rows="4" />
        <textarea v-model="form.notes" placeholder="الملاحظات" class="ui-textarea md:col-span-2" rows="3" />

        <div class="md:col-span-2">
            <p class="mb-2 font-bold">المميزات</p>
            <div class="flex flex-wrap gap-3">
                <label v-for="feature in features" :key="feature.id" class="flex items-center gap-2 text-sm">
                    <input v-model="form.feature_ids" type="checkbox" :value="feature.id" />
                    {{ feature.name }}
                </label>
            </div>
        </div>

        <label class="flex items-center gap-2 text-sm"><input v-model="form.is_featured" type="checkbox" />عقار مميز</label>
        <label class="flex items-center gap-2 text-sm"><input v-model="form.is_published" type="checkbox" />نشر على الموقع</label>

        <div class="md:col-span-2">
            <label class="mb-2 block font-bold">الصور والفيديوهات</label>
            <input
                ref="mediaInput"
                type="file"
                multiple
                accept="image/*,video/mp4,video/webm,video/quicktime,.mov"
                class="hidden"
                @change="onMediaChange"
            />
            <div class="ui-file-drop">
                <div class="flex flex-col items-center gap-3 text-center sm:flex-row sm:justify-between sm:text-right">
                    <div class="space-y-1">
                        <p class="text-sm font-semibold text-slate-800">ارفع صور وفيديوهات العقار</p>
                        <p class="text-xs text-muted">يمكن اختيار حتى {{ MAX_MEDIA }} ملفات. الصور حتى 5 ميغابايت، والفيديو حتى 50 ميغابايت (MP4, WEBM, MOV).</p>
                        <p class="text-xs font-bold text-forest">
                            {{ selectedMedia.length }} / {{ MAX_MEDIA }} {{ selectedMedia.length === 1 ? 'ملف مختار' : 'ملفات مختارة' }}
                        </p>
                        <p v-if="mediaError" class="text-xs font-bold text-red-600">{{ mediaError }}</p>
                    </div>
                    <button
                        type="button"
                        class="inline-flex shrink-0 items-center gap-2 rounded-xl px-5 py-2.5 text-sm font-bold text-white transition-colors"
                        :class="canAddMore ? 'bg-forest hover:bg-forest-light' : 'cursor-not-allowed bg-slate-400'"
                        :disabled="!canAddMore"
                        @click="openMediaPicker"
                    >
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        <span>{{ canAddMore ? 'اختيار الملفات' : 'اكتمل العدد' }}</span>
                    </button>
                </div>

                <div v-if="selectedMedia.length" class="mt-4 grid grid-cols-2 gap-3 border-t border-slate-100 pt-4 sm:grid-cols-3 md:grid-cols-5">
                    <div
                        v-for="(item, index) in selectedMedia"
                        :key="item.id"
                        class="group relative overflow-hidden rounded-xl border border-slate-200 bg-white shadow-xs"
                    >
                        <video
                            v-if="item.mediaType === 'video'"
                            :src="item.preview"
                            class="h-28 w-full object-cover bg-slate-900"
                            muted
                            playsinline
                            preload="metadata"
                        />
                        <img v-else :src="item.preview" :alt="item.file.name" class="h-28 w-full object-cover" />
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
                            class="absolute top-2 right-2 flex h-7 w-7 items-center justify-center rounded-full bg-red-600 text-sm font-bold text-white shadow-md transition-opacity hover:bg-red-700"
                            title="حذف الملف"
                            @click="removeMedia(item.id)"
                        >
                            ×
                        </button>
                        <div class="space-y-0.5 px-2 py-2">
                            <p class="truncate text-[11px] font-bold text-slate-800">{{ item.file.name }}</p>
                            <p class="text-[10px] text-muted">{{ mediaLabel(item, index) }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div v-if="property?.images?.length" class="md:col-span-2">
            <p class="mb-2 text-sm font-bold text-slate-700">الملفات الحالية على العقار</p>
            <div class="grid gap-3 md:grid-cols-4">
                <div v-for="image in property.images" :key="image.id" class="rounded-xl bg-cream p-2">
                    <video
                        v-if="image.media_type === 'video'"
                        :src="image.url"
                        class="mb-2 h-24 w-full rounded-lg object-cover bg-slate-900"
                        controls
                        playsinline
                        preload="metadata"
                    />
                    <img v-else :src="image.url" class="mb-2 h-24 w-full rounded-lg object-cover" />
                    <p class="text-xs">
                        {{ image.media_type === 'video' ? 'فيديو' : (image.is_primary ? 'صورة رئيسية' : 'صورة إضافية') }}
                    </p>
                </div>
            </div>
        </div>

        <button class="ui-btn ui-btn-primary w-full md:col-span-2" :disabled="form.processing">حفظ العقار</button>
    </form>
</template>
