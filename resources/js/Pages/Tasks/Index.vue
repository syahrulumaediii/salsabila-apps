<script setup>
import { ref, reactive } from 'vue';
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
    divisions: Array,
    filters: Object,
    sidebarBadges: Object,
});

const filterForm = reactive({
    search: props.filters?.search || '',
    project_id: props.filters?.project_id || 'ALL',
    divisi: props.filters?.divisi || 'ALL',
    pic_user_id: props.filters?.pic_user_id || 'ALL',
    status: props.filters?.status || 'ALL',
    priority: props.filters?.priority || 'ALL',
    sort: props.filters?.sort || 'deadline_asc',
});

function applyFilters() {
    router.get(route('tasks.index'), filterForm, { preserveState: true, preserveScroll: true });
}

// Modal state
const showModal = ref(false);
const activeTask = ref(null);

function openCreateModal() {
    activeTask.value = null;
    showModal.value = true;
}

function openEditModal(task) {
    activeTask.value = task;
    showModal.value = true;
}

function toggleSubtask(subtask) {
    router.patch(route('tasks.subtask.toggle', subtask.id), {}, { preserveScroll: true });
}

function quickToggleDone(task) {
    router.patch(route('tasks.toggleDone', task.id), {}, { preserveScroll: true });
}

function deleteTask(task) {
    if (!confirm(`Hapus pekerjaan "${task.name}"?`)) return;
    router.delete(route('tasks.destroy', task.id), { preserveScroll: true });
}

function formatDateID(dateStr) {
    if (!dateStr) return 'Tanpa Deadline';
    const d = new Date(dateStr + 'T00:00:00');
    return d.toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' });
}
</script>

