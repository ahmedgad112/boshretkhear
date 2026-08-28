<script setup>
import { Head } from '@inertiajs/vue3';
import { reactive } from 'vue';
import PublicLayout from '../../Layouts/PublicLayout.vue';

defineOptions({ layout: PublicLayout });

const WHATSAPP_NUMBER = '201210072971';
const PHONE_NUMBER = '+201515789505';
const PHONE_DISPLAY = '+20 151 578 9505';
const WHATSAPP_DISPLAY = '+20 121 007 2971';
const FACEBOOK_URL = 'https://www.facebook.com/share/1BwGnxPj9o/';

const form = reactive({
    name: '',
    phone: '',
    type: 'contact',
    message: '',
});

const typeLabels = {
    contact: 'استفسار عام',
    viewing: 'طلب معاينة عقار',
    booking: 'طلب حجز / إيجار',
    sale: 'مهتم بشراء عقار',
};

const submit = () => {
    const lines = [
        'رسالة من صفحة تواصل معنا',
        '',
        `الاسم: ${form.name}`,
        `الهاتف: ${form.phone}`,
        `نوع الطلب: ${typeLabels[form.type] || form.type}`,
        '',
        'الرسالة:',
        form.message,
    ];

    const url = `https://wa.me/${WHATSAPP_NUMBER}?text=${encodeURIComponent(lines.join('\n'))}`;
    window.open(url, '_blank', 'noopener,noreferrer');
};
</script>

<template>
    <Head title="تواصل معنا" />

    <section class="grid gap-8 lg:grid-cols-5">
        <!-- Form -->
        <div class="lg:col-span-3 rounded-3xl bg-white p-6 md:p-8 shadow-sm border border-slate-100">
            <div class="mb-6 border-b border-slate-100 pb-5">
                <span class="text-xs font-bold text-amber-600 tracking-wider">نحن هنا لمساعدتك</span>
                <h1 class="mt-1 text-2xl font-black text-slate-900 md:text-3xl">تواصل معنا</h1>
                <p class="mt-2 text-sm leading-7 text-slate-500">
                    يسعدنا استقبال استفساراتك حول البيع أو الإيجار أو إدارة العقارات. أرسل رسالتك عبر واتساب وسنرد عليك في أقرب وقت.
                </p>
            </div>

            <form class="space-y-4" @submit.prevent="submit">
                <div>
                    <label class="mb-1 block text-xs font-bold text-slate-700">الاسم الكريم</label>
                    <input
                        v-model="form.name"
                        placeholder="أدخل اسمك بالكامل"
                        class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-forest/20"
                        required
                    />
                </div>

                <div>
                    <label class="mb-1 block text-xs font-bold text-slate-700">رقم الهاتف / الواتساب</label>
                    <input
                        v-model="form.phone"
                        placeholder="05xxxxxxxx"
                        class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-forest/20"
                        required
                    />
                </div>

                <div>
                    <label class="mb-1 block text-xs font-bold text-slate-700">نوع الطلب</label>
                    <select
                        v-model="form.type"
                        class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-forest/20"
                    >
                        <option value="contact">استفسار عام</option>
                        <option value="viewing">طلب معاينة عقار</option>
                        <option value="booking">طلب حجز / إيجار</option>
                        <option value="sale">مهتم بشراء عقار</option>
                    </select>
                </div>

                <div>
                    <label class="mb-1 block text-xs font-bold text-slate-700">رسالتك</label>
                    <textarea
                        v-model="form.message"
                        rows="5"
                        placeholder="اكتب استفسارك أو طلبك هنا..."
                        class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-forest/20"
                        required
                    />
                </div>

                <button
                    type="submit"
                    class="inline-flex w-full items-center justify-center gap-2 rounded-2xl bg-forest py-3.5 text-sm font-extrabold text-white shadow-md transition-all hover:bg-forest-light active:scale-[0.98] sm:w-auto sm:px-8"
                >
                    <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.435 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z" />
                    </svg>
                    <span>إرسال عبر واتساب</span>
                </button>
            </form>
        </div>

        <!-- Contact Info -->
        <aside class="lg:col-span-2 space-y-4">
            <div class="rounded-3xl bg-gradient-to-br from-forest to-forest-light p-6 md:p-8 text-white shadow-lg">
                <h2 class="mb-2 text-xl font-black">بيانات التواصل</h2>
                <p class="mb-6 text-sm leading-7 text-white/80">
                    تواصل معنا مباشرة عبر الهاتف أو واتساب، أو تابعنا على فيسبوك.
                </p>

                <div class="space-y-3">
                    <a
                        :href="`tel:${PHONE_NUMBER}`"
                        class="flex items-center gap-3 rounded-2xl bg-white/10 px-4 py-3.5 transition-colors hover:bg-white/15"
                    >
                        <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-white/15">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                            </svg>
                        </span>
                        <span>
                            <span class="block text-xs text-white/70">اتصال هاتفي</span>
                            <span dir="ltr" class="inline-block text-sm font-bold">{{ PHONE_DISPLAY }}</span>
                        </span>
                    </a>

                    <a
                        :href="`https://wa.me/${WHATSAPP_NUMBER}`"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="flex items-center gap-3 rounded-2xl bg-white/10 px-4 py-3.5 transition-colors hover:bg-white/15"
                    >
                        <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-emerald-500/90">
                            <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.435 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z" />
                            </svg>
                        </span>
                        <span>
                            <span class="block text-xs text-white/70">واتساب</span>
                            <span dir="ltr" class="inline-block text-sm font-bold">{{ WHATSAPP_DISPLAY }}</span>
                        </span>
                    </a>

                    <a
                        :href="FACEBOOK_URL"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="flex items-center gap-3 rounded-2xl bg-white/10 px-4 py-3.5 transition-colors hover:bg-white/15"
                    >
                        <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-white/15">
                            <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z" />
                            </svg>
                        </span>
                        <span>
                            <span class="block text-xs text-white/70">فيسبوك</span>
                            <span class="text-sm font-bold">صفحة بشرى خير</span>
                        </span>
                    </a>
                </div>
            </div>

            <div class="rounded-3xl bg-amber-50 border border-amber-100 p-5">
                <p class="text-sm leading-7 text-amber-950">
                    <span class="font-bold">ساعات العمل:</span>
                    متاحون يوميًا للرد على استفساراتكم. أرسل رسالتك عبر واتساب وسنتواصل معك في أقرب وقت ممكن.
                </p>
            </div>
        </aside>
    </section>
</template>
