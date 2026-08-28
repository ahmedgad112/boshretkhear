<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AdminLayout from '../../../Layouts/AdminLayout.vue';
import Pagination from '../../../Components/Pagination.vue';

defineOptions({ layout: AdminLayout });
const props = defineProps({ rentals: Object, filters: Object });
const form = useForm({ q: props.filters.q || '', status: props.filters.status || '' });
</script>

<template>
    <Head title="الإيجارات" />
    
    <div class="mb-6">
        <h1 class="text-2xl font-black text-slate-900">إدارة الإيجارات</h1>
        <p class="text-xs text-slate-500 mt-1">سجل وعقود الإيجار النشطة في المنصة</p>
    </div>

    <!-- Search Form -->
    <form class="ui-panel mb-6 flex gap-3 max-w-md" @submit.prevent="form.get(route('admin.rentals.index'))">
        <input v-model="form.q" placeholder="رمز الإيجار أو العقار..." class="ui-input" />
        <button class="ui-btn ui-btn-dark shrink-0">بحث</button>
    </form>

    <!-- Desktop Table View -->
    <div class="hidden md:block overflow-hidden rounded-3xl bg-white shadow-xs border border-slate-200/80">
        <table class="min-w-full text-right text-sm">
            <thead class="bg-slate-50 text-xs font-black text-slate-500 border-b border-slate-200/80">
                <tr>
                    <th class="p-4">الرمز</th>
                    <th class="p-4">العقار</th>
                    <th class="p-4">العميل</th>
                    <th class="p-4">الحالة</th>
                    <th class="p-4 text-center">التفاصيل</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                <tr v-for="item in rentals.data" :key="item.id" class="hover:bg-slate-50/60 transition-colors">
                    <td class="p-4 font-black text-slate-900">{{ item.code }}</td>
                    <td class="p-4 font-bold text-forest">{{ item.property?.name }}</td>
                    <td class="p-4 font-medium text-slate-700">{{ item.customer?.name }}</td>
                    <td class="p-4">
                        <span class="inline-flex rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-bold text-emerald-700 border border-emerald-200">
                            {{ $page.props.labels.bookingStatuses[item.status] || item.status }}
                        </span>
                    </td>
                    <td class="p-4 text-center">
                        <div class="flex items-center justify-center gap-2">
                            <Link :href="route('admin.bookings.show', item.id)" class="inline-flex items-center gap-1 rounded-xl bg-emerald-50 px-3 py-1.5 text-xs font-bold text-emerald-700 border border-emerald-200 hover:bg-emerald-100 transition-colors">
                                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                <span>عرض</span>
                            </Link>
                            <a :href="route('admin.bookings.contract', item.id)" target="_blank" class="inline-flex items-center gap-1 rounded-xl bg-slate-100 px-3 py-1.5 text-xs font-bold text-slate-700 border border-slate-200 hover:bg-slate-200 transition-colors">
                                طباعة العقد
                            </a>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Mobile Cards View -->
    <div class="grid gap-4 md:hidden">
        <div v-for="item in rentals.data" :key="item.id" class="rounded-3xl bg-white p-5 border border-slate-200/80 shadow-xs space-y-3">
            <div class="flex items-center justify-between border-b border-slate-100 pb-2">
                <span class="font-black text-slate-900 text-sm">{{ item.code }}</span>
                <span class="text-xs font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-full border border-emerald-200">
                    {{ $page.props.labels.bookingStatuses[item.status] || item.status }}
                </span>
            </div>
            <div class="space-y-1 text-xs text-slate-600">
                <p>العقار: <strong class="text-slate-900">{{ item.property?.name }}</strong></p>
                <p>المستأجر: <strong class="text-slate-900">{{ item.customer?.name }}</strong></p>
            </div>
            <div class="flex items-center justify-end border-t border-slate-100 pt-3">
                <Link :href="route('admin.bookings.show', item.id)" class="inline-flex items-center gap-1 rounded-xl bg-emerald-50 px-3 py-1.5 text-xs font-bold text-emerald-700 border border-emerald-200">
                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    <span>عرض التفاصيل</span>
                </Link>
            </div>
        </div>
    </div>

    <Pagination :links="rentals.links" />
</template>

