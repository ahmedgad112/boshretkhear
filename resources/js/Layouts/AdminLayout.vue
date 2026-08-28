<script setup>
import { Link, router, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import FlashAlert from '../Components/FlashAlert.vue';
import { useCan } from '../composables/useCan';

const page = usePage();
const { can } = useCan();
const open = ref(false);
const search = ref('');
const results = ref(null);
const showNotes = ref(false);
const showProfileMenu = ref(false);

const user = computed(() => page.props.auth?.user);
const settings = computed(() => page.props.settings || {});
const notifications = computed(() => page.props.notifications || { unread: 0, items: [] });

const groups = computed(() => [
    {
        title: 'الرئيسية والتحليل',
        items: [
            { href: route('admin.dashboard'), label: 'الرئيسية', icon: 'dashboard', show: can('dashboard.view') },
        ],
    },
    {
        title: 'إدارة العقارات',
        items: [
            { href: route('admin.properties.index'), label: 'العقارات', icon: 'building', show: can('properties.view') },
            { href: route('admin.property-types.index'), label: 'أنواع العقارات', icon: 'type', show: can('property_types.view') },
        ],
    },
    {
        title: 'العمليات والعملاء',
        items: [
            { href: route('admin.customers.index'), label: 'العملاء', icon: 'users', show: can('customers.view') },
            { href: route('admin.bookings.index'), label: 'الحجوزات', icon: 'calendar', show: can('bookings.view') },
            { href: route('admin.rentals.index'), label: 'الإيجارات', icon: 'key', show: can('bookings.view') },
            { href: route('admin.sales.index'), label: 'المبيعات', icon: 'tag', show: can('sales.view') },
        ],
    },
    {
        title: 'المالية والتقارير',
        items: [
            { href: route('admin.payments.index'), label: 'المدفوعات', icon: 'credit-card', show: can('payments.view') },
            { href: route('admin.expenses.index'), label: 'المصروفات', icon: 'receipt', show: can('expenses.view') },
            { href: route('admin.accounts.index'), label: 'الحسابات المالية', icon: 'wallet', show: can('accounts.view') },
            { href: route('admin.reports.index'), label: 'التقارير الإحصائية', icon: 'chart', show: can('reports.view') },
        ],
    },
    {
        title: 'إدارة النظام',
        items: [
            { href: route('admin.users.index'), label: 'المستخدمون', icon: 'user-cog', show: can('users.view') },
            { href: route('admin.roles.index'), label: 'الأدوار والصلاحيات', icon: 'shield', show: can('roles.view') },
            { href: route('admin.settings.edit'), label: 'الإعدادات العامة', icon: 'settings', show: can('settings.view') },
            { href: route('admin.activity-logs.index'), label: 'سجل العمليات', icon: 'log', show: can('activity_logs.view') },
        ],
    },
].map(group => ({
    ...group,
    items: group.items.filter(item => item.show)
})).filter(group => group.items.length > 0));

const doSearch = async () => {
    if (search.value.length < 2) {
        results.value = null;
        return;
    }

    try {
        const response = await fetch(`${route('admin.search')}?q=${encodeURIComponent(search.value)}`, {
            headers: { Accept: 'application/json' },
        });
        results.value = await response.json();
    } catch (e) {
        console.error(e);
    }
};

const pathOf = (href) => {
    try {
        return new URL(href, window.location.origin).pathname;
    } catch {
        return href;
    }
};

const normalizePath = (path) => {
    const clean = String(path || '/').split('?')[0].replace(/\/+$/, '');

    return clean || '/';
};

const currentPath = computed(() => normalizePath(page.url));

const menuPaths = computed(() =>
    groups.value.flatMap((group) => group.items.map((item) => normalizePath(pathOf(item.href)))),
);

const isActive = (href) => {
    const path = normalizePath(pathOf(href));
    const current = currentPath.value;
    const matches = menuPaths.value.filter(
        (candidate) => current === candidate || current.startsWith(`${candidate}/`),
    );

    if (!matches.length) {
        return current === path;
    }

    const best = matches.reduce((longest, candidate) =>
        candidate.length > longest.length ? candidate : longest,
    );

    return path === best;
};

const logout = () => router.post(route('logout'));
</script>

<template>
    <div class="min-h-screen bg-slate-50/80 font-cairo antialiased selection:bg-amber-500 selection:text-white">
        <!-- Sidebar Backdrop for Mobile -->
        <div v-if="open" class="fixed inset-0 z-40 bg-slate-900/40 backdrop-blur-xs lg:hidden" @click="open = false"></div>

        <!-- Light Sidebar -->
        <aside
            class="fixed inset-y-0 right-0 z-50 flex w-72 flex-col bg-white text-slate-700 shadow-xl transition-all duration-300 border-l border-slate-200/80"
            :class="open ? 'translate-x-0' : 'translate-x-full lg:translate-x-0'"
        >
            <!-- Sidebar Header -->
            <div class="flex items-center justify-between border-b border-slate-100 p-5">
                <Link :href="route('home')" class="flex items-center gap-2.5 group">
                    <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-forest text-amber-400 font-black shadow-sm group-hover:scale-105 transition-transform">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5m0 0h4m-4 0V11m0 0h4m-4 0H7" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="font-extrabold text-slate-900 text-base leading-tight">{{ settings.business_name || 'بشرى خير' }}</h2>
                        <p class="text-[11px] font-medium text-slate-400">لوحة الإدارة العقارية</p>
                    </div>
                </Link>

                <button class="rounded-lg p-1.5 text-slate-400 hover:bg-slate-100 lg:hidden" @click="open = false">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Navigation Links -->
            <nav class="flex-1 overflow-y-auto p-4 space-y-6">
                <div v-for="group in groups" :key="group.title" class="space-y-1.5">
                    <p class="px-3 text-[11px] font-bold text-slate-400 uppercase tracking-wider">{{ group.title }}</p>
                    <Link
                        v-for="item in group.items"
                        :key="item.href"
                        :href="item.href"
                        class="flex items-center justify-between rounded-2xl px-3.5 py-2.5 text-sm font-semibold transition-all group"
                        :class="isActive(item.href) ? 'bg-forest text-white shadow-sm font-bold' : 'text-slate-600 hover:bg-emerald-50/60 hover:text-forest'"
                        @click="open = false"
                    >
                        <div class="flex items-center gap-3">
                            <!-- Icons -->
                            <svg v-if="item.icon === 'dashboard'" class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                            </svg>
                            <svg v-else-if="item.icon === 'building'" class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5m0 0h4m-4 0V11m0 0h4m-4 0H7" />
                            </svg>
                            <svg v-else-if="item.icon === 'type'" class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                            </svg>
                            <svg v-else-if="item.icon === 'users'" class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                            <svg v-else-if="item.icon === 'calendar'" class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <svg v-else-if="item.icon === 'key'" class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                            </svg>
                            <svg v-else-if="item.icon === 'tag'" class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                            </svg>
                            <svg v-else-if="item.icon === 'credit-card'" class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                            </svg>
                            <svg v-else-if="item.icon === 'receipt'" class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <svg v-else-if="item.icon === 'wallet'" class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                            <svg v-else-if="item.icon === 'chart'" class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                            <svg v-else-if="item.icon === 'user-cog'" class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            <svg v-else-if="item.icon === 'shield'" class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                            </svg>
                            <svg v-else-if="item.icon === 'settings'" class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <svg v-else class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                            <span>{{ item.label }}</span>
                        </div>
                        <span v-if="isActive(item.href)" class="h-2 w-2 rounded-full bg-amber-400"></span>
                    </Link>
                </div>
            </nav>

            <!-- Sidebar Footer / Quick Site Link -->
            <div class="border-t border-slate-100 p-4">
                <Link
                    :href="route('home')"
                    target="_blank"
                    class="flex w-full items-center justify-center gap-2 rounded-2xl bg-slate-50 border border-slate-200 py-2.5 text-xs font-bold text-slate-700 hover:bg-slate-100 transition-all"
                >
                    <svg class="h-4 w-4 text-forest" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                    </svg>
                    <span>معاينة الموقع العام</span>
                </Link>
            </div>
        </aside>

        <!-- Main Wrapper -->
        <div class="lg:pr-72">
            <!-- Header Bar -->
            <header class="sticky top-0 z-30 flex items-center justify-between gap-4 border-b border-slate-200/80 bg-white/90 px-4 py-3.5 backdrop-blur-md md:px-8 shadow-2xs">
                <!-- Mobile Menu Button -->
                <button class="rounded-xl p-2 text-slate-700 hover:bg-slate-100 lg:hidden" @click="open = true">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>

                <!-- Search Input -->
                <div class="relative flex-1 max-w-xl">
                    <div class="relative">
                        <input
                            v-model="search"
                            type="search"
                            placeholder="بحث سريع في العقارات والعملاء والحجوزات والمبيعات..."
                            class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-2.5 pr-10 text-sm focus:bg-white"
                            @input="doSearch"
                        />
                        <svg class="absolute right-3.5 top-3 h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>

                    <!-- Search Modal Dropdown -->
                    <div v-if="results" class="absolute right-0 mt-2 w-full max-h-96 overflow-y-auto rounded-3xl bg-white p-5 shadow-2xl ring-1 ring-slate-900/10 z-50">
                        <div v-for="(group, key) in results" :key="key" class="mb-4 last:mb-0">
                            <p class="mb-2 text-xs font-black text-forest border-b border-slate-100 pb-1">
                                {{ { properties: 'العقارات', customers: 'العملاء', bookings: 'الحجوزات', sales: 'المبيعات', payments: 'المدفوعات', expenses: 'المصروفات' }[key] || key }}
                            </p>
                            <div class="space-y-1">
                                <Link
                                    v-for="item in group"
                                    :key="item.id"
                                    :href="item.url"
                                    class="block rounded-xl px-3 py-2 text-sm text-slate-700 hover:bg-slate-50 hover:text-forest transition-colors"
                                    @click="results = null; search = ''"
                                >
                                    {{ item.label }}
                                </Link>
                                <p v-if="!group.length" class="text-xs text-slate-400 px-2 py-1">لا توجد نتائج مطابقة</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Header Actions -->
                <div class="flex items-center gap-3">
                    <!-- Notifications -->
                    <div class="relative">
                        <button
                            class="relative flex h-10 w-10 items-center justify-center rounded-2xl border border-slate-200 bg-slate-50 text-slate-700 hover:bg-slate-100 transition-colors"
                            @click="showNotes = !showNotes; showProfileMenu = false"
                        >
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                            </svg>
                            <span v-if="notifications.unread" class="absolute -top-1 -left-1 flex h-5 w-5 items-center justify-center rounded-full bg-amber-500 text-[10px] font-black text-slate-950 shadow-sm">
                                {{ notifications.unread }}
                            </span>
                        </button>

                        <!-- Notification Dropdown -->
                        <div v-if="showNotes" class="absolute left-0 mt-3 w-80 rounded-3xl bg-white p-5 shadow-2xl ring-1 ring-slate-900/10 z-50 space-y-3">
                            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                                <h4 class="font-extrabold text-slate-900 text-sm">الإشعارات والتنبيهات</h4>
                                <Link :href="route('admin.notifications.read-all')" method="post" class="text-xs font-bold text-forest hover:underline">تعليم الكل كمقروء</Link>
                            </div>
                            <div class="max-h-64 overflow-y-auto space-y-2">
                                <div v-for="item in notifications.items" :key="item.id" class="rounded-2xl bg-slate-50 p-3 text-xs transition-colors hover:bg-slate-100">
                                    <Link :href="route('admin.notifications.read', item.id)" method="post" class="block space-y-1">
                                        <p class="font-bold text-slate-900">{{ item.title }}</p>
                                        <p class="text-slate-500 leading-relaxed">{{ item.message }}</p>
                                    </Link>
                                </div>
                                <p v-if="!notifications.items.length" class="text-center text-xs text-slate-400 py-4">لا توجد إشعارات حديثة</p>
                            </div>
                        </div>
                    </div>

                    <!-- User Profile Dropdown -->
                    <div class="relative">
                        <button
                            class="flex items-center gap-3 rounded-2xl border border-slate-200 bg-slate-50 p-1.5 pl-3 hover:bg-slate-100 transition-colors"
                            @click="showProfileMenu = !showProfileMenu; showNotes = false"
                        >
                            <div class="flex h-8 w-8 items-center justify-center rounded-xl bg-forest text-xs font-bold text-white shadow-xs">
                                {{ user?.name ? user.name.charAt(0) : 'م' }}
                            </div>
                            <div class="hidden text-right text-xs md:block">
                                <p class="font-extrabold text-slate-900 leading-none">{{ user?.name }}</p>
                                <p class="text-[10px] text-slate-400 mt-0.5">{{ user?.roles?.[0]?.name || 'مدير' }}</p>
                            </div>
                            <svg class="h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>

                        <div v-if="showProfileMenu" class="absolute left-0 mt-3 w-48 rounded-2xl bg-white p-2 shadow-2xl ring-1 ring-slate-900/10 z-50 space-y-1">
                            <Link :href="route('admin.settings.edit')" class="block rounded-xl px-3 py-2 text-xs font-bold text-slate-700 hover:bg-slate-50">
                                إعدادات النظام
                            </Link>
                            <button @click="logout" class="block w-full text-right rounded-xl px-3 py-2 text-xs font-bold text-rose-600 hover:bg-rose-50">
                                تسجيل الخروج
                            </button>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Main Page Content -->
            <main class="p-4 md:p-8">
                <FlashAlert />
                <slot />
            </main>
        </div>
    </div>
</template>


