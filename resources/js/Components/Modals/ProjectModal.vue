<script setup>
import { ref, watch } from 'vue';
import { useForm } from '@inertiajs/vue3';

const props = defineProps({
    show: Boolean,
    project: Object,
    users: Array,
});

const emit = defineEmits(['close']);
const isEdit = ref(false);

const form = useForm({
    id: null,
    name: '',
    client: '',
    contract_no: '',
    start_date: '',
    deadline: '',
    pic_user_id: '',
    team_members: '',
    status: 'Aktif Berjalan',
    target_progress: 80,
    obstacle: '',
    notes: '',
});

watch(() => props.project, (val) => {
    if (val && val.id) {
        isEdit.value = true;
        form.id = val.id;
        form.name = val.name || '';
        form.client = val.client || '';
        form.contract_no = val.contract_no || '';
        form.start_date = val.start_date || '';
        form.deadline = val.deadline || '';
        form.pic_user_id = val.pic_user_id || '';
        form.team_members = val.team_members || '';
        form.status = val.status || 'Aktif Berjalan';
        form.target_progress = val.target_progress || 80;
        form.obstacle = val.obstacle || '';
        form.notes = val.notes || '';
    } else {
        isEdit.value = false;
        form.reset();
        form.status = 'Aktif Berjalan';
        form.target_progress = 80;
        form.pic_user_id = props.users?.[0]?.id || '';
    }
}, { immediate: true });

function submit() {
    if (isEdit.value) {
        form.put(route('projects.update', form.id), {
            preserveScroll: true,
            onSuccess: () => emit('close'),
        });
    } else {
        form.post(route('projects.store'), {
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
        <div class="bg-white rounded-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto shadow-2xl border border-slate-200">
            <div class="px-6 py-4 bg-slate-900 text-white flex items-center justify-between sticky top-0 z-10">
                <h3 class="text-sm font-bold flex items-center gap-2">
                    <i class="fa-solid fa-folder-open text-sky-400"></i>
                    {{ isEdit ? 'Edit Proyek' : 'Tambah Proyek Baru' }}
                </h3>
                <button @click="$emit('close')" class="text-slate-400 hover:text-white">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            <form @submit.prevent="submit" class="p-6 space-y-4 text-xs">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="md:col-span-2">
                        <label class="font-bold text-slate-700 block mb-1">Nama Proyek *</label>
                        <input
                            type="text"
                            v-model="form.name"
                            required
                            class="w-full p-2.5 border border-slate-300 rounded-lg text-xs"
                            placeholder="Contoh: Perencanaan DED Gedung Serbaguna (15x20 m)"
                        />
                    </div>

                    <div>
                        <label class="font-bold text-slate-700 block mb-1">Nama Klien / Instansi *</label>
                        <input
                            type="text"
                            v-model="form.client"
                            required
                            class="w-full p-2.5 border border-slate-300 rounded-lg text-xs"
                            placeholder="Contoh: Dinas Kesehatan Kab. Cirebon"
                        />
                    </div>

                    <div>
                        <label class="font-bold text-slate-700 block mb-1">Nomor Kontrak / SPK</label>
                        <input
                            type="text"
                            v-model="form.contract_no"
                            class="w-full p-2.5 border border-slate-300 rounded-lg text-xs"
                            placeholder="Contoh: 602.1/SPK-PRC/2026"
                        />
                    </div>

                    <div>
                        <label class="font-bold text-slate-700 block mb-1">Tanggal Mulai *</label>
                        <input type="date" v-model="form.start_date" required class="w-full p-2.5 border border-slate-300 rounded-lg text-xs" />
                    </div>

                    <div>
                        <label class="font-bold text-slate-700 block mb-1">Deadline Proyek *</label>
                        <input type="date" v-model="form.deadline" required class="w-full p-2.5 border border-slate-300 rounded-lg text-xs" />
                    </div>

                    <div>
                        <label class="font-bold text-slate-700 block mb-1">PIC Utama Proyek *</label>
                        <select v-model="form.pic_user_id" required class="w-full p-2.5 border border-slate-300 rounded-lg bg-white text-xs">
                            <option value="" disabled>Pilih PIC...</option>
                            <option v-for="u in users" :key="u.id" :value="u.id">{{ u.name }} ({{ u.role }})</option>
                        </select>
                    </div>

                    <div>
                        <label class="font-bold text-slate-700 block mb-1">Anggota Tim Terlibat</label>
                        <input
                            type="text"
                            v-model="form.team_members"
                            class="w-full p-2.5 border border-slate-300 rounded-lg text-xs"
                            placeholder="Contoh: Hendra Wijaya, Deden Pratama, Rina"
                        />
                    </div>

                    <div>
                        <label class="font-bold text-slate-700 block mb-1">Status Proyek *</label>
                        <select v-model="form.status" class="w-full p-2.5 border border-slate-300 rounded-lg bg-white text-xs">
                            <option value="Aktif Berjalan">Aktif Berjalan</option>
                            <option value="Menunggu Review Klien">Menunggu Review Klien</option>
                            <option value="Terhambat">Terhambat</option>
                            <option value="Selesai Teknis">Selesai Teknis</option>
                            <option value="Selesai & Lunas">Selesai & Lunas</option>
                        </select>
                    </div>

                    <div>
                        <label class="font-bold text-slate-700 block mb-1">Target Progress Saat Ini (%) *</label>
                        <input
                            type="number"
                            v-model="form.target_progress"
                            min="0"
                            max="100"
                            required
                            class="w-full p-2.5 border border-slate-300 rounded-lg text-xs"
                        />
                    </div>

                    <div class="md:col-span-2">
                        <label class="font-bold text-slate-700 block mb-1">Kendala Utama Proyek</label>
                        <input
                            type="text"
                            v-model="form.obstacle"
                            class="w-full p-2.5 border border-slate-300 rounded-lg text-xs"
                            placeholder="Kosongkan jika proyek berjalan lancar..."
                        />
                    </div>

                    <div class="md:col-span-2">
                        <label class="font-bold text-slate-700 block mb-1">Catatan Team Leader & Arahan</label>
                        <textarea
                            v-model="form.notes"
                            rows="2"
                            class="w-full p-2.5 border border-slate-300 rounded-lg text-xs"
                            placeholder="Arahan khusus percepatan atau catatan spesifikasi..."
                        ></textarea>
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
                        class="px-5 py-2 bg-sky-600 hover:bg-sky-700 text-white rounded-lg font-semibold flex items-center gap-1.5"
                    >
                        <i class="fa-solid fa-floppy-disk"></i>
                        {{ isEdit ? 'Simpan Perubahan' : 'Simpan Proyek' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>
