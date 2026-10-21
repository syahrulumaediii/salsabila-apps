<script setup>
import { ref, watch } from 'vue';
import { useForm } from '@inertiajs/vue3';

const props = defineProps({
    show: Boolean,
    member: Object,
    divisions: Array,
});

const emit = defineEmits(['close']);
const isEdit = ref(false);

const form = useForm({
    id: null,
    name: '',
    email: '',
    role: '',
    divisi: 'Arsitektur',
    eval_notes: '',
});

watch(() => props.member, (val) => {
    if (val && val.id) {
        isEdit.value = true;
        form.id = val.id;
        form.name = val.name || '';
        form.email = val.email || '';
        form.role = val.role || '';
        form.divisi = val.divisi || 'Arsitektur';
        form.eval_notes = val.eval_notes || '';
    } else {
        isEdit.value = false;
        form.reset();
        form.divisi = 'Arsitektur';
    }
}, { immediate: true });

function submit() {
    if (isEdit.value) {
        form.put(route('team.update', form.id), {
            preserveScroll: true,
            onSuccess: () => emit('close'),
        });
    } else {
        form.post(route('team.store'), {
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
        <div class="bg-white rounded-2xl max-w-lg w-full shadow-2xl border border-slate-200">
            <div class="px-6 py-4 bg-slate-900 text-white flex items-center justify-between sticky top-0 z-10">
                <h3 class="text-sm font-bold flex items-center gap-2">
                    <i class="fa-solid fa-user text-sky-400"></i>
                    {{ isEdit ? 'Edit Anggota Tim' : 'Tambah Anggota Tim Baru' }}
                </h3>
                <button @click="$emit('close')" class="text-slate-400 hover:text-white">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            <form @submit.prevent="submit" class="p-6 space-y-4 text-xs">
                <div>
                    <label class="font-bold text-slate-700 block mb-1">Nama Lengkap & Gelar *</label>
                    <input
                        type="text"
                        v-model="form.name"
                        required
                        class="w-full p-2.5 border border-slate-300 rounded-lg text-xs"
                        placeholder="Contoh: Ir. Hendra Wijaya, S.T."
                    />
                </div>

                <div>
                    <label class="font-bold text-slate-700 block mb-1">Email *</label>
                    <input
                        type="email"
                        v-model="form.email"
                        required
                        class="w-full p-2.5 border border-slate-300 rounded-lg text-xs"
                        placeholder="Contoh: hendra@salsabila.com"
                    />
                </div>

                <div>
                    <label class="font-bold text-slate-700 block mb-1">Jabatan / Peran *</label>
                    <input
                        type="text"
                        v-model="form.role"
                        required
                        class="w-full p-2.5 border border-slate-300 rounded-lg text-xs"
                        placeholder="Contoh: Tenaga Ahli Struktur / Principal Architect"
                    />
                </div>

                <div>
                    <label class="font-bold text-slate-700 block mb-1">Divisi *</label>
                    <select v-model="form.divisi" required class="w-full p-2.5 border border-slate-300 rounded-lg bg-white text-xs">
                        <option v-for="d in divisions" :key="d" :value="d">{{ d }}</option>
                    </select>
                </div>

                <div>
                    <label class="font-bold text-slate-700 block mb-1">Catatan Evaluasi Objektif Team Leader</label>
                    <textarea
                        v-model="form.eval_notes"
                        rows="3"
                        class="w-full p-2.5 border border-slate-300 rounded-lg text-xs"
                        placeholder="Catat evaluasi ketepatan waktu, ketelitian gambar/perhitungan, dan koordinasi tim..."
                    ></textarea>
                </div>

                <div class="flex justify-end gap-2 pt-3 border-t border-slate-200">
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
                        {{ isEdit ? 'Simpan Perubahan' : 'Simpan Anggota' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>
