<script setup>
import { ref, watch } from 'vue';
import { useForm } from '@inertiajs/vue3';

const props = defineProps({
    show: Boolean,
    issue: Object,
    projects: Array,
    users: Array,
    categories: Array,
});

const emit = defineEmits(['close']);
const isEdit = ref(false);

const form = useForm({
    id: null,
    project_id: '',
    category: 'Menunggu keputusan Team Leader',
    problem: '',
    reporter_user_id: '',
    urgency: 'Mendesak (Blokir Progress)',
    impact: '',
    decision_needed: '',
    solution: '',
    target_date: '',
    status: 'Menunggu Keputusan TL',
});

watch(() => props.issue, (val) => {
    if (val && val.id) {
        isEdit.value = true;
        form.id = val.id;
        form.project_id = val.project_id || '';
        form.category = val.category || 'Menunggu keputusan Team Leader';
        form.problem = val.problem || '';
        form.reporter_user_id = val.reporter_user_id || '';
        form.urgency = val.urgency || 'Mendesak (Blokir Progress)';
        form.impact = val.impact || '';
        form.decision_needed = val.decision_needed || '';
        form.solution = val.solution || '';
        form.target_date = val.target_date || '';
        form.status = val.status || 'Menunggu Keputusan TL';
    } else {
        isEdit.value = false;
        form.reset();
        form.project_id = props.projects?.[0]?.id || '';
        form.reporter_user_id = props.users?.[0]?.id || '';
        form.category = 'Menunggu keputusan Team Leader';
        form.urgency = 'Mendesak (Blokir Progress)';
        form.status = 'Menunggu Keputusan TL';
    }
}, { immediate: true });

function submit() {
    if (isEdit.value) {
        form.put(route('issues.update', form.id), {
            preserveScroll: true,
            onSuccess: () => emit('close'),
        });
    } else {
        form.post(route('issues.store'), {
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
        <div class="bg-white rounded-2xl max-w-2xl w-full max-h-[92vh] overflow-y-auto shadow-2xl border border-slate-200">
            <div class="px-6 py-4 bg-slate-900 text-white flex items-center justify-between sticky top-0 z-10">
                <h3 class="text-sm font-bold flex items-center gap-2">
                    <i class="fa-solid fa-triangle-exclamation text-rose-500"></i>
                    {{ isEdit ? 'Edit Kendala / Keputusan' : 'Catat Kendala & Keputusan Team Leader' }}
                </h3>
                <button @click="$emit('close')" class="text-slate-400 hover:text-white">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            <form @submit.prevent="submit" class="p-6 space-y-4 text-xs">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="font-bold text-slate-700 block mb-1">Proyek *</label>
                        <select v-model="form.project_id" required class="w-full p-2.5 border border-slate-300 rounded-lg bg-white text-xs">
                            <option value="" disabled>Pilih Proyek...</option>
                            <option v-for="p in projects" :key="p.id" :value="p.id">{{ p.name }}</option>
                        </select>
                    </div>

                    <div>
                        <label class="font-bold text-slate-700 block mb-1">Kategori Kendala *</label>
                        <select v-model="form.category" required class="w-full p-2.5 border border-slate-300 rounded-lg bg-white text-xs">
                            <option v-for="cat in categories" :key="cat" :value="cat">{{ cat }}</option>
                        </select>
                    </div>

                    <div class="md:col-span-2">
                        <label class="font-bold text-slate-700 block mb-1">Deskripsi Permasalahan *</label>
                        <input
                            type="text"
                            v-model="form.problem"
                            required
                            class="w-full p-2.5 border border-slate-300 rounded-lg text-xs"
                            placeholder="Contoh: Perubahan spesifikasi penutup atap UPVC vs Spandek"
                        />
                    </div>

                    <div>
                        <label class="font-bold text-slate-700 block mb-1">PIC Pelapor *</label>
                        <select v-model="form.reporter_user_id" required class="w-full p-2.5 border border-slate-300 rounded-lg bg-white text-xs">
                            <option value="" disabled>Pilih Pelapor...</option>
                            <option v-for="u in users" :key="u.id" :value="u.id">{{ u.name }}</option>
                        </select>
                    </div>

                    <div>
                        <label class="font-bold text-slate-700 block mb-1">Tingkat Urgensi & Dampak *</label>
                        <select v-model="form.urgency" required class="w-full p-2.5 border border-slate-300 rounded-lg bg-white text-xs">
                            <option value="Mendesak (Blokir Progress)">Mendesak (Blokir Progress)</option>
                            <option value="Tinggi (Berpotensi Terlambat)">Tinggi (Berpotensi Terlambat)</option>
                            <option value="Sedang">Sedang</option>
                        </select>
                    </div>

                    <div class="md:col-span-2">
                        <label class="font-bold text-slate-700 block mb-1">Dampak Terhadap Pekerjaan</label>
                        <input
                            type="text"
                            v-model="form.impact"
                            class="w-full p-2.5 border border-slate-300 rounded-lg text-xs"
                            placeholder="Contoh: Finalisasi nota hitungan struktur SAP2000 tertunda 2 hari"
                        />
                    </div>

                    <div class="md:col-span-2">
                        <label class="font-bold text-slate-700 block mb-1">Keputusan yang Dibutuhkan dari Team Leader</label>
                        <input
                            type="text"
                            v-model="form.decision_needed"
                            class="w-full p-2.5 border border-slate-300 rounded-lg text-xs"
                            placeholder="Contoh: Penguncian opsi profil gording dan merk penutup atap"
                        />
                    </div>

                    <div class="md:col-span-2">
                        <label class="font-bold text-slate-700 block mb-1">Solusi / Arahan Tindakan Team Leader</label>
                        <textarea
                            v-model="form.solution"
                            rows="2"
                            class="w-full p-2.5 border border-slate-300 rounded-lg text-xs"
                            placeholder="Tuliskan arahan resmi Team Leader kepada PIC..."
                        ></textarea>
                    </div>

                    <div>
                        <label class="font-bold text-slate-700 block mb-1">Target Penyelesaian *</label>
                        <input type="date" v-model="form.target_date" required class="w-full p-2.5 border border-slate-300 rounded-lg text-xs" />
                    </div>

                    <div>
                        <label class="font-bold text-slate-700 block mb-1">Status Tindak Lanjut *</label>
                        <select v-model="form.status" required class="w-full p-2.5 border border-slate-300 rounded-lg bg-white text-xs">
                            <option value="Menunggu Keputusan TL">Menunggu Keputusan TL</option>
                            <option value="Sedang Ditindaklanjuti">Sedang Ditindaklanjuti</option>
                            <option value="Menunggu Konfirmasi Instansi">Menunggu Konfirmasi Instansi</option>
                            <option value="Selesai Teratasi">Selesai Teratasi</option>
                        </select>
                    </div>
                </div>

                <div class="flex justify-end gap-2 pt-4 border-t border-slate-200">
                    <button
                        type="button"
                        @click="$emit('close')"
                        class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg font-semibold"
                    >
                        Batal
                    </button>
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="px-5 py-2 bg-rose-600 hover:bg-rose-700 text-white rounded-lg font-semibold flex items-center gap-1.5"
                    >
                        <i class="fa-solid fa-floppy-disk"></i>
                        {{ isEdit ? 'Simpan Perubahan' : 'Simpan Data Kendala' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>
