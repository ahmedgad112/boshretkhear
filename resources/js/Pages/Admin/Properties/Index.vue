<script setup>
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import AdminLayout from '../../../Layouts/AdminLayout.vue';
import Pagination from '../../../Components/Pagination.vue';
import StatusBadge from '../../../Components/StatusBadge.vue';
import ConfirmModal from '../../../Components/ConfirmModal.vue';
import { useCan } from '../../../composables/useCan';

defineOptions({ layout: AdminLayout });
const { can } = useCan();

const props = defineProps({
    properties: Object,
    filters: Object,
});

const form = useForm({ q: props.filters.q || '', status: props.filters.status || '', purpose: props.filters.purpose || '' });
const deleting = ref(null);

const search = () => form.get(route('admin.properties.index'), { preserveState: true });
const remove = () => {
    router.delete(route('admin.properties.destroy', deleting.value));
    deleting.value = null;
};
</script>

<template>
    <Head title="العقارات" />
    
    <div class="mb-6 flex flex-wrap items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-slate-900">إدارة العقارات</h1>
            <p class="text-xs text-slate-500 mt-1">استعراض وتصفية وتعديل عقارات المنصة</p>
        </div>
        <Link v-if="can('properties.create')" :href="route('admin.properties.create')" class="inline-flex items-center gap-2 rounded-2xl bg-forest px-5 py-2.5 text-xs font-extrabold text-white shadow-xs hover:bg-forest-light transition-all active:scale-95">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
            </svg>
            <span>إضافة عقار جديد</span>
        </Link>
    </div>

    <!-- Filter Form -->
    <form class="ui-panel mb-6 grid gap-3 md:grid-cols-4" @submit.prevent="search">
        <input v-model="form.q" placeholder="بحث باسم العقار أو الكود..." class="ui-input" />
        <select v-model="form.status" class="ui-select">
            <option value="">كل الحالات</option>
            <option v-for="(label, value) in $page.props.labels.propertyStatuses" :key="value" :value="value">{{ label }}</option>
        </select>
        <select v-model="form.purpose" class="ui-select">
            <option value="">كل الأغراض</option>
            <option v-for="(label, value) in $page.props.labels.propertyPurposes" :key="value" :value="value">{{ label }}</option>
        </select>
        <button class="ui-btn ui-btn-dark">تصفية النتائج</button>
    </form>

    <!-- Desktop Table View -->
    <div class="hidden md:block overflow-hidden rounded-3xl bg-white shadow-xs border border-slate-200/80">
        <table class="min-w-full text-right text-sm">
            <thead class="bg-slate-50 text-xs font-black text-slate-500 border-b border-slate-200/80">
                <tr>
                    <th class="p-4">الصورة</th>
                    <th class="p-4">العقار</th>
                    <th class="p-4">النوع</th>
                    <th class="p-4">الغرض</th>
                    <th class="p-4">الحالة</th>
                    <th class="p-4 text-center">إجراءات</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                <tr v-for="item in properties.data" :key="item.id" class="hover:bg-slate-50/60 transition-colors">
                    <td class="p-4">
                        <video v-if="item.image && item.image_type === 'video'" :src="item.image" class="h-12 w-16 rounded-xl object-cover shadow-2xs border border-slate-200 bg-slate-900" muted playsinline preload="metadata" />
                        <img v-else-if="item.image" :src="item.image" class="h-12 w-16 rounded-xl object-cover shadow-2xs border border-slate-200" />
                        <div v-else class="h-12 w-16 rounded-xl bg-slate-100 flex items-center justify-center text-slate-400">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5m0 0h4m-4 0V11m0 0h4m-4 0H7" />
                            </svg>
                        </div>
                    </td>
                    <td class="p-4">
                        <p class="font-extrabold text-slate-900">{{ item.name }}</p>
                        <p class="text-xs text-slate-400 mt-0.5">{{ item.code }} - {{ item.city }}</p>
                    </td>
                    <td class="p-4 font-semibold text-slate-700">{{ item.type }}</td>
                    <td class="p-4 font-semibold text-slate-700">{{ item.purpose_label }}</td>
                    <td class="p-4"><StatusBadge :value="item.status" :label="item.status_label" /></td>
                    <td class="p-4">
                        <div class="flex items-center justify-center gap-2">
                            <Link :href="route('admin.properties.show', item.id)" class="inline-flex items-center gap-1 rounded-xl bg-emerald-50 px-2.5 py-1.5 text-xs font-bold text-emerald-700 border border-emerald-200/80 hover:bg-emerald-100 transition-colors" title="عرض">
                                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                <span>عرض</span>
                            </Link>
                            <Link v-if="can('properties.update')" :href="route('admin.properties.edit', item.id)" class="inline-flex items-center gap-1 rounded-xl bg-amber-50 px-2.5 py-1.5 text-xs font-bold text-amber-700 border border-amber-200/80 hover:bg-amber-100 transition-colors" title="تعديل">
                                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                                <span>تعديل</span>
                            </Link>
                            <button v-if="can('properties.delete')" class="inline-flex items-center gap-1 rounded-xl bg-rose-50 px-2.5 py-1.5 text-xs font-bold text-rose-700 border border-rose-200/80 hover:bg-rose-100 transition-colors" @click="deleting = item.id" title="حذف">
                                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                                <span>حذف</span>
                            </button>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Mobile Cards View -->
    <div class="grid gap-4 md:hidden">
        <div v-for="item in properties.data" :key="item.id" class="rounded-3xl bg-white p-5 border border-slate-200/80 shadow-xs space-y-4">
            <div class="flex items-center gap-3">
                <video v-if="item.image && item.image_type === 'video'" :src="item.image" class="h-16 w-20 rounded-2xl object-cover border border-slate-200 shrink-0 bg-slate-900" muted playsinline preload="metadata" />
                <img v-else-if="item.image" :src="item.image" class="h-16 w-20 rounded-2xl object-cover border border-slate-200 shrink-0" />
                <div class="space-y-1 min-w-0 flex-1">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold text-slate-400">{{ item.code }}</span>
                        <StatusBadge :value="item.status" :label="item.status_label" />
                    </div>
                    <h3 class="font-black text-slate-900 truncate">{{ item.name }}</h3>
                    <p class="text-xs text-slate-500">{{ item.type }} - {{ item.purpose_label }}</p>
                </div>
            </div>

            <div class="flex items-center justify-end gap-2 border-t border-slate-100 pt-3">
                <Link :href="route('admin.properties.show', item.id)" class="inline-flex items-center gap-1 rounded-xl bg-emerald-50 px-3 py-1.5 text-xs font-bold text-emerald-700 border border-emerald-200">
                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                    <span>عرض</span>
                </Link>
                <Link v-if="can('properties.update')" :href="route('admin.properties.edit', item.id)" class="inline-flex items-center gap-1 rounded-xl bg-amber-50 px-3 py-1.5 text-xs font-bold text-amber-700 border border-amber-200">
                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    <span>تعديل</span>
                </Link>
                <button v-if="can('properties.delete')" class="inline-flex items-center gap-1 rounded-xl bg-rose-50 px-3 py-1.5 text-xs font-bold text-rose-700 border border-rose-200" @click="deleting = item.id">
                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                    <span>حذف</span>
                </button>
            </div>
        </div>
    </div>

    <Pagination :links="properties.links" />
    <ConfirmModal :show="!!deleting" message="هل تريد حذف هذا العقار؟" @cancel="deleting = null" @confirm="remove" />
</template>

