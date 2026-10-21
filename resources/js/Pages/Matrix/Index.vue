<script setup>
import { reactive, ref } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import StatusBadge from '@/Components/Common/StatusBadge.vue';
import PriorityBadge from '@/Components/Common/PriorityBadge.vue';
import ProgressBar from '@/Components/Common/ProgressBar.vue';
import TaskModal from '@/Components/Modals/TaskModal.vue';

const props = defineProps({
    tasks: Array,
    projects: Array,
    users: Array,
    filters: Object,
    sidebarBadges: Object,
});

const filterForm = reactive({
    search: props.filters?.search || '',
    project_id: props.filters?.project_id || 'ALL',
    stage: props.filters?.stage || 'ALL',
    pic_user_id: props.filters?.pic_user_id || 'ALL',
    status: props.filters?.status || 'ALL',
    date_from: props.filters?.date_from || '',
    date_to: props.filters?.date_to || '',
    sort: props.filters?.sort || 'group',
});

function applyFilters() {
    router.get(route('matrix.index'), filterForm, { preserveState: true, preserveScroll: true });
}

// Modal state
const showModal = ref(false);
const activeTask = ref(null);

function openEditModal(task) {
    activeTask.value = task;
    showModal.value = true;
}

function openCreateModal() {
    activeTask.value = null;
    showModal.value = true;
}

function quickToggleDone(task) {
    router.patch(route('tasks.toggleDone', task.id), {}, { preserveScroll: true });
}

function formatDateID(dateStr) {
    if (!dateStr) return '-';
    const d = new Date(dateStr + 'T00:00:00');
    return d.toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' });
}
</script>

