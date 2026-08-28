<script setup>
import { Head, router, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import AdminLayout from '../../../Layouts/AdminLayout.vue';
import PageHeader from '../../../Components/PageHeader.vue';
import ConfirmModal from '../../../Components/ConfirmModal.vue';

defineOptions({ layout: AdminLayout });
defineProps({ roles: Array, permissionGroups: Object });
const form = useForm({ name: '', permissions: [] });
const editing = ref(null);
const deleting = ref(null);

const edit = (role) => {
    editing.value = role.id;
    form.name = role.name;
    form.permissions = role.permissions.map((item) => item.name);
};

const save = () => {
    if (editing.value) {
        form.put(route('admin.roles.update', editing.value), { onSuccess: () => { editing.value = null; form.reset(); } });
    } else {
        form.post(route('admin.roles.store'), { onSuccess: () => form.reset() });
    }
};
</script>

<template>
    <Head title="الأدوار والصلاحيات" />

    <PageHeader title="الأدوار والصلاحيات" subtitle="تحديد صلاحيات كل دور وظيفي في النظام" />

    <form class="ui-panel mb-6" @submit.prevent="save">
        <input v-model="form.name" placeholder="اسم الدور" class="ui-input mb-4" required />
        <div v-for="(group, title) in permissionGroups" :key="title" class="mb-4">
            <h3 class="mb-2 text-sm font-black text-forest">{{ title }}</h3>
            <div class="grid gap-2 md:grid-cols-3">
                <label v-for="(label, key) in group" :key="key" class="ui-check-label">
                    <input v-model="form.permissions" type="checkbox" class="ui-check" :value="key" />
                    {{ label }}
                </label>
            </div>
        </div>
        <button class="ui-btn ui-btn-primary">{{ editing ? 'تحديث الدور' : 'إنشاء دور جديد' }}</button>
    </form>

    <div class="ui-table-wrap">
        <table class="ui-table">
            <thead class="ui-table-head">
                <tr>
                    <th class="p-4">الدور الوظيفي</th>
                    <th class="p-4">عدد المستخدمين</th>
                    <th class="p-4 text-center">إجراءات</th>
                </tr>
            </thead>
            <tbody class="ui-table-body">
                <tr v-for="role in roles" :key="role.id" class="ui-table-row">
                    <td class="p-4 font-black text-slate-900">{{ role.name }}</td>
                    <td class="p-4 font-bold text-forest">{{ role.users_count }} مستخدم</td>
                    <td class="p-4">
                        <div class="flex items-center justify-center gap-2">
                            <button class="rounded-xl border border-amber-200/80 bg-amber-50 px-2.5 py-1.5 text-xs font-bold text-amber-700 hover:bg-amber-100" @click="edit(role)">تعديل الصلاحيات</button>
                            <button class="rounded-xl border border-rose-200/80 bg-rose-50 px-2.5 py-1.5 text-xs font-bold text-rose-700 hover:bg-rose-100" @click="deleting = role.id">حذف</button>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <ConfirmModal :show="!!deleting" @cancel="deleting = null" @confirm="() => { router.delete(route('admin.roles.destroy', deleting)); deleting = null; }" />
</template>
