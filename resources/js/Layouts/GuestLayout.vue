<script setup>
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import { Link } from '@inertiajs/vue3';

defineProps({
    title: {
        type: String,
        default: '',
    },
    subtitle: {
        type: String,
        default: '',
    },
    badge: {
        type: String,
        default: '',
    },
    badgeIcon: {
        type: String,
        default: 'fa-shield-halved',
    },
});

const currentYear = new Date().getFullYear();
</script>

<template>
    <div class="min-h-screen bg-slate-950 text-slate-100 flex flex-col justify-center items-center p-4 sm:p-6 py-12 relative overflow-x-hidden font-sans selection:bg-sky-500 selection:text-white">
        <!-- Ambient Background Glows & Blueprint Grid -->
        <div class="absolute -top-32 -left-32 w-80 h-80 bg-sky-500/15 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute -bottom-32 -right-32 w-80 h-80 bg-indigo-500/10 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[550px] h-[550px] bg-sky-600/10 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute inset-0 bg-[linear-gradient(to_right,#0284c70d_1px,transparent_1px),linear-gradient(to_bottom,#0284c70d_1px,transparent_1px)] bg-[size:32px_32px] pointer-events-none"></div>

        <!-- Main Centered Content Container -->
        <div class="w-full max-w-md relative z-10 flex flex-col items-center">
            <!-- Brand Header: Logo & Company Name Directly Above the Card -->
            <div class="mb-6 flex flex-col items-center text-center">
                <Link href="/" class="group flex flex-col items-center">
                    <ApplicationLogo class="h-16 w-16 shadow-xl shadow-sky-500/25 transition-transform duration-200 group-hover:scale-105" />
                    <h1 class="mt-3.5 text-lg sm:text-xl font-extrabold tracking-widest text-white leading-tight">
                        SALSABILA CONTROL
                    </h1>
                    <p class="mt-1 text-xs sm:text-sm font-semibold tracking-wider text-sky-400 uppercase">
                        CV. Design Salsabila
                    </p>
                </Link>
            </div>

            <!-- Card Container -->
            <div class="w-full bg-white rounded-2xl sm:rounded-3xl shadow-2xl border border-slate-200/90 overflow-hidden relative text-slate-800">
                <!-- Top Gradient Stripe -->
                <div class="h-1.5 bg-gradient-to-r from-sky-400 via-sky-500 to-indigo-600"></div>

                <!-- Inner Card Body -->
                <div class="p-6 sm:p-8">
                    <!-- Optional Header / Title inside Card -->
                    <div v-if="title || badge || $slots.header" class="mb-6 text-center">
                        <slot name="header">
                            <div v-if="badge" class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-sky-50 border border-sky-100 text-sky-700 text-[11px] font-semibold mb-2">
                                <i :class="['fa-solid', badgeIcon]"></i>
                                <span>{{ badge }}</span>
                            </div>
                            <h2 v-if="title" class="text-xl sm:text-2xl font-bold text-slate-900 tracking-tight">
                                {{ title }}
                            </h2>
                            <p v-if="subtitle" class="text-xs sm:text-sm text-slate-500 mt-1 leading-relaxed">
                                {{ subtitle }}
                            </p>
                        </slot>
                    </div>

                    <!-- Form / Content Slot -->
                    <slot />
                </div>

                <!-- Card Security Footer -->
                <div class="px-6 py-3 bg-slate-50 border-t border-slate-100 flex items-center justify-between text-[11px] text-slate-500">
                    <span class="inline-flex items-center gap-1.5">
                        <i class="fa-solid fa-shield-halved text-sky-600 text-[10px]"></i>
                        Autentikasi Terenkripsi
                    </span>
                    <span>CV. Design Salsabila</span>
                </div>
            </div>

            <!-- Bottom Copyright -->
            <div class="mt-6 text-center text-xs text-slate-500">
                © {{ currentYear }} CV. Design Salsabila. Seluruh hak cipta dilindungi.
            </div>
        </div>
    </div>
</template>
