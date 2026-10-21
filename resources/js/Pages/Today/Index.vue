<script setup>
import { ref } from 'vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import StatusBadge from '@/Components/Common/StatusBadge.vue';
import PriorityBadge from '@/Components/Common/PriorityBadge.vue';
import ProgressBar from '@/Components/Common/ProgressBar.vue';
import TaskModal from '@/Components/Modals/TaskModal.vue';

const props = defineProps({
    selectedDate: String,
    agenda: Object,
    top3Tasks: Array,
    allTasks: Array,
    projects: Array,
    users: Array,
    divisions: Array,
    categories: Object,
    sidebarBadges: Object,
});

const showTaskModal = ref(false);
const datePicker = ref(props.selectedDate);

function onDateChange() {
    router.get(route('today.index'), { date: datePicker.value }, { preserveState: true });
}

function setToday() {
    const today = new Date().toISOString().split('T')[0];
    datePicker.value = today;
    router.get(route('today.index'), { date: today });
}

function changeDateBy(days) {
    const current = new Date(datePicker.value + 'T00:00:00');
    current.setDate(current.getDate() + days);
    const newDateStr = current.toISOString().split('T')[0];
    datePicker.value = newDateStr;
    router.get(route('today.index'), { date: newDateStr });
}

// Top 3 Priority selection
function onTopTaskSelect(slotIndex, taskId) {
    const current = [...(props.agenda.top_task_ids || [])];
    current[slotIndex] = taskId ? Number(taskId) : null;
    const filtered = current.filter(Boolean);

    router.post(route('today.top3'), {
        date: props.selectedDate,
        top_task_ids: filtered,
    }, { preserveScroll: true });
}

// Checklist Harian
const newChecklistText = ref('');
function addChecklist() {
    if (!newChecklistText.value.trim()) return;
    router.post(route('today.checklist.add'), {
        daily_agenda_id: props.agenda.id,
        text: newChecklistText.value.trim(),
    }, {
        preserveScroll: true,
        onSuccess: () => {
            newChecklistText.value = '';
        },
    });
}

function toggleChecklist(item) {
    router.patch(route('today.checklist.toggle', item.id), {}, { preserveScroll: true });
}

function deleteChecklist(item) {
    if (!confirm('Hapus item checklist ini?')) return;
    router.delete(route('today.checklist.delete', item.id), { preserveScroll: true });
}

// Evaluasi Akhir Hari Form
const evalForm = useForm({
    daily_agenda_id: props.agenda.id,
    eval_q1: props.agenda.eval_q1 || '',
    eval_q2: props.agenda.eval_q2 || '',
    eval_q3: props.agenda.eval_q3 || '',
    eval_q4: props.agenda.eval_q4 || '',
    eval_q5: props.agenda.eval_q5 || '',
});

const isSaved = ref(false);

function submitEvaluation() {
    evalForm.post(route('today.evaluation'), {
        preserveScroll: true,
        onSuccess: () => {
            isSaved.value = true;
            setTimeout(() => { isSaved.value = false; }, 3000);
        },
    });
}
</script>

