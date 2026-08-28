<script setup>
import { Head, router, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import AdminLayout from '../../../Layouts/AdminLayout.vue';
import PageHeader from '../../../Components/PageHeader.vue';
import Pagination from '../../../Components/Pagination.vue';
import ConfirmModal from '../../../Components/ConfirmModal.vue';

defineOptions({ layout: AdminLayout });
defineProps({ users: Object, roles: Array, filters: Object });
const form = useForm({
    name: '',
    email: '',
    phone: '',
    password: '',
    password_confirmation: '',
    role: '',
    is_active: true,
});
const editing = ref(null);
const deleting = ref(null);

const edit = (user) => {
    editing.value = user.id;
    form.name = user.name;
    form.email = user.email;
    form.phone = user.phone;
    form.role = user.roles?.[0]?.name || '';
    form.is_active = user.is_active;
    form.password = '';
    form.password_confirmation = '';
};

const save = () => {
    if (editing.value) {
        form.put(route('admin.users.update', editing.value), { onSuccess: () => { editing.value = null; form.reset(); } });
    } else {
        form.post(route('admin.users.store'), { onSuccess: () => form.reset() });
    }
};
</script>

<template>
    <Head title="المستخدمون" />

    <PageHeader title="المستخدمون" subtitle="إدارة حسابات الدخول والأدوار الوظيفية" />

    <form class="ui-panel mb-6 grid gap-3 md:grid-cols-3" @submit.prevent="save">
        <input v-model="form.name" placeholder="الاسم" class="ui-input" required />
        <input v-model="form.email" placeholder="البريد الإلكتروني" class="ui-input" required />
        <input v-model="form.phone" placeholder="رقم الهاتف" class="ui-input" />
        <input v-model="form.password" type="password" placeholder="كلمة المرور" class="ui-input" />
        <input v-model="form.password_confirmation" type="password" placeholder="تأكيد كلمة المرور" class="ui-input" />
        <select v-model="form.role" class="ui-select" required>
            <option value="">الدور</option>
            <option v-for="role in roles" :key="role.id" :value="role.name">{{ role.name }}</option>
        </select>
        <label class="ui-check-label"><input v-model="form.is_active" type="checkbox" class="ui-check" />حساب نشط</label>
        <button class="ui-btn ui-btn-primary">{{ editing ? 'تحديث المستخدم' : 'إنشاء مستخدم' }}</button>
    </form>

    <div class="ui-table-wrap">
        <table class="ui-table">
            <thead class="ui-table-head">
                <tr>
                    <th class="p-4">الاسم</th>
                    <th class="p-4">البريد الإلكتروني</th>
                    <th class="p-4">الدور</th>
                    <th class="p-4">الحالة</th>
                    <th class="p-4 text-center">إجراءات</th>
                </tr>
            </thead>
            <tbody class="ui-table-body">
                <tr v-for="user in users.data" :key="user.id" class="ui-table-row">
                    <td class="p-4 font-black text-slate-900">{{ user.name }}</td>
                    <td class="p-4 text-slate-500">{{ user.email }}</td>
                    <td class="p-4 font-bold text-forest">{{ user.roles?.[0]?.name || '-' }}</td>
                    <td class="p-4">
                        <span class="inline-flex items-center gap-1 rounded-full border px-2.5 py-1 text-xs font-bold" :class="user.is_active ? 'border-emerald-200 bg-emerald-50 text-emerald-700' : 'border-rose-200 bg-rose-50 text-rose-700'">
                            {{ user.is_active ? 'نشط' : 'معطل' }}
                        </span>
                    </td>
                    <td class="p-4">
                        <div class="flex items-center justify-center gap-2">
                            <button class="inline-flex items-center gap-1 rounded-xl border border-amber-200/80 bg-amber-50 px-2.5 py-1.5 text-xs font-bold text-amber-700 hover:bg-amber-100" @click="edit(user)">
                                <span>تعديل</span>
                            </button>
                            <button class="inline-flex items-center gap-1 rounded-xl border border-sky-200/80 bg-sky-50 px-2.5 py-1.5 text-xs font-bold text-sky-700 hover:bg-sky-100" @click="router.post(route('admin.users.toggle', user.id))">
                                <span>{{ user.is_active ? 'تعطيل' : 'تفعيل' }}</span>
                            </button>
                            <button class="inline-flex items-center gap-1 rounded-xl border border-rose-200/80 bg-rose-50 px-2.5 py-1.5 text-xs font-bold text-rose-700 hover:bg-rose-100" @click="deleting = user.id">
                                <span>حذف</span>
                            </button>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="grid gap-4 md:hidden">
        <div v-for="user in users.data" :key="user.id" class="ui-mobile-card">
            <div class="flex items-center justify-between border-b border-slate-100 pb-2">
                <h3 class="text-sm font-black text-slate-900">{{ user.name }}</h3>
                <span class="rounded-full border px-2.5 py-0.5 text-xs font-bold" :class="user.is_active ? 'border-emerald-200 bg-emerald-50 text-emerald-700' : 'border-rose-200 bg-rose-50 text-rose-700'">
                    {{ user.is_active ? 'نشط' : 'معطل' }}
                </span>
            </div>
            <div class="space-y-1 text-xs text-slate-600">
                <p>البريد: <strong class="text-slate-900">{{ user.email }}</strong></p>
                <p>الدور: <strong class="text-forest">{{ user.roles?.[0]?.name || '-' }}</strong></p>
            </div>
            <div class="flex items-center justify-end gap-2 border-t border-slate-100 pt-3">
                <button class="rounded-xl border border-amber-200 bg-amber-50 px-3 py-1.5 text-xs font-bold text-amber-700" @click="edit(user)">تعديل</button>
                <button class="rounded-xl border border-sky-200 bg-sky-50 px-3 py-1.5 text-xs font-bold text-sky-700" @click="router.post(route('admin.users.toggle', user.id))">{{ user.is_active ? 'تعطيل' : 'تفعيل' }}</button>
                <button class="rounded-xl border border-rose-200 bg-rose-50 px-3 py-1.5 text-xs font-bold text-rose-700" @click="deleting = user.id">حذف</button>
            </div>
        </div>
    </div>

    <Pagination :links="users.links" />
    <ConfirmModal :show="!!deleting" @cancel="deleting = null" @confirm="() => { router.delete(route('admin.users.destroy', deleting)); deleting = null; }" />
</template>
