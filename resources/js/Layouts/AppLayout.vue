<script setup>
import { ref, onMounted, onUnmounted, computed } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';

const props = defineProps({
    title: {
        type: String,
        default: 'Salsabila Control Center',
    },
    subtitle: {
        type: String,
        default: 'Team Leader Dashboard & Project Control System',
    },
    badges: {
        type: Object,
        default: () => ({
            alerts: 0,
            today: 0,
            todos: 0,
            review: 0,
            issues: 0,
        }),
    },
});

const page = usePage();
const user = computed(() => page.props.auth?.user || {});

// Responsive Sidebar States
const desktopCollapsed = ref(false);
const mobileDrawerOpen = ref(false);

function toggleSidebar() {
    if (typeof window !== 'undefined' && window.innerWidth < 1024) {
        mobileDrawerOpen.value = !mobileDrawerOpen.value;
    } else {
        desktopCollapsed.value = !desktopCollapsed.value;
        try {
            localStorage.setItem('salsabila_sidebar_collapsed', String(desktopCollapsed.value));
        } catch (e) {}
    }
}

// Live Date & Clock WIB
const liveDate = ref('-');
const liveTime = ref('-');
let timer = null;

function updateClock() {
    const now = new Date();
    liveDate.value = now.toLocaleDateString('id-ID', {
        timeZone: 'Asia/Jakarta',
        weekday: 'short',
        day: 'numeric',
        month: 'short',
        year: 'numeric',
    });
    liveTime.value = now.toLocaleTimeString('id-ID', {
        timeZone: 'Asia/Jakarta',
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit',
    }) + ' WIB';
}

onMounted(() => {
    try {
        if (typeof window !== 'undefined') {
            const saved = localStorage.getItem('salsabila_sidebar_collapsed');
            if (saved !== null) {
                desktopCollapsed.value = saved === 'true';
            }
        }
    } catch (e) {}

    updateClock();
    timer = setInterval(updateClock, 1000);
});

onUnmounted(() => {
    if (timer) clearInterval(timer);
});

const navItems = [
    { name: 'Dashboard Utama', routeName: 'dashboard', icon: 'fa-chart-pie', badgeKey: 'alerts', badgeColor: 'bg-rose-500 text-white' },
    { name: 'Agenda Hari Ini', routeName: 'today.index', icon: 'fa-calendar-day', badgeKey: 'today', badgeColor: 'bg-amber-400 text-slate-950 font-bold' },
    { name: 'To-Do List & Tugas', routeName: 'tasks.index', icon: 'fa-list-check', badgeKey: 'todos', badgeColor: 'bg-slate-700 text-white' },
    { name: 'Matriks Pekerjaan', routeName: 'matrix.index', icon: 'fa-table-cells' },
    { name: 'Monitoring Proyek', routeName: 'projects.index', icon: 'fa-building-shield' },
    { name: 'Kontrol Tim & PIC', routeName: 'team.index', icon: 'fa-users-gear' },
    { name: 'Pemeriksaan & Revisi', routeName: 'review.index', icon: 'fa-clipboard-check', badgeKey: 'review', badgeColor: 'bg-purple-500 text-white' },
    { name: 'Kendala & Keputusan', routeName: 'issues.index', icon: 'fa-triangle-exclamation', badgeKey: 'issues', badgeColor: 'bg-rose-600 text-white' },
    { name: 'Penagihan & Termin', routeName: 'billing.index', icon: 'fa-file-invoice-dollar' },
    { name: 'Laporan & Ekspor Data', routeName: 'reports.index', icon: 'fa-file-export' },
];

function isRouteActive(name) {
    try {
        return route().current(name) || route().current(name + '.*');
    } catch (e) {
        return false;
    }
}

const emit = defineEmits(['open-task-modal', 'search']);
const searchQuery = ref('');

function onSearchInput() {
    emit('search', searchQuery.value);
}
</script>

