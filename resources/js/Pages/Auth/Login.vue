<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import AuthLayout from '../../Layouts/AuthLayout.vue';

defineOptions({ layout: AuthLayout });

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => form.post(route('login.store'));
</script>

<template>
    <Head title="الدخول إلى الحساب" />

    <div class="mb-6 text-center">
        <h1 class="text-2xl font-black text-slate-900">الدخول إلى الحساب</h1>
        <p class="mt-2 text-sm text-slate-500">أدخل بياناتك للوصول إلى لوحة التحكم</p>
    </div>

    <form class="space-y-4" @submit.prevent="submit">
        <div>
            <label class="ui-label">البريد الإلكتروني</label>
            <input v-model="form.email" type="email" class="ui-input" required />
            <p v-if="form.errors.email" class="mt-1 text-xs text-red-600">{{ form.errors.email }}</p>
        </div>
        <div>
            <label class="ui-label">كلمة المرور</label>
            <input v-model="form.password" type="password" class="ui-input" required />
        </div>
        <label class="ui-check-label">
            <input v-model="form.remember" type="checkbox" class="ui-check" />
            تذكرني
        </label>
        <button class="ui-btn ui-btn-primary w-full py-3.5" :disabled="form.processing">دخول</button>
    </form>
</template>
