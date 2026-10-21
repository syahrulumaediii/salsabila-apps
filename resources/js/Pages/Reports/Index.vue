<script setup>
import { reactive } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import StatusBadge from '@/Components/Common/StatusBadge.vue';
import PriorityBadge from '@/Components/Common/PriorityBadge.vue';

const props = defineProps({
    tasks: Array,
    projects: Array,
    users: Array,
    filters: Object,
    summary: Object,
    sidebarBadges: Object,
});

const filterForm = reactive({
    period: props.filters?.period || 'ALL',
    project_id: props.filters?.project_id || 'ALL',
    pic_user_id: props.filters?.pic_user_id || 'ALL',
});

function applyFilters() {
    router.get(route('reports.index'), filterForm, { preserveState: true, preserveScroll: true });
}

function printReport() {
    window.print();
}

function exportCsv() {
    window.location.href = route('reports.csv');
}

function formatRupiah(num) {
    return 'Rp ' + Number(num || 0).toLocaleString('id-ID');
}

function formatDateID(dateStr) {
    if (!dateStr) return '-';
    const d = new Date(dateStr + 'T00:00:00');
    return d.toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' });
}

const currentDateFormatted = new Date().toLocaleDateString('id-ID', {
    day: 'numeric',
    month: 'long',
    year: 'numeric',
});
</script>