<template>
    <div class="h-screen flex overflow-hidden bg-slate-100 font-sans text-slate-900 select-none">
        <!-- ================= MOBILE / TABLET OVERLAY BACKDROP ================= -->
        <transition
            enter-active-class="transition-opacity duration-300 ease-out"
            enter-from-class="opacity-0"
            enter-to-class="opacity-100"
            leave-active-class="transition-opacity duration-200 ease-in"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <div
                v-if="mobileDrawerOpen"
                @click="mobileDrawerOpen = false"
                class="fixed inset-0 bg-slate-950/70 backdrop-blur-sm z-40 lg:hidden"
            ></div>
        </transition>

        <!-- ================= MOBILE / TABLET DRAWER ================= -->
        <transition
            enter-active-class="transition-transform duration-300 ease-out"
            enter-from-class="-translate-x-full"
            enter-to-class="translate-x-0"
            leave-active-class="transition-transform duration-200 ease-in"
            leave-from-class="translate-x-0"
            leave-to-class="-translate-x-full"
        >
            <aside
                v-if="mobileDrawerOpen"
                class="fixed inset-y-0 left-0 z-50 w-72 bg-slate-900 text-slate-300 flex flex-col justify-between shadow-2xl lg:hidden select-none"
            >
                <div class="flex flex-col h-full overflow-hidden">
                    <!-- Brand Header Mobile -->
                    <div class="p-4 border-b border-slate-800 flex items-center justify-between shrink-0">
                        <Link :href="route('dashboard')" @click="mobileDrawerOpen = false" class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-lg bg-sky-500 flex items-center justify-center text-white font-bold text-base shadow">
                                <i class="fa-solid fa-compass-drafting"></i>
                            </div>
                            <div>
                                <h1 class="text-white font-extrabold text-xs tracking-wider leading-tight">SALSABILA CONTROL</h1>
                                <p class="text-[10px] text-sky-400 font-medium">CV. Design Salsabila</p>
                            </div>
                        </Link>
                        <button
                            @click="mobileDrawerOpen = false"
                            class="p-2 rounded-lg text-slate-400 hover:text-white hover:bg-slate-800 transition"
                            title="Tutup Menu"
                        >
                            <i class="fa-solid fa-xmark text-lg"></i>
                        </button>
                    </div>

                    <!-- Role Info Mobile -->
                    <div class="px-4 py-2.5 bg-slate-800/60 border-b border-slate-800 flex items-center justify-between text-xs shrink-0">
                        <span class="text-slate-400 text-[11px]">Mode Kendali:</span>
                        <span class="px-2 py-0.5 bg-sky-500/20 text-sky-300 rounded font-semibold text-[11px]">
                            {{ user.role || 'Team Leader' }}
                        </span>
                    </div>

                    <!-- Menu List Mobile -->
                    <nav class="p-3 space-y-1 overflow-y-auto flex-1">
                        <template v-for="item in navItems" :key="item.routeName">
                            <Link
                                :href="route().has(item.routeName) ? route(item.routeName) : '#'"
                                @click="mobileDrawerOpen = false"
                                :class="[
                                    'w-full flex items-center justify-between px-3.5 py-2.5 rounded-lg text-xs font-medium transition',
                                    isRouteActive(item.routeName)
                                        ? 'bg-sky-600 text-white font-semibold shadow-sm'
                                        : 'text-slate-300 hover:bg-slate-800 hover:text-white'
                                ]"
                            >
                                <span class="flex items-center gap-3">
                                    <i :class="['fa-solid', item.icon, 'w-4 text-center']"></i>
                                    {{ item.name }}
                                </span>
                                <span
                                    v-if="item.badgeKey && badges[item.badgeKey] > 0"
                                    :class="[
                                        'px-1.5 py-0.5 text-[10px] rounded-full font-bold',
                                        item.badgeColor || 'bg-sky-500 text-white'
                                    ]"
                                >
                                    {{ badges[item.badgeKey] }}
                                </span>
                            </Link>
                        </template>
                    </nav>

                    <!-- Footer Mobile -->
                    <div class="p-4 border-t border-slate-800 text-xs text-slate-400 shrink-0 space-y-2">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-8 rounded-full bg-sky-600 text-white flex items-center justify-center font-bold text-xs uppercase shadow">
                                    {{ (user.name || 'U').charAt(0) }}
                                </div>
                                <div class="truncate max-w-[130px]">
                                    <p class="text-white font-medium text-xs truncate">{{ user.name }}</p>
                                    <p class="text-[10px] text-slate-400 truncate">{{ user.email }}</p>
                                </div>
                            </div>
                            <Link
                                :href="route('logout')"
                                method="post"
                                as="button"
                                class="p-2 text-slate-400 hover:text-rose-400 hover:bg-slate-800 rounded-lg transition"
                                title="Keluar / Logout"
                            >
                                <i class="fa-solid fa-right-from-bracket"></i>
                            </Link>
                        </div>
                    </div>
                </div>
            </aside>
        </transition>

        <!-- ================= DESKTOP SIDEBAR ================= -->
        <aside
            :class="[
                'hidden lg:flex flex-col justify-between shrink-0 bg-slate-900 text-slate-300 border-r border-slate-800 no-print transition-all duration-300 ease-in-out',
                desktopCollapsed ? 'w-20' : 'w-64 xl:w-72'
            ]"
        >
            <div class="flex flex-col h-full overflow-hidden">
                <!-- Brand Header Desktop -->
                <div class="p-4 border-b border-slate-800 flex items-center justify-between shrink-0">
                    <Link :href="route('dashboard')" class="flex items-center gap-3 overflow-hidden">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-sky-600 to-sky-400 flex items-center justify-center text-white font-bold text-lg shadow-md shrink-0">
                            <i class="fa-solid fa-compass-drafting"></i>
                        </div>
                        <div v-show="!desktopCollapsed" class="transition-opacity duration-200">
                            <h1 class="text-white font-extrabold text-xs tracking-wider leading-tight">SALSABILA CONTROL</h1>
                            <p class="text-[10px] text-sky-400 font-medium">CV. Design Salsabila</p>
                        </div>
                    </Link>
                </div>

                <!-- Role Info Desktop -->
                <div
                    v-show="!desktopCollapsed"
                    class="px-4 py-2 bg-slate-800/50 border-b border-slate-800 flex items-center justify-between text-xs shrink-0"
                >
                    <span class="text-slate-400 text-[11px]">Mode:</span>
                    <span class="px-2 py-0.5 bg-sky-500/20 text-sky-300 rounded font-semibold text-[10px] tracking-wide">
                        {{ user.role || 'Team Leader' }}
                    </span>
                </div>

                <!-- Menu List Desktop -->
                <nav class="p-2 space-y-1 overflow-y-auto flex-1">
                    <template v-for="item in navItems" :key="item.routeName">
                        <Link
                            :href="route().has(item.routeName) ? route(item.routeName) : '#'"
                            :title="item.name"
                            :class="[
                                'w-full flex items-center rounded-xl text-xs font-medium transition relative group',
                                desktopCollapsed ? 'justify-center p-3' : 'justify-between px-3 py-2.5',
                                isRouteActive(item.routeName)
                                    ? 'bg-sky-600 text-white font-semibold shadow'
                                    : 'text-slate-300 hover:bg-slate-800/80 hover:text-white'
                            ]"
                        >
                            <span class="flex items-center gap-3">
                                <i :class="['fa-solid', item.icon, 'w-4 text-center text-sm']"></i>
                                <span v-show="!desktopCollapsed" class="truncate">{{ item.name }}</span>
                            </span>

                            <!-- Full Badge -->
                            <span
                                v-if="!desktopCollapsed && item.badgeKey && badges[item.badgeKey] > 0"
                                :class="[
                                    'px-1.5 py-0.5 text-[10px] rounded-full font-bold shadow-xs',
                                    item.badgeColor || 'bg-sky-500 text-white'
                                ]"
                            >
                                {{ badges[item.badgeKey] }}
                            </span>

                            <!-- Collapsed Tiny Dot Badge -->
                            <span
                                v-if="desktopCollapsed && item.badgeKey && badges[item.badgeKey] > 0"
                                class="absolute top-1.5 right-1.5 w-2.5 h-2.5 rounded-full bg-rose-500 ring-2 ring-slate-900"
                            ></span>

                            <!-- Tooltip when collapsed -->
                            <div
                                v-if="desktopCollapsed"
                                class="absolute left-full ml-2 px-2.5 py-1 bg-slate-950 text-white text-[11px] rounded-md whitespace-nowrap opacity-0 group-hover:opacity-100 pointer-events-none transition-opacity duration-150 shadow-lg z-50"
                            >
                                {{ item.name }}
                            </div>
                        </Link>
                    </template>
                </nav>

                <!-- Footer Desktop -->
                <div class="p-3 border-t border-slate-800 text-xs text-slate-400 shrink-0 space-y-2">
                    <div :class="['flex items-center', desktopCollapsed ? 'justify-center' : 'justify-between']">
                        <div class="flex items-center gap-2 overflow-hidden">
                            <div class="w-8 h-8 rounded-full bg-sky-600 text-white flex items-center justify-center font-bold text-xs uppercase shadow shrink-0">
                                {{ (user.name || 'U').charAt(0) }}
                            </div>
                            <div v-show="!desktopCollapsed" class="truncate max-w-[120px]">
                                <p class="text-white font-semibold text-xs truncate">{{ user.name }}</p>
                                <p class="text-[10px] text-slate-500 truncate">{{ user.email }}</p>
                            </div>
                        </div>
                        <Link
                            v-show="!desktopCollapsed"
                            :href="route('logout')"
                            method="post"
                            as="button"
                            class="p-1.5 text-slate-400 hover:text-rose-400 hover:bg-slate-800 rounded-lg transition"
                            title="Keluar / Logout"
                        >
                            <i class="fa-solid fa-right-from-bracket"></i>
                        </Link>
                    </div>

                    <div
                        v-show="!desktopCollapsed"
                        class="flex items-center justify-between pt-1 border-t border-slate-800/80 text-[10px] text-slate-500"
                    >
                        <span class="flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                            Online
                        </span>
                        <span>Asia/Jakarta</span>
                    </div>
                </div>
            </div>
        </aside>

        <!-- ================= MAIN CONTENT WRAPPER ================= -->
        <div class="flex-1 flex flex-col h-screen overflow-hidden min-w-0">
            <!-- TOP HEADER -->
            <header class="bg-white border-b border-slate-200 px-4 sm:px-6 py-3 flex items-center justify-between gap-3 shrink-0 no-print z-20 shadow-xs">
                <!-- Left: Hamburger + Title -->
                <div class="flex items-center gap-2.5 sm:gap-3.5 min-w-0">
                    <button
                        @click="toggleSidebar"
                        class="p-2 sm:p-2.5 rounded-xl text-slate-600 hover:text-slate-900 hover:bg-slate-100 flex items-center justify-center transition border border-slate-200/80 active:scale-95 shrink-0"
                        title="Buka / Tutup Navigasi (Hamburger)"
                    >
                        <i class="fa-solid fa-bars text-sm sm:text-base"></i>
                    </button>

                    <div class="min-w-0">
                        <h2 class="text-sm sm:text-base md:text-lg font-bold text-slate-800 truncate leading-tight">{{ title }}</h2>
                        <p class="text-[10px] sm:text-xs text-slate-500 truncate hidden xs:block">{{ subtitle }}</p>
                    </div>
                </div>

                <!-- Right: Search + Quick Action + Live Clock -->
                <div class="flex items-center gap-2 sm:gap-3 shrink-0">
                    <!-- Global Search (Tablet & Desktop) -->
                    <div class="relative hidden md:block w-44 lg:w-56">
                        <i class="fa-solid fa-magnifying-glass absolute left-3 top-2.5 text-slate-400 text-xs"></i>
                        <input
                            v-model="searchQuery"
                            @input="onSearchInput"
                            type="text"
                            placeholder="Cari tugas, PIC..."
                            class="w-full pl-8 pr-3 py-1.5 bg-slate-100 border border-slate-200 rounded-lg text-xs focus:outline-none focus:bg-white focus:border-sky-500 transition"
                        />
                    </div>

                    <!-- Quick Action Button "Tugas Baru" -->
                    <slot name="header-actions">
                        <button
                            @click="$emit('open-task-modal')"
                            class="px-3 py-1.5 sm:px-3.5 sm:py-2 bg-sky-600 hover:bg-sky-700 active:scale-95 text-white rounded-lg text-xs font-semibold flex items-center gap-1.5 shadow-sm transition"
                            title="Tambah Tugas Baru"
                        >
                            <i class="fa-solid fa-plus text-xs"></i>
                            <span class="inline">Tugas Baru</span>
                        </button>
                    </slot>

                    <!-- Date & Time WIB -->
                    <div class="text-right border-l border-slate-200 pl-2.5 sm:pl-3.5 hidden sm:block">
                        <div class="text-[11px] sm:text-xs font-bold text-slate-700 whitespace-nowrap">{{ liveDate }}</div>
                        <div class="text-[10px] text-sky-600 font-mono font-semibold">{{ liveTime }}</div>
                    </div>

                    <!-- Logout Button for Mobile -->
                    <div class="lg:hidden pl-1 border-l border-slate-200">
                        <Link
                            :href="route('logout')"
                            method="post"
                            as="button"
                            class="p-1.5 text-slate-400 hover:text-rose-500 rounded-lg transition"
                            title="Logout"
                        >
                            <i class="fa-solid fa-right-from-bracket text-sm"></i>
                        </Link>
                    </div>
                </div>
            </header>

            <!-- MAIN CONTENT AREA -->
            <main class="flex-1 overflow-y-auto p-3 sm:p-5 md:p-6 bg-slate-50/70" id="main-content">
                <slot />
            </main>
        </div>

        <!-- Global Modals Slot -->
        <slot name="modals" />
    </div>
</template>
