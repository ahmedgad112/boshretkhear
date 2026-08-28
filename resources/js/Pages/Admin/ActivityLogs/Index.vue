<script setup>
import { Head } from '@inertiajs/vue3';
import AdminLayout from '../../../Layouts/AdminLayout.vue';
import PageHeader from '../../../Components/PageHeader.vue';
import Pagination from '../../../Components/Pagination.vue';

defineOptions({ layout: AdminLayout });
defineProps({ logs: Object, filters: Object });
</script>

<template>
    <Head title="سجل العمليات" />

    <PageHeader title="سجل العمليات" subtitle="تتبع كل الإجراءات التي تمت على النظام" />

    <div class="ui-table-wrap">
        <table class="ui-table">
            <thead class="ui-table-head">
                <tr>
                    <th class="p-4">المستخدم</th>
                    <th class="p-4">تفاصيل العملية</th>
                    <th class="p-4">التاريخ</th>
                    <th class="p-4">الوقت</th>
                </tr>
            </thead>
            <tbody class="ui-table-body">
                <tr v-for="item in logs.data" :key="item.id" class="ui-table-row">
                    <td class="p-4 font-extrabold text-slate-900">{{ item.user?.name || 'النظام' }}</td>
                    <td class="p-4 text-xs leading-relaxed text-slate-700">{{ item.description }}</td>
                    <td class="p-4 text-xs font-medium text-slate-500">{{ item.created_at?.slice(0, 10) }}</td>
                    <td class="p-4 font-mono text-xs text-slate-400">{{ item.created_at?.slice(11, 19) }}</td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="grid gap-4 md:hidden">
        <div v-for="item in logs.data" :key="item.id" class="ui-mobile-card">
            <div class="flex items-center justify-between border-b border-slate-100 pb-2">
                <span class="text-sm font-black text-slate-900">{{ item.user?.name || 'النظام' }}</span>
                <span class="font-mono text-xs text-slate-400">{{ item.created_at?.slice(0, 10) }}</span>
            </div>
            <p class="text-xs leading-relaxed text-slate-700">{{ item.description }}</p>
        </div>
    </div>

    <Pagination :links="logs.links" />
</template>
