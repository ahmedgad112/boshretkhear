<script setup>
import { Head, router, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import AdminLayout from '../../../Layouts/AdminLayout.vue';
import PageHeader from '../../../Components/PageHeader.vue';
import ConfirmModal from '../../../Components/ConfirmModal.vue';

defineOptions({ layout: AdminLayout });
const props = defineProps({ types: Array });
const form = useForm({ name: '', description: '', is_active: true });
const editing = ref(null);
const deleting = ref(null);

const save = () => {
    if (editing.value) {
        form.put(route('admin.property-types.update', editing.value), { onSuccess: () => { editing.value = null; form.reset(); } });
    } else {
        form.post(route('admin.property-types.store'), { onSuccess: () => form.reset() });
    }
};

const edit = (type) => {
    editing.value = type.id;
    form.name = type.name;
    form.description = type.description;
    form.is_active = type.is_active;
};

const remove = () => {
    router.delete(route('admin.property-types.destroy', deleting.value));
    deleting.value = null;
};
</script>

<template>
    <Head title="أنواع العقارات" />

    <PageHeader title="أنواع العقارات" subtitle="تصنيف العقارات حسب النوع (شقة، فيلا، محل...)" />

    <form class="ui-panel mb-6 grid gap-3 md:grid-cols-4" @submit.prevent="save">
        <input v-model="form.name" placeholder="اسم النوع" class="ui-input" required />
        <input v-model="form.description" placeholder="الوصف" class="ui-input md:col-span-2" />
        <button class="ui-btn ui-btn-primary">{{ editing ? 'تحديث' : 'إضافة' }}</button>
    </form>

    <div class="ui-table-wrap">
        <table class="ui-table">
            <thead class="ui-table-head">
                <tr>
                    <th class="p-4">اسم النوع</th>
                    <th class="p-4">الوصف</th>
                    <th class="p-4">عدد العقارات</th>
                    <th class="p-4 text-center">إجراءات</th>
                </tr>
            </thead>
            <tbody class="ui-table-body">
                <tr v-for="type in types" :key="type.id" class="ui-table-row">
                    <td class="p-4 font-black text-slate-900">{{ type.name }}</td>
                    <td class="p-4 text-xs text-slate-500">{{ type.description || '-' }}</td>
                    <td class="p-4 font-bold text-forest">{{ type.properties_count }}</td>
                    <td class="p-4">
                        <div class="flex items-center justify-center gap-2">
                            <button class="rounded-xl border border-amber-200/80 bg-amber-50 px-2.5 py-1.5 text-xs font-bold text-amber-700" @click="edit(type)">تعديل</button>
                            <button class="rounded-xl border border-rose-200/80 bg-rose-50 px-2.5 py-1.5 text-xs font-bold text-rose-700" @click="deleting = type.id">حذف</button>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <ConfirmModal :show="!!deleting" @cancel="deleting = null" @confirm="remove" />
</template>
