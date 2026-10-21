<script setup>
import { ref } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import StatusBadge from '@/Components/Common/StatusBadge.vue';
import PriorityBadge from '@/Components/Common/PriorityBadge.vue';
import ProgressBar from '@/Components/Common/ProgressBar.vue';

const props = defineProps({
    tasks: Array,
    mode: String,
    defaultQcItems: Array,
    sidebarBadges: Object,
});

const activeMode = ref(props.mode);

function onModeChange() {
    router.get(route('review.index'), { mode: activeMode.value }, { preserveState: true });
}

function toggleQcCheck(task, index) {
    router.post(route('review.toggleQc', task.id), { index }, { preserveScroll: true });
}

const noteInputs = ref({});

function approveQc(task) {
    const notes = noteInputs.value[task.id] || task.qc_review?.qc_notes || '';
    router.post(route('review.approve', task.id), { qc_notes: notes }, { preserveScroll: true });
}

function rejectQc(task) {
    const notes = (noteInputs.value[task.id] || '').trim();
    if (!notes) {
        alert('Wajib mengisi catatan revisi sebelum mengembalikan pekerjaan ke PIC!');
        return;
    }
    router.post(route('review.reject', task.id), { qc_notes: notes }, { preserveScroll: true });
}

function formatDateID(dateStr) {
    if (!dateStr) return '-';
    const d = new Date(dateStr + 'T00:00:00');
    return d.toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' });
}
</script>