<template>
    <AppLayout
        title="To-Do List & Sub-Checklist Pekerjaan"
        subtitle="Manajemen Tugas Detail dengan Perhitungan Progress Otomatis Berbasis Checklist"
        :badges="sidebarBadges"
        @open-task-modal="openCreateModal"
    >
        <Head title="To-Do List & Tugas — Salsabila Control Center" />

        <div class="space-y-5">
            <!-- Filter & Action Bar -->
            <div class="bg-white rounded-xl border border-slate-200 p-4 shadow-sm space-y-3">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <div class="flex flex-wrap items-center gap-2 flex-1">
                        <!-- Search -->
                        <div class="relative w-full sm:w-48 md:w-56">
                            <i class="fa-solid fa-magnifying-glass absolute left-3 top-2.5 text-slate-400 text-xs"></i>
                            <input
                                type="text"
                                v-model="filterForm.search"
                                @input="applyFilters"
                                placeholder="Cari nama / kendala..."
                                class="w-full pl-8 pr-3 py-1.5 border border-slate-300 rounded-lg text-xs focus:outline-none focus:border-sky-500"
                            />
                        </div>

                        <!-- Project Filter -->
                        <select
                            v-model="filterForm.project_id"
                            @change="applyFilters"
                            class="px-2.5 py-1.5 border border-slate-300 rounded-lg text-xs bg-white"
                        >
                            <option value="ALL">Semua Proyek</option>
                            <option v-for="p in projects" :key="p.id" :value="p.id">
                                {{ p.code ? `[${p.code}] ` : '' }}{{ p.name }}
                            </option>
                        </select>

                        <!-- Divisi Filter -->
                        <select
                            v-model="filterForm.divisi"
                            @change="applyFilters"
                            class="px-2.5 py-1.5 border border-slate-300 rounded-lg text-xs bg-white"
                        >
                            <option value="ALL">Semua Divisi</option>
                            <option v-for="d in divisions" :key="d" :value="d">{{ d }}</option>
                        </select>

                        <!-- PIC Filter -->
                        <select
                            v-model="filterForm.pic_user_id"
                            @change="applyFilters"
                            class="px-2.5 py-1.5 border border-slate-300 rounded-lg text-xs bg-white"
                        >
                            <option value="ALL">Semua PIC</option>
                            <option v-for="u in users" :key="u.id" :value="u.id">{{ u.name }}</option>
                        </select>

                        <!-- Status Filter -->
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

                        <!-- Priority Filter -->
                        <select
                            v-model="filterForm.priority"
                            @change="applyFilters"
                            class="px-2.5 py-1.5 border border-slate-300 rounded-lg text-xs bg-white"
                        >
                            <option value="ALL">Semua Prioritas</option>
                            <option value="Kritis">Kritis</option>
                            <option value="Tinggi">Tinggi</option>
                            <option value="Sedang">Sedang</option>
                            <option value="Rendah">Rendah</option>
                        </select>

                        <!-- Sort -->
                        <select
                            v-model="filterForm.sort"
                            @change="applyFilters"
                            class="px-2.5 py-1.5 border border-slate-300 rounded-lg text-xs bg-slate-50 font-medium"
                        >
                            <option value="deadline_asc">Urut: Deadline Terdekat</option>
                            <option value="priority_desc">Urut: Prioritas Tertinggi</option>
                            <option value="progress_asc">Urut: Progress Terendah</option>
                            <option value="progress_desc">Urut: Progress Tertinggi</option>
                        </select>
                    </div>

                    <button
                        @click="openCreateModal"
                        class="px-4 py-2 bg-sky-600 hover:bg-sky-700 text-white rounded-lg text-xs font-semibold flex items-center gap-2 shrink-0 transition"
                    >
                        <i class="fa-solid fa-plus"></i> Tambah Pekerjaan
                    </button>
                </div>
            </div>

            <!-- Daftar Kartu Pekerjaan -->
            <div class="space-y-3">
                <div
                    v-for="task in tasks"
                    :key="task.id"
                    class="bg-white rounded-xl border border-slate-200 p-4 shadow-sm hover:shadow-md transition space-y-3"
                >
                    <!-- Header Kartu -->
                    <div class="flex flex-wrap items-start justify-between gap-3">
                        <div class="space-y-1 flex-1">
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="px-2 py-0.5 bg-slate-900 text-white rounded text-[10px] font-mono font-bold">
                                    {{ task.code || 'T' }}
                                </span>
                                <span class="px-2 py-0.5 bg-slate-100 text-slate-700 rounded text-[10px] font-semibold border border-slate-200">
                                    {{ task.stage }}
                                </span>
                                <span class="px-2 py-0.5 bg-sky-50 text-sky-700 rounded text-[10px] font-semibold border border-sky-200">
                                    Divisi {{ task.divisi }}
                                </span>
                                <span v-if="task.need_decision" class="px-2 py-0.5 bg-rose-600 text-white rounded text-[10px] font-bold animate-pulse">
                                    <i class="fa-solid fa-bell"></i> Butuh Keputusan TL
                                </span>
                                <span v-if="task.client_follow_up" class="px-2 py-0.5 bg-amber-500 text-slate-950 rounded text-[10px] font-bold">
                                    <i class="fa-solid fa-handshake"></i> Follow-Up Klien
                                </span>
                            </div>

                            <h3 class="text-sm font-bold text-slate-900 leading-snug">{{ task.name }}</h3>
                            <p class="text-xs text-slate-500">
                                <strong>{{ task.project?.name }}</strong> &bull; Sub-Tahap: {{ task.sub_stage || '-' }}
                            </p>
                        </div>

                        <div class="flex items-center gap-2 shrink-0">
                            <StatusBadge :status="task.status" />
                            <PriorityBadge :priority="task.priority" />
                        </div>
                    </div>

                    <!-- Meta Information & Progress -->
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-3 text-xs pt-2 border-t border-slate-100">
                        <div>
                            <span class="text-[10px] text-slate-400 block font-semibold">PIC Utama</span>
                            <span class="font-bold text-slate-800">{{ task.pic?.name || 'Belum Ditentukan' }}</span>
                            <span v-if="task.support_team" class="text-[10px] text-slate-500 block truncate">
                                Tim: {{ task.support_team }}
                            </span>
                        </div>

                        <div>
                            <span class="text-[10px] text-slate-400 block font-semibold">Tenggat Waktu (Deadline)</span>
                            <span :class="[task.is_overdue ? 'text-rose-600 font-bold' : 'text-slate-800 font-medium']">
                                <i class="fa-solid fa-calendar text-slate-400 mr-1"></i>
                                {{ formatDateID(task.deadline) }}
                            </span>
                            <span v-if="task.is_overdue" class="text-[10px] text-rose-600 font-bold block">
                                (Terlambat)
                            </span>
                        </div>

                        <div>
                            <span class="text-[10px] text-slate-400 block font-semibold">Bobot dalam Proyek</span>
                            <span class="font-bold text-slate-700">{{ task.weight }}%</span>
                            <span class="text-[10px] text-slate-400 block">
                                {{ task.manual_progress_mode ? 'Mode Manual' : 'Mode Otomatis' }}
                            </span>
                        </div>

                        <div>
                            <ProgressBar :progress="task.computed_progress" />
                        </div>
                    </div>

                    <!-- Subtasks Checklist Section -->
                    <div v-if="task.subtasks?.length > 0" class="bg-slate-50 p-3 rounded-lg border border-slate-200/80 space-y-2">
                        <div class="flex items-center justify-between text-xs">
                            <span class="font-bold text-slate-700 flex items-center gap-1.5">
                                <i class="fa-solid fa-list-check text-sky-600"></i>
                                Sub-Pekerjaan ({{ task.subtasks.filter(s => s.is_done).length }}/{{ task.subtasks.length }})
                            </span>
                            <span class="text-[11px] text-slate-500 font-medium">Klik kotak untuk update progress</span>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-2 text-xs">
                            <label
                                v-for="st in task.subtasks"
                                :key="st.id"
                                class="flex items-center gap-2 p-1.5 rounded hover:bg-white cursor-pointer transition border border-transparent hover:border-slate-200"
                            >
                                <input
                                    type="checkbox"
                                    :checked="st.is_done"
                                    @change="toggleSubtask(st)"
                                    class="rounded text-sky-600 focus:ring-sky-500"
                                />
                                <span :class="[st.is_done ? 'line-through text-slate-400' : 'text-slate-800 font-medium']">
                                    {{ st.title }}
                                </span>
                            </label>
                        </div>
                    </div>

                    <!-- Kendala & Tindakan Selanjutnya -->
                    <div v-if="task.obstacle || task.next_action || task.notes" class="grid grid-cols-1 md:grid-cols-2 gap-2 text-xs">
                        <div v-if="task.obstacle" class="p-2.5 rounded-lg bg-rose-50 border border-rose-200 text-rose-800">
                            <strong><i class="fa-solid fa-triangle-exclamation"></i> Kendala:</strong> {{ task.obstacle }}
                        </div>
                        <div v-if="task.next_action" class="p-2.5 rounded-lg bg-sky-50 border border-sky-200 text-sky-800">
                            <strong><i class="fa-solid fa-forward-step"></i> Tindakan Berikutnya:</strong> {{ task.next_action }}
                        </div>
                    </div>

                    <!-- Footer Action Bar -->
                    <div class="flex flex-wrap items-center justify-between gap-2 pt-2 border-t border-slate-100">
                        <div class="text-[11px] text-slate-400">
                            Diperbarui: {{ formatDateID(task.last_updated_date || task.updated_at) }}
                        </div>

                        <div class="flex items-center gap-2">
                            <button
                                @click="quickToggleDone(task)"
                                class="px-3 py-1.5 rounded-lg text-xs font-semibold flex items-center gap-1.5 transition"
                                :class="[
                                    task.status === 'Selesai'
                                        ? 'bg-amber-100 text-amber-800 hover:bg-amber-200'
                                        : 'bg-emerald-600 hover:bg-emerald-700 text-white'
                                ]"
                            >
                                <i :class="['fa-solid', task.status === 'Selesai' ? 'fa-rotate-left' : 'fa-check']"></i>
                                {{ task.status === 'Selesai' ? 'Buka Kembali' : 'Tandai Selesai' }}
                            </button>

                            <button
                                @click="openEditModal(task)"
                                class="px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-xs font-semibold flex items-center gap-1.5"
                            >
                                <i class="fa-solid fa-pen-to-square"></i> Edit
                            </button>

                            <button
                                @click="deleteTask(task)"
                                class="px-2.5 py-1.5 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg text-xs"
                                title="Hapus Pekerjaan"
                            >
                                <i class="fa-solid fa-trash-can"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <div v-if="tasks.length === 0" class="bg-white rounded-xl border border-slate-200 p-12 text-center text-slate-400 text-xs">
                    <i class="fa-solid fa-folder-open text-3xl mb-2 text-slate-300 block"></i>
                    Tidak ada pekerjaan yang cocok dengan filter yang dipilih.
                </div>
            </div>
        </div>

        <!-- Task Modal Component -->
        <template #modals>
            <TaskModal
                :show="showModal"
                :task="activeTask"
                :projects="projects"
                :users="users"
                :divisions="divisions"
                @close="showModal = false"
            />
        </template>
    </AppLayout>
</template>
