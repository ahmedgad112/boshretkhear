<script setup>
import { Link, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import FlashAlert from '../Components/FlashAlert.vue';

const page = usePage();
const open = ref(false);
const settings = computed(() => page.props.settings || {});
const user = computed(() => page.props.auth?.user);
const name = computed(() => settings.value.business_name || 'بشرة خير');

const links = [
    { href: route('home'), label: 'الرئيسية' },
    { href: route('properties.index'), label: 'الشقق' },
    { href: route('about'), label: 'من نحن' },
    { href: route('contact'), label: 'تواصل معنا' },
];

const pathOf = (href) => {
    try {
        return new URL(href, window.location.origin).pathname;
    } catch {
        return href;
    }
};

const PHONE_NUMBER = '+201515789505';
const PHONE_DISPLAY = '+20 151 578 9505';
const WHATSAPP_NUMBER = '201210072971';
const WHATSAPP_DISPLAY = '+20 121 007 2971';
const FACEBOOK_URL = 'https://www.facebook.com/share/1BwGnxPj9o/';

const isActive = (href) => page.url === pathOf(href) || (href !== route('home') && page.url.startsWith(pathOf(href)));
</script>

<template>
    <div class="flex min-h-screen flex-col bg-cream font-cairo antialiased selection:bg-amber-500 selection:text-white">
        <!-- Top Announcement / Contact Bar (Light Mode) -->
        <div class="bg-emerald-50/80 text-emerald-950 text-xs py-2.5 px-4 border-b border-emerald-100/80 hidden sm:block">
            <div class="mx-auto flex max-w-7xl items-center justify-between font-medium">
                <div class="flex items-center gap-6">
                    <a :href="`tel:${PHONE_NUMBER}`" class="flex items-center gap-1.5 text-forest hover:opacity-80 transition-opacity">
                        <svg class="h-3.5 w-3.5 text-forest shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                        </svg>
                        <span dir="ltr" class="inline-block">{{ PHONE_DISPLAY }}</span>
                    </a>
                    <a :href="`https://wa.me/${WHATSAPP_NUMBER}`" target="_blank" rel="noopener noreferrer" class="flex items-center gap-1.5 text-forest hover:opacity-80 transition-opacity">
                        <svg class="h-3.5 w-3.5 text-forest shrink-0" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.435 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z" />
                        </svg>
                        <span dir="ltr" class="inline-block">{{ WHATSAPP_DISPLAY }}</span>
                    </a>
                </div>
                <div class="flex items-center gap-2">
                    <span class="inline-block h-2 w-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    <span>منصة بشرة خير لعرض الشقق - متاحون لخدمتكم</span>
                </div>
            </div>
        </div>

        <!-- Header -->
        <header class="sticky top-0 z-40 border-b border-slate-200/80 bg-white/95 backdrop-blur-md transition-all shadow-2xs">
            <div class="mx-auto flex max-w-7xl items-center justify-between px-4 py-3.5 md:px-6">
                <!-- Logo -->
                <Link :href="route('home')" class="flex items-center gap-2.5 text-2xl font-black text-forest group">
                    <div class="flex h-10 w-10 items-center justify-center rounded-2xl bg-gradient-to-tr from-forest to-forest-light text-amber-400 shadow-sm group-hover:scale-105 transition-transform">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5m0 0h4m-4 0V11m0 0h4m-4 0H7" />
                        </svg>
                    </div>
                    <div class="flex flex-col">
                        <span class="leading-none text-forest font-extrabold tracking-tight">{{ name }}</span>
                        <span class="text-[10px] font-semibold text-amber-600 tracking-wider">عرض الشقق</span>
                    </div>
                </Link>

                <!-- Navigation Desktop -->
                <nav class="hidden items-center gap-1.5 lg:flex">
                    <Link
                        v-for="link in links"
                        :key="link.label"
                        :href="link.href"
                        class="rounded-xl px-4 py-2 text-sm font-semibold transition-all"
                        :class="isActive(link.href) ? 'bg-forest/10 text-forest font-bold' : 'text-slate-700 hover:bg-slate-100 hover:text-forest'"
                    >
                        {{ link.label }}
                    </Link>
                </nav>

                <!-- User Button Desktop -->
                <div class="hidden items-center gap-3 lg:flex">
                    <Link
                        v-if="user"
                        :href="route('admin.dashboard')"
                        class="inline-flex items-center gap-2 rounded-2xl bg-forest px-5 py-2.5 text-sm font-bold text-white shadow-xs hover:bg-forest-light transition-all active:scale-95"
                    >
                        <svg class="h-4 w-4 text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                        </svg>
                        <span>لوحة التحكم</span>
                    </Link>
                    <Link
                        v-else
                        :href="route('login')"
                        class="inline-flex items-center gap-2 rounded-2xl bg-forest px-5 py-2.5 text-sm font-bold text-white shadow-xs hover:bg-forest-light transition-all active:scale-95"
                    >
                        <svg class="h-4 w-4 text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                        </svg>
                        <span>دخول الحساب</span>
                    </Link>
                </div>

                <!-- Mobile Toggle -->
                <button
                    class="rounded-xl p-2 text-slate-700 hover:bg-slate-100 lg:hidden"
                    @click="open = !open"
                    aria-label="القائمة"
                >
                    <svg v-if="!open" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                    <svg v-else class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Mobile Navigation Drawer -->
            <div v-if="open" class="space-y-2 border-t border-slate-200 bg-white/95 px-4 py-4 backdrop-blur-md lg:hidden">
                <Link
                    v-for="link in links"
                    :key="link.label"
                    :href="link.href"
                    class="block rounded-xl px-4 py-2.5 text-sm font-bold transition-all"
                    :class="isActive(link.href) ? 'bg-forest/10 text-forest' : 'text-slate-700 hover:bg-slate-50'"
                    @click="open = false"
                >
                    {{ link.label }}
                </Link>
                <div class="pt-2 border-t border-slate-100">
                    <Link
                        :href="user ? route('admin.dashboard') : route('login')"
                        class="block w-full text-center rounded-xl bg-forest px-4 py-3 text-sm font-bold text-white"
                        @click="open = false"
                    >
                        {{ user ? 'لوحة التحكم' : 'الدخول إلى الحساب' }}
                    </Link>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="mx-auto flex-1 w-full max-w-7xl px-4 py-8 md:px-6">
            <FlashAlert />
            <slot />
        </main>

        <!-- Light Footer -->
        <footer class="mt-16 bg-white text-slate-700 border-t border-slate-200/80 shadow-2xs">
            <div class="mx-auto grid max-w-7xl gap-8 px-4 py-12 md:grid-cols-4 md:px-6">
                <!-- Brand Info -->
                <div class="md:col-span-2 space-y-4">
                    <div class="flex items-center gap-2 text-2xl font-black text-forest">
                        <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-forest text-amber-400">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5m0 0h4m-4 0V11m0 0h4m-4 0H7" />
                            </svg>
                        </div>
                        <span>{{ name }}</span>
                    </div>
                    <p class="text-sm leading-7 text-slate-500 max-w-md">
                        منصة عربية لعرض الشقق بتفاصيلها وصورها. تصفّح الوحدات المتاحة وتواصل معنا مباشرة للاستفسار.
                    </p>
                </div>

                <!-- Quick Links -->
                <div class="space-y-3">
                    <h4 class="text-base font-extrabold text-slate-900">روابط سريعة</h4>
                    <ul class="space-y-2 text-sm text-slate-600">
                        <li><Link :href="route('home')" class="hover:text-forest transition-colors">الرئيسية</Link></li>
                        <li><Link :href="route('properties.index')" class="hover:text-forest transition-colors">جميع الشقق</Link></li>
                        <li><Link :href="route('about')" class="hover:text-forest transition-colors">عن الشركة</Link></li>
                        <li><Link :href="route('contact')" class="hover:text-forest transition-colors">تواصل معنا</Link></li>
                    </ul>
                </div>

                <!-- Contact Details -->
                <div class="space-y-3 text-sm text-slate-600">
                    <h4 class="text-base font-extrabold text-slate-900">التواصل</h4>
                    <a :href="`tel:${PHONE_NUMBER}`" class="flex items-center gap-2 hover:text-forest transition-colors">
                        <svg class="h-4 w-4 text-forest shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                        </svg>
                        <span dir="ltr" class="inline-block">{{ PHONE_DISPLAY }}</span>
                    </a>
                    <a :href="`https://wa.me/${WHATSAPP_NUMBER}`" target="_blank" rel="noopener noreferrer" class="flex items-center gap-2 hover:text-forest transition-colors">
                        <svg class="h-4 w-4 text-forest shrink-0" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.435 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z" />
                        </svg>
                        <span dir="ltr" class="inline-block">{{ WHATSAPP_DISPLAY }}</span>
                    </a>
                    <a :href="FACEBOOK_URL" target="_blank" rel="noopener noreferrer" class="flex items-center gap-2 hover:text-forest transition-colors">
                        <svg class="h-4 w-4 text-forest shrink-0" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z" />
                        </svg>
                        <span>صفحة فيسبوك</span>
                    </a>
                </div>
            </div>

            <div class="border-t border-slate-100 py-6 text-center text-xs text-slate-400">
                جميع الحقوق محفوظة © {{ new Date().getFullYear() }} {{ name }}
            </div>
        </footer>
    </div>
</template>


