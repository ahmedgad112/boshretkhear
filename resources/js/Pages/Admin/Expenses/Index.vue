<script setup>
import { Head, router, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import AdminLayout from '../../../Layouts/AdminLayout.vue';
import PageHeader from '../../../Components/PageHeader.vue';
import Pagination from '../../../Components/Pagination.vue';
import ConfirmModal from '../../../Components/ConfirmModal.vue';
import { useCan } from '../../../composables/useCan';

defineOptions({ layout: AdminLayout });
const { can } = useCan();
defineProps({ expenses: Object, categories: Array, properties: Array, filters: Object });
const form = useForm({
    expense_category_id: '',
    amount: '',
    expense_date: '',
    property_id: '',
    description: '',
    notes: '',
});
const deleting = ref(null);
</script>

<template>
    <Head title="المصروفات" />

    <PageHeader title="المصروفات" subtitle="تسجيل ومتابعة مصروفات النشاط العقاري" />

    <form v-if="can('expenses.create')" class="ui-panel mb-6 grid gap-3 md:grid-cols-3" @submit.prevent="form.post(route('admin.expenses.store'))">
        <select v-model="form.expense_category_id" class="ui-select" required>
            <option value="">نوع المصروف</option>
            <option v-for="item in categories" :key="item.id" :value="item.id">{{ item.name }}</option>
        </select>
        <input v-model="form.amount" placeholder="المبلغ" class="ui-input" required />
        <input v-model="form.expense_date" type="date" class="ui-input" required />
        <select v-model="form.property_id" class="ui-select">
            <option value="">العقار عند الحاجة</option>
            <option v-for="item in properties" :key="item.id" :value="item.id">{{ item.name }}</option>
        </select>
        <input v-model="form.description" placeholder="الوصف" class="ui-input" />
        <input v-model="form.notes" placeholder="الملاحظات" class="ui-input" />
        <button class="ui-btn ui-btn-primary md:col-span-3">إضافة مصروف</button>
    </form>

    <div class="ui-table-wrap">
        <table class="ui-table">
            <thead class="ui-table-head">
                <tr>
                    <th class="p-4">الرمز</th>
                    <th class="p-4">نوع المصروف</th>
                    <th class="p-4">المبلغ</th>
                    <th class="p-4">التاريخ</th>
                    <th class="p-4">العقار المرتبط</th>
                    <th class="p-4">المستخدم</th>
                    <th class="p-4 text-center">إجراءات</th>
                </tr>
            </thead>
            <tbody class="ui-table-body">
                <tr v-for="item in expenses.data" :key="item.id" class="ui-table-row">
                    <td class="p-4 font-black text-slate-900">{{ item.code }}</td>
                    <td class="p-4 font-bold text-slate-800">{{ item.category?.name }}</td>
                    <td class="p-4 font-black text-rose-600">{{ item.amount }}</td>
                    <td class="p-4 text-xs text-slate-500">{{ item.expense_date }}</td>
                    <td class="p-4 font-semibold text-forest">{{ item.property?.name || '-' }}</td>
                    <td class="p-4 text-xs text-slate-500">{{ item.creator?.name }}</td>
                    <td class="p-4 text-center">
                        <button v-if="can('expenses.delete')" class="inline-flex items-center gap-1 rounded-xl border border-rose-200/80 bg-rose-50 px-2.5 py-1.5 text-xs font-bold text-rose-700 transition-colors hover:bg-rose-100" @click="deleting = item.id">
                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                            <span>حذف</span>
                        </button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="grid gap-4 md:hidden">
        <div v-for="item in expenses.data" :key="item.id" class="ui-mobile-card">
            <div class="flex items-center justify-between border-b border-slate-100 pb-2">
                <span class="text-sm font-black text-slate-900">{{ item.code }}</span>
                <span class="rounded-full border border-rose-200 bg-rose-50 px-2.5 py-1 text-xs font-black text-rose-600">{{ item.amount }}</span>
            </div>
            <div class="space-y-1 text-xs text-slate-600">
                <p>نوع المصروف: <strong class="text-slate-900">{{ item.category?.name }}</strong></p>
                <p>العقار: <strong class="text-forest">{{ item.property?.name || '-' }}</strong></p>
                <p>التاريخ: <span>{{ item.expense_date }}</span></p>
                <p>بواسطة: <span>{{ item.creator?.name }}</span></p>
            </div>
            <div v-if="can('expenses.delete')" class="flex items-center justify-end border-t border-slate-100 pt-3">
                <button class="inline-flex items-center gap-1 rounded-xl border border-rose-200 bg-rose-50 px-3 py-1.5 text-xs font-bold text-rose-700" @click="deleting = item.id">
                    <span>حذف المصروف</span>
                </button>
            </div>
        </div>
    </div>

    <Pagination :links="expenses.links" />
    <ConfirmModal :show="!!deleting" @cancel="deleting = null" @confirm="() => { router.delete(route('admin.expenses.destroy', deleting)); deleting = null; }" />
</template>