<template>
    <AppLayout
        title="Pemeriksaan & Revisi (QC Gate)"
        subtitle="Verifikasi Checklist Dokumen, Gambar, Perhitungan, dan RAB Sebelum Disetujui Selesai"
        :badges="sidebarBadges"
    >
        <template #header-actions>
            <div class="flex items-center gap-2">
                <span class="text-xs font-bold px-3 py-1.5 bg-purple-100 text-purple-800 rounded-lg border border-purple-200">
                    <i class="fa-solid fa-clipboard-check mr-1"></i> {{ tasks.length }} Dokumen QC
                </span>
            </div>
        </template>

        <Head title="Pemeriksaan & Revisi — Salsabila Control Center" />

        <div class="space-y-6">
            <!-- Header Filter Bar -->
            <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm flex flex-wrap items-center justify-between gap-4">
                <div>
                    <h3 class="text-sm font-bold text-slate-800">Meja Quality Control (QC Gate)</h3>
                    <p class="text-xs text-slate-500">
                        Pekerjaan tidak sah berstatus <strong>Selesai</strong> sebelum lulus verifikasi checklist mutu dan disetujui Team Leader.
                    </p>
                </div>
                <div>
                    <select
                        v-model="activeMode"
                        @change="onModeChange"
                        class="px-3 py-1.5 border border-slate-300 rounded-lg text-xs bg-white font-medium"
                    >
                        <option value="NEED_REVIEW">Menunggu Pemeriksaan & Revisi</option>
                        <option value="ALL">Seluruh Pekerjaan (Audit QC)</option>
                    </select>
                </div>
            </div>

            <!-- List of QC Review Cards -->
            <div class="space-y-4">
                <div
                    v-for="task in tasks"
                    :key="task.id"
                    class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm space-y-4 hover:shadow-md transition"
                >
                    <div class="flex flex-wrap items-start justify-between gap-3">
                        <div>
                            <div class="flex flex-wrap items-center gap-2 mb-1">
                                <span class="px-2 py-0.5 bg-slate-900 text-white rounded text-[10px] font-mono font-bold">
                                    {{ task.code || 'T' }}
                                </span>
                                <span class="text-xs font-semibold text-slate-600">{{ task.project?.name }}</span>
                                <span class="text-xs text-slate-400">&bull; Divisi {{ task.divisi }}</span>
                            </div>
                            <h3 class="text-base font-bold text-slate-900 leading-snug">{{ task.name }}</h3>
                            <p class="text-xs text-slate-500 mt-0.5">
                                PIC: <strong>{{ task.pic?.name || 'Belum Ditentukan' }}</strong> &bull; Deadline: {{ formatDateID(task.deadline) }}
                            </p>
                        </div>

                        <div class="flex items-center gap-2">
                            <StatusBadge :status="task.status" />
                            <PriorityBadge :priority="task.priority" />
                        </div>
                    </div>

                    <!-- 7 Standard QC Checklist Items -->
                    <div class="bg-slate-50 p-4 rounded-xl border border-slate-200/80 space-y-3">
                        <div class="flex items-center justify-between text-xs">
                            <h4 class="font-bold text-slate-800 uppercase tracking-wider text-[11px] flex items-center gap-1.5">
                                <i class="fa-solid fa-clipboard-check text-purple-600"></i>
                                Standar 7 Verifikasi Mutu Konsultan
                            </h4>
                            <span class="text-[11px] text-purple-700 font-semibold">
                                Centang item yang sudah diverifikasi Team Leader
                            </span>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-2 text-xs">
                            <label
                                v-for="(item, idx) in defaultQcItems"
                                :key="idx"
                                class="flex items-center gap-2.5 p-2 rounded-lg bg-white border border-slate-200/70 hover:border-purple-300 cursor-pointer transition select-none"
                            >
                                <input
                                    type="checkbox"
                                    :checked="task.qc_review?.qc_checklist?.[idx]"
                                    @change="toggleQcCheck(task, idx)"
                                    class="rounded text-purple-600 focus:ring-purple-500"
                                />
                                <span :class="[task.qc_review?.qc_checklist?.[idx] ? 'text-slate-900 font-semibold' : 'text-slate-600']">
                                    {{ idx + 1 }}. {{ item }}
                                </span>
                            </label>
                        </div>
                    </div>

                    <!-- QC Notes & Reviewer Info -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs pt-1">
                        <div>
                            <label class="font-bold text-slate-700 block mb-1">Catatan Pemeriksaan / Arahan Revisi:</label>
                            <textarea
                                v-model="noteInputs[task.id]"
                                rows="2"
                                class="w-full p-2.5 border border-slate-300 rounded-lg text-xs"
                                :placeholder="task.qc_review?.qc_notes || 'Tulis catatan perbaikan atau instruksi revisi detail untuk PIC...'"
                            ></textarea>
                            <span v-if="task.qc_review?.reviewer" class="text-[10px] text-slate-400 mt-1 block">
                                Pemeriksa Terakhir: {{ task.qc_review.reviewer.name }} ({{ formatDateID(task.qc_review.review_date) }})
                            </span>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex flex-col justify-end gap-2">
                            <div class="flex items-center justify-end gap-2">
                                <button
                                    @click="rejectQc(task)"
                                    class="px-4 py-2 bg-amber-500 hover:bg-amber-600 text-slate-950 font-bold rounded-lg text-xs flex items-center gap-1.5 transition"
                                >
                                    <i class="fa-solid fa-rotate-left"></i> Kembalikan Revisi
                                </button>
                                <button
                                    @click="approveQc(task)"
                                    class="px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-lg text-xs flex items-center gap-1.5 transition shadow-sm"
                                >
                                    <i class="fa-solid fa-check-double"></i> Setujui Selesai 100%
                                </button>
                            </div>
                            <p class="text-[10px] text-slate-400 text-right">
                                Persetujuan akan menandai tugas selesai dan memperbarui Weighted Progress proyek secara otomatis.
                            </p>
                        </div>
                    </div>
                </div>

                <div v-if="tasks.length === 0" class="bg-white rounded-xl border border-slate-200 p-12 text-center text-slate-400 text-xs">
                    <i class="fa-solid fa-clipboard-check text-3xl mb-2 text-purple-400 block"></i>
                    Tidak ada pekerjaan yang membutuhkan pemeriksaan saat ini.
                </div>
            </div>
        </div>
    </AppLayout>
</template>
