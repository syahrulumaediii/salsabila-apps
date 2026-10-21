<script setup>
import { ref, watch, computed } from 'vue';
import { useForm } from '@inertiajs/vue3';

const defaultDivisions = [
    'Arsitektur',
    'Struktur',
    'MEP',
    'Estimasi & RAB',
    'Survey & Pengawasan',
    'Administrasi & Kontrak',
];

const props = defineProps({
    show: Boolean,
    task: Object,
    projects: {
        type: Array,
        default: () => [],
    },
    users: {
        type: Array,
        default: () => [],
    },
    divisions: {
        type: Array,
        default: () => [
            'Arsitektur',
            'Struktur',
            'MEP',
            'Estimasi & RAB',
            'Survey & Pengawasan',
            'Administrasi & Kontrak',
        ],
    },
});

const divisionList = computed(() => {
    if (Array.isArray(props.divisions) && props.divisions.length > 0) {
        return props.divisions;
    }
    return defaultDivisions;
});

const emit = defineEmits(['close']);

const isEdit = ref(false);

const form = useForm({
    id: null,
    name: '',
    project_id: '',
    stage: 'B. PERENCANAAN',
    sub_stage: 'Perhitungan struktur',
    divisi: 'Arsitektur',
    pic_user_id: '',
    support_team: '',
    start_date: '',
    deadline: '',
    priority: 'Sedang',
    status: 'Belum Dimulai',
    weight: 10,
    manual_progress_mode: false,
    progress: 0,
    obstacle: '',
    notes: '',
    next_action: '',
    need_decision: false,
    client_follow_up: false,
    subtasks_text: '',
});

watch(() => props.task, (val) => {
    if (val && val.id) {
        isEdit.value = true;
        form.id = val.id;
        form.name = val.name || '';
        form.project_id = val.project_id || '';
        form.stage = val.stage || 'B. PERENCANAAN';
        form.sub_stage = val.sub_stage || '';
        form.divisi = val.divisi || 'Arsitektur';
        form.pic_user_id = val.pic_user_id || '';
        form.support_team = val.support_team || '';
        form.start_date = val.start_date || '';
        form.deadline = val.deadline || '';
        form.priority = val.priority || 'Sedang';
        form.status = val.status || 'Belum Dimulai';
        form.weight = val.weight || 10;
        form.manual_progress_mode = Boolean(val.manual_progress_mode);
        form.progress = val.progress || 0;
        form.obstacle = val.obstacle || '';
        form.notes = val.notes || '';
        form.next_action = val.next_action || '';
        form.need_decision = Boolean(val.need_decision);
        form.client_follow_up = Boolean(val.client_follow_up);
        form.subtasks_text = val.subtasks ? val.subtasks.map(s => s.title).join('\n') : '';
    } else {
        isEdit.value = false;
        form.reset();
        form.project_id = props.projects?.[0]?.id || '';
        form.weight = 10;
        form.priority = 'Sedang';
        form.status = 'Belum Dimulai';
        form.stage = 'B. PERENCANAAN';
        form.divisi = 'Arsitektur';
    }
}, { immediate: true });

function onPicChange() {
    if (!form.pic_user_id) return;
    const selected = (props.users || []).find(u => u.id == form.pic_user_id);
    if (selected && selected.divisi) {
        form.divisi = selected.divisi;
    }
}

function submit() {
    if (isEdit.value) {
        form.put(route('tasks.update', form.id), {
            preserveScroll: true,
            onSuccess: () => emit('close'),
        });
    } else {
        form.post(route('tasks.store'), {
            preserveScroll: true,
            onSuccess: () => emit('close'),
        });
    }
}
</script>

