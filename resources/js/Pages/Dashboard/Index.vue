<script setup>
import { ref } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import StatusBadge from '@/Components/Common/StatusBadge.vue';
import PriorityBadge from '@/Components/Common/PriorityBadge.vue';
import ProgressBar from '@/Components/Common/ProgressBar.vue';
import TaskModal from '@/Components/Modals/TaskModal.vue';

const props = defineProps({
    stats: Object,
    warnings: Array,
    attentionTasks: Array,
    projects: Array,
    users: Array,
    divisions: Array,
    picWorkload: Array,
    sidebarBadges: Object,
});

const showTaskModal = ref(false);

function formatRupiah(num) {
    return 'Rp ' + Number(num || 0).toLocaleString('id-ID');
}

function formatDateID(dateStr) {
    if (!dateStr) return 'Tanpa Deadline';
    const d = new Date(dateStr + 'T00:00:00');
    return d.toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' });
}
</script>

<template>
    <AppLayout
        title="Dashboard Utama"
        subtitle="Ringkasan Eksekutif & Sistem Peringatan Dini CV. Design Salsabila"
        :badges="sidebarBadges"
        @open-task-modal="showTaskModal = true"
    >
        <Head title="Dashboard Utama — Salsabila Control Center" />

        <div class="space-y-6 max-w-7xl mx-auto pb-8">
            <!-- ================= BANNER SUMMARY & QUICK ACTION ================= -->
            <div class="bg-gradient-to-r from-slate-900 via-slate-800 to-sky-950 rounded-2xl p-5 sm:p-6 text-white shadow-md flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
                <div class="space-y-1">
                    <div class="flex items-center gap-2">
                        <span class="px-2.5 py-0.5 rounded-full bg-sky-500/20 text-sky-300 text-[11px] font-semibold border border-sky-400/30">
                            Sistem Kontrol Terpadu
                        </span>
                        <span class="text-xs text-slate-400">Team Leader Overview</span>
                    </div>
                    <h2 class="text-lg sm:text-xl font-bold tracking-tight">Panel Pengendali Proyek & Kualitas Teknis</h2>
                    <p class="text-xs sm:text-sm text-slate-300 max-w-2xl">
                        Pantau progres berbobot seluruh portofolio perencanaan, periksa standar QC, dan tangani kendala kritis secara real-time.
                    </p>
                </div>
                <div class="flex flex-wrap items-center gap-2.5 shrink-0 w-full sm:w-auto">
                    <button
                        @click="showTaskModal = true"
                        class="w-full sm:w-auto px-4 py-2.5 bg-sky-500 hover:bg-sky-400 text-white rounded-xl text-xs font-bold flex items-center justify-center gap-2 shadow-sm transition active:scale-95"
                    >
                        <i class="fa-solid fa-plus-circle text-sm"></i>
                        <span>Tambah Tugas Baru</span>
                    </button>
                    <Link
                        :href="route('today.index')"
                        class="w-full sm:w-auto px-4 py-2.5 bg-slate-800 hover:bg-slate-700 text-slate-200 rounded-xl text-xs font-semibold flex items-center justify-center gap-2 border border-slate-700 transition"
                    >
                        <i class="fa-solid fa-calendar-day text-amber-400"></i>
                        <span>Agenda Hari Ini</span>
                    </Link>
                </div>
            </div>

            <!-- ================= 8 STAT CARDS (RAPI & TERKELOMPOK) ================= -->
            <div class="space-y-3">
                <!-- Group 1: Status Operasional & Teknis -->
                <div>
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-500 mb-2.5 flex items-center gap-1.5">
                        <i class="fa-solid fa-layer-group text-sky-500"></i> Ringkasan Operasional Proyek
                    </h3>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-3 sm:gap-4">
                        <!-- Proyek Aktif -->
                        <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs hover:shadow-md transition">
                            <div class="flex items-center justify-between">
                                <span class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Proyek Aktif</span>
                                <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center text-sm">
                                    <i class="fa-solid fa-folder-open"></i>
                                </div>
                            </div>
                            <div class="text-2xl sm:text-3xl font-extrabold text-slate-800 mt-2">{{ stats.active_projects }}</div>
                            <span class="text-[11px] text-emerald-600 font-medium mt-1 flex items-center gap-1">
                                <i class="fa-solid fa-circle-check text-[10px]"></i> Sedang berjalan
                            </span>
                        </div>

                        <!-- Tugas Berjalan -->
                        <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs hover:shadow-md transition">
                            <div class="flex items-center justify-between">
                                <span class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Tugas Aktif</span>
                                <div class="w-8 h-8 rounded-lg bg-sky-50 text-sky-600 flex items-center justify-center text-sm">
                                    <i class="fa-solid fa-list-check"></i>
                                </div>
                            </div>
                            <div class="text-2xl sm:text-3xl font-extrabold text-sky-600 mt-2">{{ stats.active_tasks }}</div>
                            <Link :href="route('tasks.index')" class="text-[11px] text-sky-600 hover:underline font-medium mt-1 inline-flex items-center gap-1">
                                Kelola To-Do &rarr;
                            </Link>
                        </div>

                        <!-- Menunggu QC -->
                        <div class="bg-white p-4 rounded-2xl border border-purple-200 bg-purple-50/20 shadow-xs hover:shadow-md transition">
                            <div class="flex items-center justify-between">
                                <span class="text-[11px] font-semibold text-purple-800 uppercase tracking-wider">Menunggu QC</span>
                                <div class="w-8 h-8 rounded-lg bg-purple-100 text-purple-700 flex items-center justify-center text-sm">
                                    <i class="fa-solid fa-clipboard-check"></i>
                                </div>
                            </div>
                            <div class="text-2xl sm:text-3xl font-extrabold text-purple-700 mt-2">{{ stats.waiting_qc }}</div>
                            <Link :href="route('review.index')" class="text-[11px] text-purple-700 font-bold hover:underline mt-1 inline-flex items-center gap-1">
                                Buka Meja QC &rarr;
                            </Link>
                        </div>

                        <!-- Proyek Terhambat -->
                        <div class="bg-white p-4 rounded-2xl border border-rose-200 bg-rose-50/20 shadow-xs hover:shadow-md transition">
                            <div class="flex items-center justify-between">
                                <span class="text-[11px] font-semibold text-rose-800 uppercase tracking-wider">Terhambat</span>
                                <div class="w-8 h-8 rounded-lg bg-rose-100 text-rose-600 flex items-center justify-center text-sm">
                                    <i class="fa-solid fa-triangle-exclamation"></i>
                                </div>
                            </div>
                            <div class="text-2xl sm:text-3xl font-extrabold text-rose-600 mt-2">{{ stats.delayed_projects }}</div>
                            <span class="text-[11px] text-rose-600 font-semibold mt-1">Perlu Intervensi</span>
                        </div>
                    </div>
                </div>

                <!-- Group 2: Prioritas, Finansial & Tim -->
                <div>
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-500 mb-2.5 flex items-center gap-1.5">
                        <i class="fa-solid fa-clock-rotate-left text-amber-500"></i> Indikator Batas Waktu & Finansial
                    </h3>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-3 sm:gap-4">
                        <!-- Terlambat (Overdue) -->
                        <div class="bg-white p-4 rounded-2xl border border-rose-200 bg-rose-50/30 shadow-xs hover:shadow-md transition">
                            <div class="flex items-center justify-between">
                                <span class="text-[11px] font-semibold text-rose-800 uppercase tracking-wider">Terlambat</span>
                                <div class="w-8 h-8 rounded-lg bg-rose-100 text-rose-700 flex items-center justify-center text-sm">
                                    <i class="fa-solid fa-circle-exclamation"></i>
                                </div>
                            </div>
                            <div class="text-2xl sm:text-3xl font-extrabold text-rose-700 mt-2">{{ stats.overdue_tasks }}</div>
                            <span class="text-[11px] text-rose-600 font-bold mt-1">Lewat Deadline</span>
                        </div>

                        <!-- Deadline Hari Ini -->
                        <div class="bg-white p-4 rounded-2xl border border-amber-200 bg-amber-50/30 shadow-xs hover:shadow-md transition">
                            <div class="flex items-center justify-between">
                                <span class="text-[11px] font-semibold text-amber-800 uppercase tracking-wider">Hari Ini</span>
                                <div class="w-8 h-8 rounded-lg bg-amber-100 text-amber-700 flex items-center justify-center text-sm">
                                    <i class="fa-solid fa-calendar-day"></i>
                                </div>
                            </div>
                            <div class="text-2xl sm:text-3xl font-extrabold text-amber-600 mt-2">{{ stats.due_today_tasks }}</div>
                            <Link :href="route('today.index')" class="text-[11px] text-amber-800 font-bold hover:underline mt-1 inline-flex items-center gap-1">
                                Cek Hari Ini &rarr;
                            </Link>
                        </div>

                        <!-- Piutang Termin -->
                        <div class="bg-white p-4 rounded-2xl border border-emerald-200 bg-emerald-50/20 shadow-xs hover:shadow-md transition">
                            <div class="flex items-center justify-between">
                                <span class="text-[11px] font-semibold text-emerald-800 uppercase tracking-wider">Piutang Termin</span>
                                <div class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center text-sm">
                                    <i class="fa-solid fa-file-invoice-dollar"></i>
                                </div>
                            </div>
                            <div class="text-base sm:text-lg font-black text-emerald-700 mt-2 truncate" :title="formatRupiah(stats.total_receivables)">
                                {{ formatRupiah(stats.total_receivables) }}
                            </div>
                            <Link :href="route('billing.index')" class="text-[11px] text-emerald-700 font-bold hover:underline mt-1 inline-flex items-center gap-1">
                                Rincian Invoice &rarr;
                            </Link>
                        </div>

                        <!-- Anggota Tim -->
                        <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs hover:shadow-md transition">
                            <div class="flex items-center justify-between">
                                <span class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Personel Tim</span>
                                <div class="w-8 h-8 rounded-lg bg-slate-100 text-slate-700 flex items-center justify-center text-sm">
                                    <i class="fa-solid fa-users"></i>
                                </div>
                            </div>
                            <div class="text-2xl sm:text-3xl font-extrabold text-slate-800 mt-2">{{ stats.total_members }}</div>
                            <Link :href="route('team.index')" class="text-[11px] text-sky-600 font-semibold hover:underline mt-1 inline-flex items-center gap-1">
                                Pantau Tim &rarr;
                            </Link>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ================= RADAR PERINGATAN OTOMATIS ================= -->
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-4 sm:p-5">
                <div class="flex flex-wrap items-center justify-between gap-2 mb-3">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-700 flex items-center gap-2">
                        <i class="fa-solid fa-satellite-dish text-sky-600 animate-pulse"></i> Radar Peringatan & Deteksi Dini
                    </h3>
                    <span class="text-[11px] text-slate-400">Sinkron otomatis dengan status database</span>
                </div>

                <div v-if="warnings.length > 0" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2.5">
                    <Link
                        v-for="(w, idx) in warnings"
                        :key="idx"
                        :href="route(w.link)"
                        class="p-3 rounded-xl border transition hover:shadow-sm flex items-start gap-3 group"
                        :class="[
                            w.type === 'danger' ? 'bg-rose-50/70 border-rose-200 text-rose-800 hover:bg-rose-100/80' :
                            w.type === 'warning' ? 'bg-amber-50/70 border-amber-200 text-amber-800 hover:bg-amber-100/80' :
                            'bg-purple-50/70 border-purple-200 text-purple-800 hover:bg-purple-100/80'
                        ]"
                    >
                        <div
                            class="w-8 h-8 rounded-lg flex items-center justify-center shrink-0 text-sm mt-0.5"
                            :class="[
                                w.type === 'danger' ? 'bg-rose-200 text-rose-800' :
                                w.type === 'warning' ? 'bg-amber-200 text-amber-800' :
                                'bg-purple-200 text-purple-800'
                            ]"
                        >
                            <i :class="['fa-solid', w.icon]"></i>
                        </div>
                        <div class="min-w-0 flex-1">
                            <h4 class="text-xs font-bold leading-tight group-hover:underline">{{ w.title }}</h4>
                            <p class="text-[11px] text-slate-600 mt-0.5 leading-snug">{{ w.desc }}</p>
                        </div>
                        <i class="fa-solid fa-chevron-right text-xs opacity-40 group-hover:opacity-100 group-hover:translate-x-0.5 transition shrink-0 self-center"></i>
                    </Link>
                </div>
                <div v-else class="text-xs text-emerald-700 bg-emerald-50/60 border border-emerald-200 rounded-xl p-3 flex items-center gap-2">
                    <i class="fa-solid fa-circle-check text-emerald-600 text-sm"></i>
                    <span>Semua target aman. Tidak ada indikator kritis yang memerlukan tindakan darurat.</span>
                </div>
            </div>

            <!-- ================= PERHATIAN TEAM LEADER (ACTION GRID) ================= -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="bg-slate-900 text-white px-5 py-3.5 flex flex-wrap items-center justify-between gap-3">
                    <div class="flex items-center gap-2.5">
                        <span class="px-2 py-0.5 bg-rose-500 text-white rounded text-[10px] font-bold tracking-wider uppercase">
                            Prioritas Kendali
                        </span>
                        <h3 class="text-sm font-bold tracking-wide">PERHATIAN TEAM LEADER</h3>
                    </div>
                    <div class="flex items-center gap-2">
                        <button
                            @click="showTaskModal = true"
                            class="px-2.5 py-1 bg-sky-600 hover:bg-sky-500 text-white rounded-lg text-xs font-semibold flex items-center gap-1.5 transition"
                        >
                            <i class="fa-solid fa-plus text-[10px]"></i> Tambah Tugas
                        </button>
                    </div>
                </div>

                <div class="p-4 sm:p-5">
                    <div v-if="attentionTasks.length > 0" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div
                            v-for="t in attentionTasks"
                            :key="t.id"
                            class="p-4 rounded-xl border border-slate-200 bg-slate-50/50 hover:bg-white hover:border-slate-300 hover:shadow-md transition space-y-3 flex flex-col justify-between"
                        >
                            <div class="space-y-2">
                                <div class="flex items-start justify-between gap-2">
                                    <div class="min-w-0">
                                        <div class="flex flex-wrap items-center gap-1.5 mb-1">
                                            <span class="px-1.5 py-0.5 bg-slate-800 text-white rounded text-[10px] font-mono font-bold">{{ t.code || 'T' }}</span>
                                            <span class="text-[11px] font-medium text-slate-500 truncate max-w-[200px]">{{ t.project?.name || 'Proyek Umum' }}</span>
                                        </div>
                                        <h4 class="text-xs sm:text-sm font-bold text-slate-900 leading-snug">{{ t.name }}</h4>
                                    </div>
                                    <div class="flex flex-col items-end gap-1 shrink-0">
                                        <StatusBadge :status="t.status" />
                                        <PriorityBadge :priority="t.priority" />
                                    </div>
                                </div>

                                <!-- Progress Bar -->
                                <div class="pt-1">
                                    <ProgressBar :progress="t.computed_progress" />
                                </div>

                                <!-- PIC & Deadline Grid -->
                                <div class="grid grid-cols-2 gap-2 text-xs pt-1 border-t border-slate-200/60">
                                    <div>
                                        <span class="text-slate-500 text-[10px] block">PIC Utama:</span>
                                        <span class="font-semibold text-slate-800 truncate block">{{ t.pic?.name || 'Belum Ditentukan' }}</span>
                                    </div>
                                    <div>
                                        <span class="text-slate-500 text-[10px] block">Batas Waktu:</span>
                                        <span :class="[t.is_overdue ? 'text-rose-600 font-bold' : 'text-slate-800 font-semibold']">
                                            {{ formatDateID(t.deadline) }}
                                        </span>
                                    </div>
                                </div>

                                <!-- Kendala & Tindakan -->
                                <div v-if="t.obstacle || t.next_action" class="bg-white p-2.5 rounded-lg border border-slate-200 text-xs space-y-1">
                                    <div v-if="t.obstacle" class="text-rose-700 text-[11px]">
                                        <span class="font-bold">Kendala:</span> {{ t.obstacle }}
                                    </div>
                                    <div v-if="t.next_action" class="text-slate-700 text-[11px]">
                                        <span class="font-bold">Tindak Lanjut:</span> {{ t.next_action }}
                                    </div>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="flex flex-wrap items-center justify-end gap-2 pt-2 border-t border-slate-200/60">
                                <Link
                                    v-if="t.status === 'Menunggu Pemeriksaan'"
                                    :href="route('review.index')"
                                    class="px-3 py-1.5 bg-purple-600 hover:bg-purple-700 text-white rounded-lg text-xs font-semibold flex items-center gap-1.5 transition"
                                >
                                    <i class="fa-solid fa-clipboard-check"></i> Meja QC
                                </Link>
                                <Link
                                    :href="route('tasks.index')"
                                    class="px-3 py-1.5 bg-slate-800 hover:bg-slate-700 text-white rounded-lg text-xs font-semibold flex items-center gap-1.5 transition"
                                >
                                    <i class="fa-solid fa-arrow-up-right-from-square"></i> Buka Pekerjaan
                                </Link>
                            </div>
                        </div>
                    </div>

                    <div v-else class="py-10 text-center text-slate-400 text-xs">
                        <i class="fa-solid fa-shield-halved text-4xl text-emerald-500 mb-2 block"></i>
                        <p class="font-medium text-slate-600">Tidak ada pekerjaan yang membutuhkan intervensi darurat.</p>
                        <p class="text-[11px] text-slate-400 mt-0.5">Semua tugas berjalan sesuai jadwal dan standar kendali.</p>
                    </div>
                </div>
            </div>

            <!-- ================= GRAFIK PROGRESS PROYEK & BEBAN KERJA PIC ================= -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Progres Berbobot Proyek -->
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4 sm:p-5 space-y-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-xs sm:text-sm font-bold text-slate-800 flex items-center gap-2">
                                <i class="fa-solid fa-chart-line text-sky-500"></i> Progres Berbobot Portofolio
                            </h3>
                            <p class="text-[11px] text-slate-500">Realisasi berdasarkan bobot tugas selesai</p>
                        </div>
                        <Link :href="route('projects.index')" class="text-xs text-sky-600 font-semibold hover:underline">
                            Detail &rarr;
                        </Link>
                    </div>

                    <div class="space-y-3">
                        <div
                            v-for="p in projects"
                            :key="p.id"
                            class="p-3.5 rounded-xl border border-slate-200/80 hover:border-slate-300 transition space-y-2 bg-slate-50/40"
                        >
                            <div class="flex items-center justify-between gap-2">
                                <div class="min-w-0">
                                    <div class="flex items-center gap-2">
                                        <span class="px-1.5 py-0.5 bg-slate-900 text-white rounded text-[10px] font-mono font-bold">{{ p.code || 'P' }}</span>
                                        <h4 class="text-xs font-bold text-slate-900 truncate">{{ p.name }}</h4>
                                    </div>
                                    <p class="text-[10px] text-slate-500 mt-0.5 truncate">Klien: {{ p.client }} &bull; PIC: {{ p.pic?.name || '-' }}</p>
                                </div>
                                <StatusBadge :status="p.status" />
                            </div>

                            <div class="space-y-1">
                                <div class="flex justify-between text-[11px] font-semibold">
                                    <span class="text-slate-600">Realisasi: {{ p.weighted_progress }}%</span>
                                    <span class="text-sky-600">Target: {{ p.target_progress }}%</span>
                                </div>
                                <div class="w-full h-2.5 bg-slate-200/80 rounded-full overflow-hidden flex">
                                    <div
                                        class="h-full bg-emerald-500 rounded-full transition-all duration-500"
                                        :style="{ width: `${p.weighted_progress}%` }"
                                    ></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Distribusi Beban Kerja PIC -->
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4 sm:p-5 space-y-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-xs sm:text-sm font-bold text-slate-800 flex items-center gap-2">
                                <i class="fa-solid fa-users-gear text-sky-500"></i> Beban Kerja & Kapasitas Tim
                            </h3>
                            <p class="text-[11px] text-slate-500">Distribusi tugas aktif per personel</p>
                        </div>
                        <Link :href="route('team.index')" class="text-xs text-sky-600 font-semibold hover:underline">
                            Kelola Tim &rarr;
                        </Link>
                    </div>

                    <div class="space-y-2.5">
                        <div
                            v-for="pic in picWorkload"
                            :key="pic.id"
                            class="p-3 rounded-xl border border-slate-100 bg-slate-50/50 flex flex-wrap items-center justify-between gap-3 hover:bg-slate-100/60 transition"
                        >
                            <div class="min-w-0">
                                <div class="flex items-center gap-2">
                                    <span class="w-2 h-2 rounded-full" :class="pic.active_tasks > 0 ? 'bg-emerald-500' : 'bg-slate-300'"></span>
                                    <h4 class="text-xs font-bold text-slate-800 truncate">{{ pic.name }}</h4>
                                </div>
                                <p class="text-[10px] text-slate-500 mt-0.5 truncate">{{ pic.role }} &bull; Divisi {{ pic.divisi }}</p>
                            </div>

                            <div class="flex items-center gap-1.5 text-xs font-semibold shrink-0">
                                <span class="px-2 py-0.5 rounded bg-sky-100 text-sky-800 text-[10px] font-bold">
                                    {{ pic.active_tasks }} Aktif
                                </span>
                                <span class="px-2 py-0.5 rounded bg-emerald-100 text-emerald-800 text-[10px] font-bold">
                                    {{ pic.done_tasks }} Selesai
                                </span>
                                <span v-if="pic.overdue_tasks > 0" class="px-2 py-0.5 rounded bg-rose-100 text-rose-800 text-[10px] font-bold">
                                    {{ pic.overdue_tasks }} Overdue
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ================= MODAL TAMBAH TUGAS ================= -->
        <template #modals>
            <TaskModal
                :show="showTaskModal"
                :projects="projects"
                :users="users"
                :divisions="divisions"
                @close="showTaskModal = false"
            />
        </template>
    </AppLayout>
</template>