<template>
    <AppLayout
        title="Laporan & Ekspor Data"
        subtitle="Laporan Eksekutif Team Leader, Ekspor CSV/Excel, dan Cetak Dokumen PDF"
        :badges="sidebarBadges"
    >
        <template #header-actions>
            <div class="flex items-center gap-2">
                <button
                    @click="printReport"
                    class="px-3.5 py-2 bg-slate-900 hover:bg-slate-800 text-white rounded-lg text-xs font-semibold flex items-center gap-1.5 shadow-sm transition active:scale-95"
                >
                    <i class="fa-solid fa-print text-xs"></i>
                    <span class="hidden sm:inline">Cetak PDF</span>
                </button>
                <button
                    @click="exportCsv"
                    class="px-3.5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-xs font-semibold flex items-center gap-1.5 shadow-sm transition active:scale-95"
                >
                    <i class="fa-solid fa-file-csv text-xs"></i>
                    <span class="hidden sm:inline">Ekspor CSV</span>
                </button>
            </div>
        </template>

        <Head title="Laporan & Ekspor Data — Salsabila Control Center" />

        <div class="space-y-6">
            <!-- Filter & Action Panel (Hidden when printing) -->
            <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm no-print space-y-4">
                <div class="flex flex-wrap items-center justify-between gap-4">
                    <div>
                        <h3 class="text-sm font-bold text-slate-800">Generator Laporan Eksekutif Team Leader</h3>
                        <p class="text-xs text-slate-500">Filter berdasarkan periode waktu, proyek, dan PIC penanggung jawab</p>
                    </div>
                    <div class="flex flex-wrap items-center gap-2">
                        <button
                            @click="printReport"
                            class="px-3.5 py-2 bg-slate-900 hover:bg-slate-800 text-white rounded-lg text-xs font-semibold flex items-center gap-2 transition shadow-sm"
                        >
                            <i class="fa-solid fa-print"></i> Cetak Laporan / PDF
                        </button>
                        <button
                            @click="exportCsv"
                            class="px-3.5 py-2 bg-sky-600 hover:bg-sky-700 text-white rounded-lg text-xs font-semibold flex items-center gap-2 transition shadow-sm"
                        >
                            <i class="fa-solid fa-file-csv"></i> Ekspor CSV Pekerjaan
                        </button>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-3 pt-3 border-t border-slate-100">
                    <div>
                        <label class="text-[11px] font-semibold text-slate-600 block mb-1">Periode Laporan</label>
                        <select
                            v-model="filterForm.period"
                            @change="applyFilters"
                            class="w-full px-3 py-1.5 border border-slate-300 rounded-lg text-xs bg-white"
                        >
                            <option value="ALL">Seluruh Periode Aktif</option>
                            <option value="DAILY">Laporan Harian (Hari Ini)</option>
                            <option value="WEEKLY">Laporan Mingguan (±7 Hari)</option>
                            <option value="MONTHLY">Laporan Bulanan (Bulan Ini)</option>
                        </select>
                    </div>

                    <div>
                        <label class="text-[11px] font-semibold text-slate-600 block mb-1">Filter Proyek</label>
                        <select
                            v-model="filterForm.project_id"
                            @change="applyFilters"
                            class="w-full px-3 py-1.5 border border-slate-300 rounded-lg text-xs bg-white"
                        >
                            <option value="ALL">Seluruh Proyek</option>
                            <option v-for="p in projects" :key="p.id" :value="p.id">{{ p.name }}</option>
                        </select>
                    </div>

                    <div>
                        <label class="text-[11px] font-semibold text-slate-600 block mb-1">Filter PIC</label>
                        <select
                            v-model="filterForm.pic_user_id"
                            @change="applyFilters"
                            class="w-full px-3 py-1.5 border border-slate-300 rounded-lg text-xs bg-white"
                        >
                            <option value="ALL">Seluruh PIC</option>
                            <option v-for="u in users" :key="u.id" :value="u.id">{{ u.name }}</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Area Dokumen Laporan yang Siap Dicetak (Printable Report Area) -->
            <div id="printable-report-area" class="bg-white p-8 rounded-xl border border-slate-200 shadow-sm space-y-6">
                <!-- KOP Laporan -->
                <div class="flex items-center justify-between pb-4 border-b-2 border-slate-900">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 rounded-lg bg-slate-900 text-white flex items-center justify-center font-bold text-xl">
                            <i class="fa-solid fa-compass-drafting"></i>
                        </div>
                        <div>
                            <h2 class="text-base font-extrabold text-slate-900 tracking-wide uppercase">CV. DESIGN SALSABILA</h2>
                            <p class="text-xs text-slate-600">Konsultan Perencana, Arsitektur, Struktur, MEP & Pengawasan Konstruksi</p>
                            <p class="text-[11px] text-slate-400">Jl. Pangeran Drajat No. 42, Cirebon &bull; Telp: (0231) 882910</p>
                        </div>
                    </div>
                    <div class="text-right text-xs">
                        <span class="px-2 py-0.5 rounded bg-slate-100 text-slate-800 font-mono text-[10px] font-bold border border-slate-300">
                            DOC-SCC-{{ new Date().getFullYear() }}
                        </span>
                        <p class="text-slate-500 mt-1">Tanggal Cetak: {{ currentDateFormatted }}</p>
                        <p class="text-slate-500 font-medium">Zona Waktu: WIB (Asia/Jakarta)</p>
                    </div>
                </div>

                <!-- Judul Dokumen -->
                <div class="text-center py-2">
                    <h3 class="text-base font-extrabold text-slate-900 uppercase tracking-wider">
                        LAPORAN KONTROL & PROGRES PEKERJAAN PROYEK
                    </h3>
                    <p class="text-xs text-slate-500">
                        Periode: {{ filterForm.period === 'ALL' ? 'Semua Periode' : filterForm.period }} &bull; Sistem Kendali Terpadu Team Leader
                    </p>
                </div>

                <!-- Executive Summary Cards -->
                <div class="grid grid-cols-4 gap-3 text-xs">
                    <div class="p-3 rounded-lg border border-slate-200 bg-slate-50 text-center">
                        <span class="text-[10px] text-slate-500 font-semibold block uppercase">Total Proyek</span>
                        <span class="text-base font-black text-slate-800">{{ summary.total_projects }} Proyek</span>
                    </div>
                    <div class="p-3 rounded-lg border border-slate-200 bg-slate-50 text-center">
                        <span class="text-[10px] text-slate-500 font-semibold block uppercase">Total Pekerjaan</span>
                        <span class="text-base font-black text-slate-800">{{ summary.total_tasks }} Pekerjaan</span>
                    </div>
                    <div class="p-3 rounded-lg border border-slate-200 bg-slate-50 text-center">
                        <span class="text-[10px] text-slate-500 font-semibold block uppercase">Pekerjaan Selesai</span>
                        <span class="text-base font-black text-emerald-700">{{ summary.completed_tasks }} Tuntas</span>
                    </div>
                    <div class="p-3 rounded-lg border border-slate-200 bg-slate-50 text-center">
                        <span class="text-[10px] text-slate-500 font-semibold block uppercase">Sisa Piutang</span>
                        <span class="text-sm font-black text-rose-700 truncate block">{{ formatRupiah(summary.total_receivable) }}</span>
                    </div>
                </div>

                <!-- Tabel Detail Laporan -->
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-xs border border-slate-300">
                        <thead>
                            <tr class="bg-slate-100 text-slate-900 uppercase text-[10px] font-bold border-b border-slate-300">
                                <th class="p-2.5 border-r border-slate-300">No</th>
                                <th class="p-2.5 border-r border-slate-300">Proyek</th>
                                <th class="p-2.5 border-r border-slate-300">Nama Pekerjaan</th>
                                <th class="p-2.5 border-r border-slate-300">Tahapan</th>
                                <th class="p-2.5 border-r border-slate-300">PIC Utama</th>
                                <th class="p-2.5 border-r border-slate-300">Deadline</th>
                                <th class="p-2.5 border-r border-slate-300 text-center">Progress</th>
                                <th class="p-2.5 border-r border-slate-300">Status</th>
                                <th class="p-2.5">Kendala / Tindak Lanjut</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200">
                            <tr
                                v-for="(t, idx) in tasks"
                                :key="t.id"
                                class="border-b border-slate-200"
                            >
                                <td class="p-2.5 border-r border-slate-200 text-center font-mono">{{ idx + 1 }}</td>
                                <td class="p-2.5 border-r border-slate-200 font-bold text-slate-900">{{ t.project?.name }}</td>
                                <td class="p-2.5 border-r border-slate-200 font-medium">{{ t.name }}</td>
                                <td class="p-2.5 border-r border-slate-200">{{ t.stage }}</td>
                                <td class="p-2.5 border-r border-slate-200 font-semibold">{{ t.pic?.name || '-' }}</td>
                                <td class="p-2.5 border-r border-slate-200">{{ formatDateID(t.deadline) }}</td>
                                <td class="p-2.5 border-r border-slate-200 text-center font-bold">
                                    {{ t.computed_progress }}% (B: {{ t.weight }}%)
                                </td>
                                <td class="p-2.5 border-r border-slate-200">
                                    <StatusBadge :status="t.status" />
                                </td>
                                <td class="p-2.5 text-[11px]">
                                    <span v-if="t.obstacle" class="text-rose-600 block">K: {{ t.obstacle }}</span>
                                    <span v-if="t.next_action" class="text-slate-700 block">TL: {{ t.next_action }}</span>
                                    <span v-if="!t.obstacle && !t.next_action" class="text-slate-400 italic">-</span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Tanda Tangan Pengesahan -->
                <div class="grid grid-cols-2 gap-8 pt-8 text-center text-xs print-break">
                    <div class="space-y-16">
                        <p class="font-semibold text-slate-700">Diverifikasi & Disusun Oleh,</p>
                        <div>
                            <p class="font-bold text-slate-900 underline">Nurul Ilmi, S.Ars</p>
                            <p class="text-slate-500">Team Leader / Principal Architect</p>
                        </div>
                    </div>

                    <div class="space-y-16">
                        <p class="font-semibold text-slate-700">Mengetahui / Menyetujui,</p>
                        <div>
                            <p class="font-bold text-slate-900 underline">Direktur Utama</p>
                            <p class="text-slate-500">CV. Design Salsabila</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