<template>
    <AppLayout
        title="Matriks Pekerjaan Konsultan"
        subtitle="Kendali Menyeluruh Berdasarkan Proyek & 4 Tahapan Bisnis (A. Kontrak, B. Perencanaan, C. Pengawasan, D. Penagihan)"
        :badges="sidebarBadges"
        @open-task-modal="openCreateModal"
    >
        <Head title="Matriks Pekerjaan — Salsabila Control Center" />

        <div class="space-y-5">
            <!-- Filter Bar -->
            <div class="bg-white rounded-xl border border-slate-200 p-4 shadow-sm space-y-3">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <div>
                        <h3 class="text-sm font-bold text-slate-800">Matriks Aktivitas Perusahaan</h3>
                        <p class="text-xs text-slate-500">A. Kontrak &bull; B. Perencanaan &bull; C. Pengawasan &bull; D. Penagihan</p>
                    </div>

                    <div class="flex flex-wrap items-center gap-2">
                        <input
                            type="text"
                            v-model="filterForm.search"
                            @input="applyFilters"
                            placeholder="Cari di matriks..."
                            class="px-3 py-1.5 border border-slate-300 rounded-lg text-xs w-40 md:w-48"
                        />

                        <select
                            v-model="filterForm.project_id"
                            @change="applyFilters"
                            class="px-2.5 py-1.5 border border-slate-300 rounded-lg text-xs bg-white"
                        >
                            <option value="ALL">Semua Proyek</option>
                            <option v-for="p in projects" :key="p.id" :value="p.id">{{ p.name }}</option>
                        </select>

                        <select
                            v-model="filterForm.stage"
                            @change="applyFilters"
                            class="px-2.5 py-1.5 border border-slate-300 rounded-lg text-xs bg-white"
                        >
                            <option value="ALL">Semua Tahapan (A-D)</option>
                            <option value="A. KONTRAK">A. KONTRAK</option>
                            <option value="B. PERENCANAAN">B. PERENCANAAN</option>
                            <option value="C. PELAKSANAAN / PENGAWASAN">C. PELAKSANAAN / PENGAWASAN</option>
                            <option value="D. PENAGIHAN">D. PENAGIHAN</option>
                        </select>

                        <select
                            v-model="filterForm.status"
                            @change="applyFilters"
                            class="px-2.5 py-1.5 border border-slate-300 rounded-lg text-xs bg-white"
                        >
                            <option value="ALL">Semua Status</option>
                            <option value="Belum Dimulai">Belum Dimulai</option>
                            <option value="Sedang Dikerjakan">Sedang Dikerjakan</option>
                            <option value="Menunggu Pemeriksaan">Menunggu Pemeriksaan</option>
                            <option value="Revisi">Revisi</option>
                            <option value="Terhambat">Terhambat</option>
                            <option value="Selesai">Selesai</option>
                        </select>

                        <select
                            v-model="filterForm.sort"
                            @change="applyFilters"
                            class="px-2.5 py-1.5 border border-slate-300 rounded-lg text-xs bg-slate-50 font-semibold"
                        >
                            <option value="group">Kelompokkan Proyek & Tahap</option>
                            <option value="deadline">Urut Deadline</option>
                            <option value="priority">Urut Prioritas</option>
                            <option value="progress">Urut Progress</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Tabel Matriks Interaktif -->
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="bg-slate-900 text-white uppercase text-[11px] tracking-wider">
                            <th class="p-3 whitespace-nowrap">Proyek</th>
                            <th class="p-3 whitespace-nowrap">Tahapan</th>
                            <th class="p-3 min-w-[200px]">Pekerjaan & Sub-Tahap</th>
                            <th class="p-3 whitespace-nowrap">PIC & Tim</th>
                            <th class="p-3 whitespace-nowrap">Deadline</th>
                            <th class="p-3 w-36 whitespace-nowrap">Progress (Bobot)</th>
                            <th class="p-3 whitespace-nowrap">Status</th>
                            <th class="p-3 min-w-[150px]">Kendala</th>
                            <th class="p-3 min-w-[150px]">Tindakan Berikutnya</th>
                            <th class="p-3 text-center whitespace-nowrap">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        <tr
                            v-for="task in tasks"
                            :key="task.id"
                            class="hover:bg-slate-50/80 transition"
                        >
                            <td class="p-3 font-bold text-slate-900 whitespace-nowrap">
                                <div class="flex items-center gap-1.5">
                                    <span class="px-1.5 py-0.5 bg-slate-100 border border-slate-300 rounded text-[10px] font-mono">
                                        {{ task.project?.code || 'P' }}
                                    </span>
                                    <span class="truncate max-w-[160px]" :title="task.project?.name">{{ task.project?.name }}</span>
                                </div>
                            </td>

                            <td class="p-3 whitespace-nowrap">
                                <span class="px-2 py-0.5 bg-slate-100 text-slate-800 rounded font-semibold text-[10px] border border-slate-200">
                                    {{ task.stage }}
                                </span>
                            </td>

                            <td class="p-3">
                                <p class="font-bold text-slate-900">{{ task.name }}</p>
                                <p v-if="task.sub_stage" class="text-[10px] text-slate-500 mt-0.5">{{ task.sub_stage }}</p>
                            </td>

                            <td class="p-3 whitespace-nowrap">
                                <p class="font-bold text-slate-800">{{ task.pic?.name || '-' }}</p>
                                <p v-if="task.support_team" class="text-[10px] text-slate-400 truncate max-w-[120px]">{{ task.support_team }}</p>
                            </td>

                            <td class="p-3 whitespace-nowrap">
                                <span :class="[task.is_overdue ? 'text-rose-600 font-bold' : 'text-slate-700 font-medium']">
                                    {{ formatDateID(task.deadline) }}
                                </span>
                                <span v-if="task.is_overdue" class="text-[9px] text-rose-600 font-bold block">
                                    Overdue
                                </span>
                            </td>

                            <td class="p-3">
                                <div class="flex justify-between text-[10px] font-semibold mb-1">
                                    <span>{{ task.computed_progress }}%</span>
                                    <span class="text-slate-400">B: {{ task.weight }}%</span>
                                </div>
                                <ProgressBar :progress="task.computed_progress" :show-label="false" height-class="h-1.5" />
                            </td>

                            <td class="p-3 whitespace-nowrap">
                                <StatusBadge :status="task.status" />
                            </td>

                            <td class="p-3 text-[11px]">
                                <span v-if="task.obstacle" class="text-rose-700 font-medium">
                                    <i class="fa-solid fa-triangle-exclamation mr-0.5"></i> {{ task.obstacle }}
                                </span>
                                <span v-else class="text-slate-400 italic">-</span>
                            </td>

                            <td class="p-3 text-[11px]">
                                <span v-if="task.next_action" class="text-slate-700">
                                    {{ task.next_action }}
                                </span>
                                <span v-else class="text-slate-400 italic">-</span>
                            </td>

                            <td class="p-3 text-center whitespace-nowrap">
                                <div class="flex items-center justify-center gap-1">
                                    <button
                                        @click="quickToggleDone(task)"
                                        :class="[
                                            'p-1.5 rounded transition text-xs',
                                            task.status === 'Selesai' ? 'text-emerald-600 hover:bg-emerald-50' : 'text-slate-400 hover:text-emerald-600 hover:bg-slate-100'
                                        ]"
                                        :title="task.status === 'Selesai' ? 'Selesai' : 'Tandai Selesai'"
                                    >
                                        <i class="fa-solid fa-circle-check"></i>
                                    </button>
                                    <button
                                        @click="openEditModal(task)"
                                        class="p-1.5 rounded text-slate-400 hover:text-sky-600 hover:bg-slate-100 transition text-xs"
                                        title="Edit Pekerjaan"
                                    >
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>

                        <tr v-if="tasks.length === 0">
                            <td colspan="10" class="p-8 text-center text-slate-400">
                                Tidak ada data matriks yang sesuai filter.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <template #modals>
            <TaskModal
                :show="showModal"
                :task="activeTask"
                :projects="projects"
                :users="users"
                :divisions="['Struktur', 'Arsitektur', 'MEP', 'Estimasi & RAB', 'Survey & Pengawasan', 'Administrasi & Kontrak']"
                @close="showModal = false"
            />
        </template>
    </AppLayout>
</template>