<template>
    <div
        v-if="show"
        class="fixed inset-0 modal-backdrop z-50 flex items-center justify-center p-4 overflow-y-auto no-print"
    >
        <div class="bg-white rounded-2xl max-w-3xl w-full max-h-[92vh] flex flex-col shadow-2xl border border-slate-200 overflow-hidden">
            <div class="px-5 sm:px-6 py-3.5 sm:py-4 bg-slate-900 text-white flex items-center justify-between shrink-0">
                <h3 class="text-xs sm:text-sm font-bold flex items-center gap-2">
                    <i class="fa-solid fa-list-check text-sky-400"></i>
                    {{ isEdit ? 'Edit Pekerjaan' : 'Tambah Pekerjaan Baru' }}
                </h3>
                <button
                    @click="$emit('close')"
                    class="p-1 rounded-lg text-slate-400 hover:text-white hover:bg-slate-800 transition"
                >
                    <i class="fa-solid fa-xmark text-base sm:text-lg"></i>
                </button>
            </div>

            <form @submit.prevent="submit" class="flex-1 overflow-y-auto p-4 sm:p-6 space-y-4 text-xs">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="md:col-span-2">
                        <label class="font-bold text-slate-700 block mb-1">Nama Pekerjaan *</label>
                        <input
                            type="text"
                            v-model="form.name"
                            required
                            class="w-full p-2.5 border border-slate-300 rounded-lg text-xs"
                            placeholder="Contoh: Analisis Struktur Portal Baja WF & Pondasi Gedung 15x20m"
                        />
                    </div>

                    <div>
                        <label class="font-bold text-slate-700 block mb-1">Proyek *</label>
                        <select v-model="form.project_id" required class="w-full p-2.5 border border-slate-300 rounded-lg bg-white text-xs">
                            <option value="" disabled>Pilih Proyek...</option>
                            <option v-for="p in projects" :key="p.id" :value="p.id">
                                {{ p.code ? `[${p.code}] ` : '' }}{{ p.name }}
                            </option>
                        </select>
                    </div>

                    <div>
                        <label class="font-bold text-slate-700 block mb-1">Tahapan Bisnis Konsultan *</label>
                        <select v-model="form.stage" required class="w-full p-2.5 border border-slate-300 rounded-lg bg-white text-xs">
                            <option value="A. KONTRAK">A. KONTRAK</option>
                            <option value="B. PERENCANAAN">B. PERENCANAAN</option>
                            <option value="C. PELAKSANAAN / PENGAWASAN">C. PELAKSANAAN / PENGAWASAN</option>
                            <option value="D. PENAGIHAN">D. PENAGIHAN</option>
                        </select>
                    </div>

                    <div>
                        <label class="font-bold text-slate-700 block mb-1">Sub-Tahapan</label>
                        <input
                            type="text"
                            v-model="form.sub_stage"
                            placeholder="Contoh: Perhitungan struktur / RAB & AHSP"
                            class="w-full p-2.5 border border-slate-300 rounded-lg text-xs"
                        />
                    </div>

                    <div>
                        <label class="font-bold text-slate-700 block mb-1">Divisi *</label>
                        <select v-model="form.divisi" required class="w-full p-2.5 border border-slate-300 rounded-lg bg-white text-xs">
                            <option v-for="d in divisionList" :key="d" :value="d">{{ d }}</option>
                        </select>
                    </div>

                    <div>
                        <label class="font-bold text-slate-700 block mb-1">PIC Utama *</label>
                        <select
                            v-model="form.pic_user_id"
                            @change="onPicChange"
                            class="w-full p-2.5 border border-slate-300 rounded-lg bg-white text-xs"
                        >
                            <option value="">-- Belum Ditentukan --</option>
                            <option v-for="u in users" :key="u.id" :value="u.id">{{ u.name }} ({{ u.role }})</option>
                        </select>
                    </div>

                    <div>
                        <label class="font-bold text-slate-700 block mb-1">Anggota Pendukung (Opsional)</label>
                        <input
                            type="text"
                            v-model="form.support_team"
                            placeholder="Contoh: Deden Pratama (Drafter)"
                            class="w-full p-2.5 border border-slate-300 rounded-lg text-xs"
                        />
                    </div>

                    <div>
                        <label class="font-bold text-slate-700 block mb-1">Tanggal Mulai</label>
                        <input type="date" v-model="form.start_date" class="w-full p-2.5 border border-slate-300 rounded-lg text-xs" />
                    </div>

                    <div>
                        <label class="font-bold text-slate-700 block mb-1">Deadline *</label>
                        <input type="date" v-model="form.deadline" class="w-full p-2.5 border border-slate-300 rounded-lg text-xs" />
                    </div>

                    <div>
                        <label class="font-bold text-slate-700 block mb-1">Prioritas *</label>
                        <select v-model="form.priority" class="w-full p-2.5 border border-slate-300 rounded-lg bg-white text-xs">
                            <option value="Kritis">Kritis</option>
                            <option value="Tinggi">Tinggi</option>
                            <option value="Sedang">Sedang</option>
                            <option value="Rendah">Rendah</option>
                        </select>
                    </div>

                    <div>
                        <label class="font-bold text-slate-700 block mb-1">Status Pekerjaan *</label>
                        <select v-model="form.status" class="w-full p-2.5 border border-slate-300 rounded-lg bg-white text-xs">
                            <option value="Belum Dimulai">Belum Dimulai</option>
                            <option value="Sedang Dikerjakan">Sedang Dikerjakan</option>
                            <option value="Menunggu Pemeriksaan">Menunggu Pemeriksaan</option>
                            <option value="Revisi">Revisi</option>
                            <option value="Terhambat">Terhambat</option>
                            <option value="Selesai">Selesai</option>
                        </select>
                    </div>

                    <div>
                        <label class="font-bold text-slate-700 block mb-1">Bobot dalam Proyek (1 - 100) *</label>
                        <input
                            type="number"
                            v-model="form.weight"
                            min="1"
                            max="100"
                            required
                            class="w-full p-2.5 border border-slate-300 rounded-lg text-xs"
                        />
                    </div>

                    <div>
                        <label class="font-bold text-slate-700 block mb-1">Mode Progress</label>
                        <div class="flex items-center gap-3 mt-1">
                            <label class="flex items-center gap-1.5 cursor-pointer">
                                <input type="checkbox" v-model="form.manual_progress_mode" class="rounded text-sky-600" />
                                <span>Progress Manual (%)</span>
                            </label>
                            <input
                                type="number"
                                v-model="form.progress"
                                :disabled="!form.manual_progress_mode"
                                min="0"
                                max="100"
                                class="w-20 p-1.5 border border-slate-300 rounded-lg text-center text-xs"
                            />
                        </div>
                    </div>

                    <div class="md:col-span-2">
                        <label class="font-bold text-slate-700 block mb-1">Sub-Pekerjaan / Checklist Teknis (Pisahkan dengan baris baru)</label>
                        <textarea
                            v-model="form.subtasks_text"
                            rows="3"
                            class="w-full p-2.5 border border-slate-300 rounded-lg font-mono text-[11px]"
                            placeholder="Pemodelan 3D di SAP2000&#10;Analisis beban gempa SNI&#10;Cetak nota perhitungan final"
                        ></textarea>
                        <p class="text-[11px] text-slate-400 mt-0.5">Jika Mode Manual tidak dicentang, progress otomatis dihitung dari persentase checklist yang selesai.</p>
                    </div>

                    <div>
                        <label class="font-bold text-slate-700 block mb-1">Kendala Saat Ini</label>
                        <input
                            type="text"
                            v-model="form.obstacle"
                            class="w-full p-2.5 border border-slate-300 rounded-lg text-xs"
                            placeholder="Kosongkan jika tidak ada kendala..."
                        />
                    </div>

                    <div>
                        <label class="font-bold text-slate-700 block mb-1">Tindakan Selanjutnya (Next Action)</label>
                        <input
                            type="text"
                            v-model="form.next_action"
                            class="w-full p-2.5 border border-slate-300 rounded-lg text-xs"
                            placeholder="Langkah konkret berikutnya..."
                        />
                    </div>

                    <div class="md:col-span-2">
                        <label class="font-bold text-slate-700 block mb-1">Catatan Arahan Team Leader</label>
                        <textarea
                            v-model="form.notes"
                            rows="2"
                            class="w-full p-2.5 border border-slate-300 rounded-lg text-xs"
                            placeholder="Instruksi khusus atau standar teknis..."
                        ></textarea>
                    </div>

                    <div class="md:col-span-2 flex flex-wrap gap-4 pt-2 border-t border-slate-100">
                        <label class="flex items-center gap-2 cursor-pointer font-medium text-slate-700">
                            <input type="checkbox" v-model="form.need_decision" class="rounded text-rose-600" />
                            <span>Butuh Keputusan / Bantuan Team Leader Segera</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer font-medium text-slate-700">
                            <input type="checkbox" v-model="form.client_follow_up" class="rounded text-sky-600" />
                            <span>Perlu Tindak Lanjut ke Owner / Instansi / Klien</span>
                        </label>
                    </div>
                </div>

                <div class="sticky bottom-0 bg-white/95 backdrop-blur-xs py-3.5 px-4 sm:px-6 -mx-4 sm:-mx-6 -mb-4 sm:-mb-6 border-t border-slate-200 flex items-center justify-end gap-2.5 z-10 shadow-xs">
                    <button
                        type="button"
                        @click="$emit('close')"
                        class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl font-semibold transition active:scale-95"
                    >
                        Batal
                    </button>
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="px-5 py-2 bg-sky-600 hover:bg-sky-700 text-white rounded-xl font-semibold flex items-center gap-1.5 transition shadow-sm active:scale-95"
                    >
                        <i class="fa-solid fa-floppy-disk"></i>
                        {{ isEdit ? 'Simpan Perubahan' : 'Simpan Pekerjaan' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>
