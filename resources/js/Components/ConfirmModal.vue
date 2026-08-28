<script setup>
defineProps({
    show: Boolean,
    title: { type: String, default: 'تأكيد العملية' },
    message: { type: String, default: 'هل أنت متأكد من تنفيذ هذه العملية؟' },
});

const emit = defineEmits(['confirm', 'cancel']);
</script>

<template>
    <div v-if="show" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/40 p-4 backdrop-blur-sm">
        <div class="w-full max-w-md rounded-3xl border border-slate-200/80 bg-white p-6 shadow-2xl">
            <h3 class="mb-2 text-lg font-black text-slate-900">{{ title }}</h3>
            <p class="mb-6 text-sm leading-7 text-slate-500">{{ message }}</p>
            <div class="flex justify-end gap-3">
                <button class="ui-btn ui-btn-secondary" @click="emit('cancel')">إلغاء</button>
                <button class="ui-btn bg-rose-600 text-white hover:bg-rose-700" @click="emit('confirm')">تأكيد الحذف</button>
            </div>
        </div>
    </div>
</template>
