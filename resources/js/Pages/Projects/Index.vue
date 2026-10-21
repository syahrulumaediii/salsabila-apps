<script setup>
import { ref } from 'vue';
import { Head, router, useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import StatusBadge from '@/Components/Common/StatusBadge.vue';
import ProgressBar from '@/Components/Common/ProgressBar.vue';
import ProjectModal from '@/Components/Modals/ProjectModal.vue';

const props = defineProps({
    projects: Array,
    users: Array,
    sidebarBadges: Object,
});

const showModal = ref(false);
const activeProject = ref(null);

function openCreateModal() {
    activeProject.value = null;
    showModal.value = true;
}

function openEditModal(p) {
    activeProject.value = p;
    showModal.value = true;
}

function deleteProject(p) {
    if (!confirm(`Hapus proyek "${p.name}" beserta seluruh tugas di dalamnya?`)) return;
    router.delete(route('projects.destroy', p.id), { preserveScroll: true });
}

// History Log inline form
const historyInputs = ref({});

function addHistoryLog(projectId) {
    const text = (historyInputs.value[projectId] || '').trim();
    if (!text) return;

    router.post(route('projects.history.add', projectId), { text }, {
        preserveScroll: true,
        onSuccess: () => {
            historyInputs.value[projectId] = '';
        },
    });
}

function formatDateID(dateStr) {
    if (!dateStr) return '-';
    const d = new Date(dateStr + 'T00:00:00');
    return d.toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' });
}
</script>

<template>
    <AppLayout
        title="Monitoring Progress Proyek"
        subtitle="Perhitungan Progress Berbobot (Weighted Progress), Rincian Tahapan, dan Riwayat Perkembangan"
        :badges="sidebarBadges"
    >
        <template #header-actions>
            <button
                @click="openCreateModal"
                class="px-3.5 py-2 bg-sky-600 hover:bg-sky-700 text-white rounded-lg text-xs font-semibold flex items-center gap-1.5 shadow-sm transition active:scale-95"
            >
                <i class="fa-solid fa-folder-plus text-xs"></i>
                <span class="inline">Proyek Baru</span>
            </button>
        </template>

        <Head title="Monitoring Proyek — Salsabila Control Center" />

        <div class="space-y-6">
            <!-- Action Header -->
            <div class="flex flex-wrap items-center justify-between gap-4 bg-white p-4 rounded-xl border border-slate-200 shadow-sm">
                <div>
                    <h3 class="text-sm font-bold text-slate-800">Portofolio & Monitoring Detail Proyek</h3>
                    <p class="text-xs text-slate-500">Progress proyek dihitung secara otomatis berdasarkan <strong>Bobot Pekerjaan</strong> masing-masing tugas</p>
                </div>
                <button
                    @click="openCreateModal"
                    class="px-4 py-2 bg-sky-600 hover:bg-sky-700 text-white rounded-lg text-xs font-semibold flex items-center gap-2 shadow-sm transition"
                >
                    <i class="fa-solid fa-folder-plus"></i> Tambah Proyek Baru
                </button>
            </div>

            <!-- List of Projects -->
            <div class="space-y-6">
                <div
                    v-for="p in projects"
                    :key="p.id"
                    class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm space-y-4 hover:shadow-md transition"
                >
                    <!-- Top Bar Proyek -->
                    <div class="flex flex-wrap items-start justify-between gap-3">
                        <div class="space-y-1">
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="px-2 py-0.5 bg-slate-900 text-white rounded text-xs font-mono font-bold">
                                    {{ p.code || 'P' }}
                                </span>
                                <StatusBadge :status="p.status" />
                                <span v-if="p.contract_no" class="text-xs text-slate-400 font-mono">
                                    SPK: {{ p.contract_no }}
                                </span>
                            </div>
                            <h3 class="text-base font-bold text-slate-900">{{ p.name }}</h3>
                            <p class="text-xs text-slate-500">
                                Klien: <strong>{{ p.client }}</strong> &bull; PIC: <strong>{{ p.pic?.name || '-' }}</strong>
                                <span v-if="p.team_members">&bull; Tim: {{ p.team_members }}</span>
                            </p>
                        </div>

                        <div class="flex items-center gap-2">
                            <button
                                @click="openEditModal(p)"
                                class="px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-xs font-semibold flex items-center gap-1.5 transition"
                            >
                                <i class="fa-solid fa-pen-to-square"></i> Edit
                            </button>
                            <button
                                @click="deleteProject(p)"
                                class="px-2.5 py-1.5 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg text-xs transition"
                                title="Hapus Proyek"
                            >
                                <i class="fa-solid fa-trash-can"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Progress Stats & Dates -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 pt-3 border-t border-slate-100 text-xs">
                        <div>
                            <span class="text-slate-400 text-[10px] font-semibold block">Jadwal Kalender</span>
                            <span class="font-bold text-slate-800">
                                {{ formatDateID(p.start_date) }} s/d {{ formatDateID(p.deadline) }}
                            </span>
                        </div>

                        <div>
                            <span class="text-slate-400 text-[10px] font-semibold block">Total Tugas</span>
                            <span class="font-bold text-slate-800">
                                {{ p.tasks?.length || 0 }} Tugas
                                ({{ p.tasks?.filter(t => t.status === 'Selesai').length || 0 }} Selesai)
                            </span>
                        </div>

                        <div>
                            <div class="flex justify-between text-[11px] font-semibold mb-1">
                                <span class="text-emerald-700">Realisasi: {{ p.weighted_progress }}%</span>
                                <span class="text-sky-700">Target: {{ p.target_progress }}%</span>
                            </div>
                            <ProgressBar :progress="p.weighted_progress" :show-label="false" height-class="h-2" />
                        </div>
                    </div>

                    <!-- Kendala & Catatan TL -->
                    <div v-if="p.obstacle || p.notes" class="grid grid-cols-1 md:grid-cols-2 gap-3 text-xs">
                        <div v-if="p.obstacle" class="p-3 bg-rose-50 border border-rose-200 rounded-lg text-rose-800">
                            <strong><i class="fa-solid fa-triangle-exclamation"></i> Kendala Proyek:</strong> {{ p.obstacle }}
                        </div>
                        <div v-if="p.notes" class="p-3 bg-slate-50 border border-slate-200 rounded-lg text-slate-800">
                            <strong><i class="fa-solid fa-note-sticky"></i> Catatan Team Leader:</strong> {{ p.notes }}
                        </div>
                    </div>

                    <!-- Riwayat Perkembangan & Milestone (History Log) -->
                    <div class="bg-slate-50/70 p-4 rounded-xl border border-slate-200/80 space-y-3">
                        <h4 class="text-xs font-bold text-slate-700 uppercase tracking-wider flex items-center gap-1.5">
                            <i class="fa-solid fa-timeline text-sky-500"></i> Riwayat Perkembangan Proyek
                        </h4>

                        <!-- Add new log -->
                        <div class="flex gap-2">
                            <input
                                type="text"
                                v-model="historyInputs[p.id]"
                                @keydown.enter="addHistoryLog(p.id)"
                                placeholder="Catat milestone atau perkembangan baru proyek ini..."
                                class="flex-1 px-3 py-1.5 border border-slate-300 rounded-lg text-xs focus:outline-none focus:border-sky-500 bg-white"
                            />
                            <button
                                @click="addHistoryLog(p.id)"
                                class="px-3.5 py-1.5 bg-slate-900 hover:bg-slate-800 text-white rounded-lg text-xs font-semibold shrink-0"
                            >
                                Catat Log
                            </button>
                        </div>

                        <!-- Logs list -->
                        <div class="space-y-1.5 max-h-48 overflow-y-auto pt-1">
                            <div
                                v-for="log in p.history_logs"
                                :key="log.id"
                                class="flex items-start gap-2 text-xs py-1 text-slate-700"
                            >
                                <span class="text-[10px] text-slate-400 font-mono shrink-0 mt-0.5">
                                    [{{ formatDateID(log.log_date) }}]
                                </span>
                                <span>{{ log.text }}</span>
                            </div>

                            <p v-if="!p.history_logs?.length" class="text-[11px] text-slate-400 italic">
                                Belum ada catatan riwayat perkembangan.
                            </p>
                        </div>
                    </div>
                </div>

                <div v-if="projects.length === 0" class="bg-white rounded-xl border border-slate-200 p-12 text-center text-slate-400 text-xs">
                    <i class="fa-solid fa-folder-open text-3xl mb-2 text-slate-300 block"></i>
                    Belum ada data proyek. Silakan buat proyek baru dengan tombol di atas.
                </div>
            </div>
        </div>

        <template #modals>
            <ProjectModal
                :show="showModal"
                :project="activeProject"
                :users="users"
                @close="showModal = false"
            />
        </template>
    </AppLayout>
</template>
