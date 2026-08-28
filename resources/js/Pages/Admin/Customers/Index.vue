<script setup>
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import AdminLayout from '../../../Layouts/AdminLayout.vue';
import Pagination from '../../../Components/Pagination.vue';
import ConfirmModal from '../../../Components/ConfirmModal.vue';
import { useCan } from '../../../composables/useCan';

defineOptions({ layout: AdminLayout });
const { can } = useCan();
const props = defineProps({ customers: Object, filters: Object });
const form = useForm({ q: props.filters.q || '' });
const deleting = ref(null);
</script>

<template>
    <Head title="العملاء" />
    
    <div class="mb-6 flex items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-slate-900">سجل العملاء</h1>
            <p class="text-xs text-slate-500 mt-1">إدارة وتقييم وإضافة بيانات العملاء</p>
        </div>
        <Link v-if="can('customers.create')" :href="route('admin.customers.create')" class="inline-flex items-center gap-2 rounded-2xl bg-forest px-5 py-2.5 text-xs font-extrabold text-white shadow-xs hover:bg-forest-light transition-all active:scale-95">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
            </svg>
            <span>إضافة عميل جديد</span>
        </Link>
    </div>

    <!-- Search Form -->
    <form class="mb-6 flex gap-3 max-w-md" @submit.prevent="form.get(route('admin.customers.index'))">
        <input v-model="form.q" placeholder="بحث بالاسم أو رقم الهاتف..." class="ui-input" />
        <button class="ui-btn ui-btn-dark shrink-0">بحث</button>
    </form>

    <!-- Desktop Table View -->
    <div class="hidden md:block overflow-hidden rounded-3xl bg-white shadow-xs border border-slate-200/80">
        <table class="min-w-full text-right text-sm">
            <thead class="bg-slate-50 text-xs font-black text-slate-500 border-b border-slate-200/80">
                <tr>
                    <th class="p-4">الاسم</th>
                    <th class="p-4">الهاتف</th>
                    <th class="p-4">الرقم القومي</th>
                    <th class="p-4 text-center">إجراءات</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                <tr v-for="item in customers.data" :key="item.id" class="hover:bg-slate-50/60 transition-colors">
                    <td class="p-4 font-extrabold text-slate-900">{{ item.name }}</td>
                    <td class="p-4 font-medium text-slate-700 dir-ltr text-right">{{ item.phone || '-' }}</td>
                    <td class="p-4 text-slate-500">{{ item.national_id || '-' }}</td>
                    <td class="p-4">
                        <div class="flex items-center justify-center gap-2">
                            <Link :href="route('admin.customers.show', item.id)" class="inline-flex items-center gap-1 rounded-xl bg-emerald-50 px-2.5 py-1.5 text-xs font-bold text-emerald-700 border border-emerald-200/80 hover:bg-emerald-100 transition-colors">
                                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                <span>عرض</span>
                            </Link>
                            <Link v-if="can('customers.update')" :href="route('admin.customers.edit', item.id)" class="inline-flex items-center gap-1 rounded-xl bg-amber-50 px-2.5 py-1.5 text-xs font-bold text-amber-700 border border-amber-200/80 hover:bg-amber-100 transition-colors">
                                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                                <span>تعديل</span>
                            </Link>
                            <button v-if="can('customers.delete')" class="inline-flex items-center gap-1 rounded-xl bg-rose-50 px-2.5 py-1.5 text-xs font-bold text-rose-700 border border-rose-200/80 hover:bg-rose-100 transition-colors" @click="deleting = item.id">
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
        <div v-for="item in customers.data" :key="item.id" class="rounded-3xl bg-white p-5 border border-slate-200/80 shadow-xs space-y-3">
            <div class="flex items-center justify-between border-b border-slate-100 pb-2">
                <h3 class="font-black text-slate-900 text-base">{{ item.name }}</h3>
                <span class="text-xs font-bold text-slate-400">#{{ item.id }}</span>
            </div>
            <div class="space-y-1 text-xs text-slate-600">
                <p>الهاتف: <strong class="text-slate-900">{{ item.phone || '-' }}</strong></p>
                <p>الرقم القومي: <strong class="text-slate-900">{{ item.national_id || '-' }}</strong></p>
            </div>
            <div class="flex items-center justify-end gap-2 border-t border-slate-100 pt-3">
                <Link :href="route('admin.customers.show', item.id)" class="inline-flex items-center gap-1 rounded-xl bg-emerald-50 px-3 py-1.5 text-xs font-bold text-emerald-700 border border-emerald-200">
                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    <span>عرض</span>
                </Link>
                <Link v-if="can('customers.update')" :href="route('admin.customers.edit', item.id)" class="inline-flex items-center gap-1 rounded-xl bg-amber-50 px-3 py-1.5 text-xs font-bold text-amber-700 border border-amber-200">
                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    <span>تعديل</span>
                </Link>
                <button v-if="can('customers.delete')" class="inline-flex items-center gap-1 rounded-xl bg-rose-50 px-3 py-1.5 text-xs font-bold text-rose-700 border border-rose-200" @click="deleting = item.id">
                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                    <span>حذف</span>
                </button>
            </div>
        </div>
    </div>

    <Pagination :links="customers.links" />
    <ConfirmModal :show="!!deleting" @cancel="deleting = null" @confirm="() => { router.delete(route('admin.customers.destroy', deleting)); deleting = null; }" />
</template>