<template>
    <AppLayout
        title="Agenda Hari Ini"
        subtitle="Prioritas Harian, Checklist Tindak Lanjut, dan Evaluasi Akhir Hari Team Leader"
        :badges="sidebarBadges"
        @open-task-modal="showTaskModal = true"
    >
        <Head title="Agenda Hari Ini — Salsabila Control Center" />

        <div class="space-y-6 max-w-7xl mx-auto pb-8">
            <!-- ================= PEMILIHAN TANGGAL & QUICK ACTION ================= -->
            <div class="bg-white rounded-2xl border border-slate-200/80 p-4 sm:p-5 flex flex-wrap items-center justify-between gap-4 shadow-xs">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-amber-500/10 text-amber-600 flex items-center justify-center font-bold text-lg shrink-0">
                        <i class="fa-solid fa-calendar-check"></i>
                    </div>
                    <div>
                        <h3 class="text-xs sm:text-sm font-bold text-slate-800">Tanggal Agenda Team Leader</h3>
                        <p class="text-[11px] text-slate-500">Pilih tanggal untuk melihat histori kerja atau menyusun rencana harian</p>
                    </div>
                </div>

                <div class="flex flex-wrap items-center gap-2 w-full sm:w-auto">
                    <div class="flex items-center gap-1 bg-slate-100 p-1 rounded-xl border border-slate-200">
                        <button
                            @click="changeDateBy(-1)"
                            class="p-1.5 px-2 text-slate-600 hover:text-slate-900 hover:bg-white rounded-lg text-xs transition"
                            title="Hari Sebelumnya"
                        >
                            <i class="fa-solid fa-chevron-left"></i>
                        </button>
                        <input
                            type="date"
                            v-model="datePicker"
                            @change="onDateChange"
                            class="px-2.5 py-1 bg-white border border-slate-200 rounded-lg text-xs font-bold text-slate-800 focus:outline-none focus:border-sky-500"
                        />
                        <button
                            @click="changeDateBy(1)"
                            class="p-1.5 px-2 text-slate-600 hover:text-slate-900 hover:bg-white rounded-lg text-xs transition"
                            title="Hari Berikutnya"
                        >
                            <i class="fa-solid fa-chevron-right"></i>
                        </button>
                    </div>

                    <button
                        @click="setToday"
                        class="px-3.5 py-2 bg-slate-800 hover:bg-slate-700 text-white rounded-xl text-xs font-semibold transition"
                    >
                        Hari Ini
                    </button>

                    <button
                        @click="showTaskModal = true"
                        class="px-3.5 py-2 bg-sky-600 hover:bg-sky-500 text-white rounded-xl text-xs font-bold flex items-center gap-1.5 transition shadow-sm active:scale-95"
                    >
                        <i class="fa-solid fa-plus text-xs"></i>
                        <span>Tambah Tugas</span>
                    </button>
                </div>
            </div>

            <!-- ================= 3 PRIORITAS UTAMA HARI INI ================= -->
            <div class="bg-gradient-to-r from-slate-900 via-slate-800 to-sky-950 text-white rounded-2xl p-4 sm:p-6 shadow-md space-y-4">
                <div class="flex flex-wrap items-center justify-between gap-2 border-b border-slate-700/80 pb-3">
                    <div>
                        <span class="text-[10px] font-bold uppercase tracking-widest text-amber-400">Fokus Eksekusi Team Leader</span>
                        <h3 class="text-sm sm:text-base font-bold tracking-wide">3 PRIORITAS UTAMA HARI INI</h3>
                    </div>
                    <span class="text-[11px] text-slate-300">Pilih 3 tugas paling krusial yang wajib selesai atau diawasi hari ini</span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-3.5 sm:gap-4">
                    <div
                        v-for="slot in [0, 1, 2]"
                        :key="slot"
                        class="bg-slate-800/80 border border-slate-700 rounded-xl p-4 flex flex-col justify-between space-y-3 hover:border-slate-600 transition"
                    >
                        <div class="flex items-center justify-between gap-2">
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-amber-400 text-slate-950 uppercase tracking-wider shrink-0">
                                Prioritas #{{ slot + 1 }}
                            </span>
                            <select
                                :value="agenda.top_task_ids?.[slot] || ''"
                                @change="onTopTaskSelect(slot, $event.target.value)"
                                class="text-[11px] bg-slate-900 border border-slate-700 text-slate-200 rounded-lg px-2.5 py-1 max-w-[170px] truncate focus:outline-none focus:border-amber-400"
                            >
                                <option value="">Pilih Tugas...</option>
                                <option v-for="t in allTasks" :key="t.id" :value="t.id">
                                    {{ t.code ? `[${t.code}] ` : '' }}{{ t.name }}
                                </option>
                            </select>
                        </div>

                        <!-- Info Tugas Terpilih -->
                        <div v-if="top3Tasks[slot]" class="space-y-2 pt-1">
                            <h4 class="text-xs font-bold text-white leading-snug line-clamp-2">
                                {{ top3Tasks[slot].name }}
                            </h4>
                            <div class="flex items-center gap-1.5 text-[11px] text-slate-300 flex-wrap">
                                <span class="truncate max-w-[140px]">{{ top3Tasks[slot].project?.name }}</span>
                                <span>&bull;</span>
                                <span class="truncate">{{ top3Tasks[slot].pic?.name || '-' }}</span>
                                <span class="px-1.5 py-0.5 rounded text-[9px] font-semibold bg-sky-950 text-sky-300 border border-sky-700/60">
                                    {{ top3Tasks[slot].divisi || 'Arsitektur' }}
                                </span>
                            </div>
                            <ProgressBar :progress="top3Tasks[slot].computed_progress" />
                        </div>
                        <div v-else class="py-5 text-center text-slate-400 text-xs italic">
                            <i class="fa-solid fa-hand-pointer text-slate-500 mb-1 block"></i>
                            Belum ada tugas prioritas dipilih untuk slot ini.
                        </div>
                    </div>
                </div>
            </div>

            <!-- ================= GRID 4 KATEGORI OTOMATIS ================= -->
            <div>
                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-500 mb-3 flex items-center gap-1.5">
                    <i class="fa-solid fa-list-check text-sky-500"></i> Klasifikasi Tugas & Tindak Lanjut Otomatis
                </h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3.5 sm:gap-4">
                    <!-- 1. Terlambat -->
                    <div class="bg-white rounded-2xl border border-rose-200 shadow-xs overflow-hidden flex flex-col">
                        <div class="bg-rose-50/80 px-4 py-2.5 border-b border-rose-200 flex items-center justify-between">
                            <h4 class="text-xs font-bold text-rose-800 flex items-center gap-1.5">
                                <i class="fa-solid fa-clock"></i> Terlambat
                            </h4>
                            <span class="px-2 py-0.5 rounded-full bg-rose-600 text-white text-[10px] font-bold">
                                {{ categories.overdue.length }}
                            </span>
                        </div>
                        <div class="p-3 divide-y divide-slate-100 flex-1 overflow-y-auto max-h-56 space-y-2">
                            <div v-for="t in categories.overdue" :key="t.id" class="pt-2 first:pt-0 text-xs">
                                <p class="font-bold text-slate-900 truncate">{{ t.name }}</p>
                                <div class="flex items-center justify-between gap-1 text-[10px] text-slate-500 mt-0.5">
                                    <span class="truncate">{{ t.project?.name }} &bull; {{ t.pic?.name || '-' }}</span>
                                    <span class="px-1.5 py-0.5 rounded text-[9px] font-semibold bg-rose-50 text-rose-700 border border-rose-200 shrink-0">
                                        {{ t.divisi || 'Arsitektur' }}
                                    </span>
                                </div>
                            </div>
                            <p v-if="categories.overdue.length === 0" class="text-slate-400 text-xs py-5 text-center italic">Tidak ada tugas terlambat</p>
                        </div>
                    </div>

                    <!-- 2. Deadline Hari Ini -->
                    <div class="bg-white rounded-2xl border border-amber-200 shadow-xs overflow-hidden flex flex-col">
                        <div class="bg-amber-50/80 px-4 py-2.5 border-b border-amber-200 flex items-center justify-between">
                            <h4 class="text-xs font-bold text-amber-800 flex items-center gap-1.5">
                                <i class="fa-solid fa-calendar-day"></i> Hari Ini
                            </h4>
                            <span class="px-2 py-0.5 rounded-full bg-amber-500 text-slate-950 text-[10px] font-bold">
                                {{ categories.dueToday.length }}
                            </span>
                        </div>
                        <div class="p-3 divide-y divide-slate-100 flex-1 overflow-y-auto max-h-56 space-y-2">
                            <div v-for="t in categories.dueToday" :key="t.id" class="pt-2 first:pt-0 text-xs">
                                <p class="font-bold text-slate-900 truncate">{{ t.name }}</p>
                                <div class="flex items-center justify-between gap-1 text-[10px] text-slate-500 mt-0.5">
                                    <span class="truncate">{{ t.project?.name }} &bull; {{ t.pic?.name || '-' }}</span>
                                    <span class="px-1.5 py-0.5 rounded text-[9px] font-semibold bg-amber-50 text-amber-800 border border-amber-200 shrink-0">
                                        {{ t.divisi || 'Arsitektur' }}
                                    </span>
                                </div>
                            </div>
                            <p v-if="categories.dueToday.length === 0" class="text-slate-400 text-xs py-5 text-center italic">Tidak ada deadline hari ini</p>
                        </div>
                    </div>

                    <!-- 3. Menunggu QC -->
                    <div class="bg-white rounded-2xl border border-purple-200 shadow-xs overflow-hidden flex flex-col">
                        <div class="bg-purple-50/80 px-4 py-2.5 border-b border-purple-200 flex items-center justify-between">
                            <h4 class="text-xs font-bold text-purple-800 flex items-center gap-1.5">
                                <i class="fa-solid fa-clipboard-check"></i> Menunggu QC
                            </h4>
                            <span class="px-2 py-0.5 rounded-full bg-purple-600 text-white text-[10px] font-bold">
                                {{ categories.qc.length }}
                            </span>
                        </div>
                        <div class="p-3 divide-y divide-slate-100 flex-1 overflow-y-auto max-h-56 space-y-2">
                            <div v-for="t in categories.qc" :key="t.id" class="pt-2 first:pt-0 text-xs">
                                <p class="font-bold text-slate-900 truncate">{{ t.name }}</p>
                                <div class="flex items-center justify-between gap-1 text-[10px] text-slate-500 mt-0.5">
                                    <span class="truncate">{{ t.project?.name }} &bull; {{ t.pic?.name || '-' }}</span>
                                    <span class="px-1.5 py-0.5 rounded text-[9px] font-semibold bg-purple-50 text-purple-700 border border-purple-200 shrink-0">
                                        {{ t.divisi || 'Arsitektur' }}
                                    </span>
                                </div>
                            </div>
                            <p v-if="categories.qc.length === 0" class="text-slate-400 text-xs py-5 text-center italic">Nihil menunggu QC</p>
                        </div>
                    </div>

                    <!-- 4. Follow-Up Klien -->
                    <div class="bg-white rounded-2xl border border-sky-200 shadow-xs overflow-hidden flex flex-col">
                        <div class="bg-sky-50/80 px-4 py-2.5 border-b border-sky-200 flex items-center justify-between">
                            <h4 class="text-xs font-bold text-sky-800 flex items-center gap-1.5">
                                <i class="fa-solid fa-handshake"></i> Follow-Up Klien
                            </h4>
                            <span class="px-2 py-0.5 rounded-full bg-sky-600 text-white text-[10px] font-bold">
                                {{ categories.followUp.length }}
                            </span>
                        </div>
                        <div class="p-3 divide-y divide-slate-100 flex-1 overflow-y-auto max-h-56 space-y-2">
                            <div v-for="t in categories.followUp" :key="t.id" class="pt-2 first:pt-0 text-xs">
                                <p class="font-bold text-slate-900 truncate">{{ t.name }}</p>
                                <div class="flex items-center justify-between gap-1 text-[10px] text-slate-500 mt-0.5">
                                    <span class="truncate">{{ t.project?.name }} &bull; Klien: {{ t.project?.client }}</span>
                                    <span class="px-1.5 py-0.5 rounded text-[9px] font-semibold bg-sky-50 text-sky-700 border border-sky-200 shrink-0">
                                        {{ t.divisi || 'Arsitektur' }}
                                    </span>
                                </div>
                            </div>
                            <p v-if="categories.followUp.length === 0" class="text-slate-400 text-xs py-5 text-center italic">Tidak ada follow-up klien</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ================= DUAL PANEL: CHECKLIST & EVALUASI AKHIR HARI ================= -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Checklist Harian Pribadi Team Leader -->
                <div class="bg-white rounded-2xl border border-slate-200 p-4 sm:p-5 shadow-xs space-y-4 flex flex-col justify-between">
                    <div class="space-y-4">
                        <div>
                            <h3 class="text-xs sm:text-sm font-bold text-slate-800 flex items-center gap-2">
                                <i class="fa-solid fa-square-check text-sky-500"></i> Checklist Harian Pribadi TL
                            </h3>
                            <p class="text-[11px] text-slate-500">Catat agenda tindak lanjut kilat, panggilan telepon, atau pengawasan langsung</p>
                        </div>

                        <!-- Form Tambah Checklist -->
                        <div class="flex gap-2">
                            <input
                                type="text"
                                v-model="newChecklistText"
                                @keydown.enter="addChecklist"
                                placeholder="Tulis agenda / tindak lanjut harian..."
                                class="flex-1 px-3.5 py-2 border border-slate-300 rounded-xl text-xs focus:outline-none focus:border-sky-500 transition"
                            />
                            <button
                                @click="addChecklist"
                                class="px-4 py-2 bg-sky-600 hover:bg-sky-700 text-white rounded-xl text-xs font-semibold transition active:scale-95 shrink-0"
                            >
                                Tambah
                            </button>
                        </div>

                        <!-- Daftar Checklist -->
                        <div class="space-y-2 max-h-80 overflow-y-auto pr-1">
                            <div
                                v-for="item in agenda.checklists"
                                :key="item.id"
                                class="flex items-center justify-between p-3 rounded-xl border border-slate-200 bg-slate-50/50 hover:bg-white text-xs transition gap-2"
                            >
                                <label class="flex items-center gap-3 flex-1 cursor-pointer min-w-0">
                                    <input
                                        type="checkbox"
                                        :checked="item.is_done"
                                        @change="toggleChecklist(item)"
                                        class="w-4 h-4 rounded text-sky-600 focus:ring-sky-500 cursor-pointer"
                                    />
                                    <span :class="[item.is_done ? 'line-through text-slate-400' : 'text-slate-800 font-medium', 'truncate']">
                                        {{ item.text }}
                                    </span>
                                </label>
                                <button
                                    @click="deleteChecklist(item)"
                                    class="text-slate-400 hover:text-rose-600 p-1.5 rounded-lg hover:bg-rose-50 transition shrink-0"
                                    title="Hapus"
                                >
                                    <i class="fa-solid fa-trash-can text-xs"></i>
                                </button>
                            </div>

                            <p v-if="!agenda.checklists?.length" class="text-slate-400 text-xs py-8 text-center italic">
                                Belum ada checklist pribadi untuk tanggal ini.
                            </p>
                        </div>
                    </div>

                    <div class="pt-3 border-t border-slate-100 flex items-center justify-between text-[11px] text-slate-500">
                        <span>Checklist tersimpan otomatis di database</span>
                        <span class="font-bold text-slate-700">
                            {{ (agenda.checklists || []).filter(c => c.is_done).length }} / {{ (agenda.checklists || []).length }} Selesai
                        </span>
                    </div>
                </div>

                <!-- Evaluasi Akhir Hari (5 Pertanyaan TL) -->
                <div class="bg-white rounded-2xl border border-slate-200 p-4 sm:p-5 shadow-xs space-y-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-xs sm:text-sm font-bold text-slate-800 flex items-center gap-2">
                                <i class="fa-solid fa-clipboard-question text-amber-500"></i> Evaluasi Akhir Hari (Closing Review)
                            </h3>
                            <p class="text-[11px] text-slate-500">Refleksi harian untuk konsistensi kualitas & progress proyek</p>
                        </div>
                        <span v-if="isSaved" class="text-xs text-emerald-600 font-bold flex items-center gap-1 bg-emerald-50 px-2.5 py-1 rounded-full border border-emerald-200 animate-bounce">
                            <i class="fa-solid fa-check"></i> Tersimpan
                        </span>
                    </div>

                    <form @submit.prevent="submitEvaluation" class="space-y-3.5 text-xs">
                        <div>
                            <label class="font-bold text-slate-700 block mb-1">1. Apa yang berhasil diselesaikan hari ini?</label>
                            <textarea
                                v-model="evalForm.eval_q1"
                                rows="2"
                                class="w-full p-2.5 border border-slate-300 rounded-xl text-xs focus:outline-none focus:border-sky-500 transition"
                                placeholder="Catat pencapaian utama hari ini..."
                            ></textarea>
                        </div>

                        <div>
                            <label class="font-bold text-slate-700 block mb-1">2. Apa yang belum selesai & perlu dilanjutkan?</label>
                            <textarea
                                v-model="evalForm.eval_q2"
                                rows="2"
                                class="w-full p-2.5 border border-slate-300 rounded-xl text-xs focus:outline-none focus:border-sky-500 transition"
                                placeholder="Pekerjaan yang masih menggantung..."
                            ></textarea>
                        </div>

                        <div>
                            <label class="font-bold text-slate-700 block mb-1">3. Apa kendala lapangan / teknis yang dihadapi?</label>
                            <textarea
                                v-model="evalForm.eval_q3"
                                rows="2"
                                class="w-full p-2.5 border border-slate-300 rounded-xl text-xs focus:outline-none focus:border-sky-500 transition"
                                placeholder="Hambatan teknis, data, atau komunikasi..."
                            ></textarea>
                        </div>

                        <div>
                            <label class="font-bold text-slate-700 block mb-1">4. Siapa yang perlu ditindaklanjuti besok pagi?</label>
                            <input
                                type="text"
                                v-model="evalForm.eval_q4"
                                class="w-full p-2.5 border border-slate-300 rounded-xl text-xs focus:outline-none focus:border-sky-500 transition"
                                placeholder="Nama PPK, Klien, Konsultan, atau Tim..."
                            />
                        </div>

                        <div>
                            <label class="font-bold text-slate-700 block mb-1">5. Apa target prioritas paling utama untuk besok?</label>
                            <textarea
                                v-model="evalForm.eval_q5"
                                rows="2"
                                class="w-full p-2.5 border border-slate-300 rounded-xl text-xs focus:outline-none focus:border-sky-500 transition"
                                placeholder="Target kunci esok hari..."
                            ></textarea>
                        </div>

                        <div class="flex justify-end pt-2">
                            <button
                                type="submit"
                                :disabled="evalForm.processing"
                                class="px-5 py-2.5 bg-slate-900 hover:bg-slate-800 text-white rounded-xl text-xs font-semibold flex items-center gap-2 transition active:scale-95 shadow-sm"
                            >
                                <i class="fa-solid fa-floppy-disk"></i>
                                <span>Simpan Evaluasi Hari Ini</span>
                            </button>
                        </div>
                    </form>
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
