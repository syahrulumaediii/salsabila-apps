<script setup>
import { ref, watch } from 'vue';
import { useForm } from '@inertiajs/vue3';

const props = defineProps({
    show: Boolean,
    billing: Object,
    projects: Array,
    users: Array,
});

const emit = defineEmits(['close']);
const isEdit = ref(false);

const form = useForm({
    id: null,
    project_id: '',
    client: '',
    contract_val: 0,
    term: '',
    invoice_val: 0,
    paid_val: 0,
    submit_date: '',
    doc_status: 'Penyusunan BAST & Berkas',
    pay_status: 'Belum Diajukan',
    pic_user_id: '',
    follow_up_date: '',
});

watch(() => props.billing, (val) => {
    if (val && val.id) {
        isEdit.value = true;
        form.id = val.id;
        form.project_id = val.project_id || '';
        form.client = val.client || '';
        form.contract_val = val.contract_val || 0;
        form.term = val.term || '';
        form.invoice_val = val.invoice_val || 0;
        form.paid_val = val.paid_val || 0;
        form.submit_date = val.submit_date || '';
        form.doc_status = val.doc_status || 'Penyusunan BAST & Berkas';
        form.pay_status = val.pay_status || 'Belum Diajukan';
        form.pic_user_id = val.pic_user_id || '';
        form.follow_up_date = val.follow_up_date || '';
    } else {
        isEdit.value = false;
        form.reset();
        form.project_id = props.projects?.[0]?.id || '';
        form.client = props.projects?.[0]?.client || '';
        form.pic_user_id = props.users?.[0]?.id || '';
        form.doc_status = 'Penyusunan BAST & Berkas';
        form.pay_status = 'Belum Diajukan';
    }
}, { immediate: true });

function onProjectChange() {
    const p = props.projects?.find(x => x.id === Number(form.project_id));
    if (p && p.client) {
        form.client = p.client;
    }
}

function submit() {
    if (isEdit.value) {
        form.put(route('billing.update', form.id), {
            preserveScroll: true,
            onSuccess: () => emit('close'),
        });
    } else {
        form.post(route('billing.store'), {
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
                    <i class="fa-solid fa-file-invoice-dollar text-emerald-400"></i>
                    {{ isEdit ? 'Edit Data Penagihan Termin' : 'Tambah Termin / Tagihan Baru' }}
                </h3>
                <button @click="$emit('close')" class="text-slate-400 hover:text-white">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            <form @submit.prevent="submit" class="p-6 space-y-4 text-xs">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="font-bold text-slate-700 block mb-1">Proyek *</label>
                        <select
                            v-model="form.project_id"
                            @change="onProjectChange"
                            required
                            class="w-full p-2.5 border border-slate-300 rounded-lg bg-white text-xs"
                        >
                            <option value="" disabled>Pilih Proyek...</option>
                            <option v-for="p in projects" :key="p.id" :value="p.id">{{ p.name }}</option>
                        </select>
                    </div>

                    <div>
                        <label class="font-bold text-slate-700 block mb-1">Klien / Instansi *</label>
                        <input
                            type="text"
                            v-model="form.client"
                            required
                            class="w-full p-2.5 border border-slate-300 rounded-lg text-xs"
                        />
                    </div>

                    <div>
                        <label class="font-bold text-slate-700 block mb-1">Nilai Kontrak Total (Rp) *</label>
                        <input
                            type="number"
                            v-model="form.contract_val"
                            required
                            class="w-full p-2.5 border border-slate-300 rounded-lg text-xs"
                        />
                    </div>

                    <div>
                        <label class="font-bold text-slate-700 block mb-1">Termin Pembayaran *</label>
                        <input
                            type="text"
                            v-model="form.term"
                            required
                            placeholder="Contoh: Termin 1 (Uang Muka 30%)"
                            class="w-full p-2.5 border border-slate-300 rounded-lg text-xs"
                        />
                    </div>

                    <div>
                        <label class="font-bold text-slate-700 block mb-1">Nilai Tagihan Termin Ini (Rp) *</label>
                        <input
                            type="number"
                            v-model="form.invoice_val"
                            required
                            class="w-full p-2.5 border border-slate-300 rounded-lg text-xs"
                        />
                    </div>

                    <div>
                        <label class="font-bold text-slate-700 block mb-1">Nominal yang Sudah Diterima (Rp) *</label>
                        <input
                            type="number"
                            v-model="form.paid_val"
                            required
                            class="w-full p-2.5 border border-slate-300 rounded-lg text-xs"
                        />
                    </div>

                    <div>
                        <label class="font-bold text-slate-700 block mb-1">Tanggal Pengajuan Tagihan</label>
                        <input
                            type="date"
                            v-model="form.submit_date"
                            class="w-full p-2.5 border border-slate-300 rounded-lg text-xs"
                        />
                    </div>

                    <div>
                        <label class="font-bold text-slate-700 block mb-1">Status Dokumen Tagihan *</label>
                        <select v-model="form.doc_status" required class="w-full p-2.5 border border-slate-300 rounded-lg bg-white text-xs">
                            <option value="Penyusunan BAST & Berkas">Penyusunan BAST & Berkas</option>
                            <option value="Menunggu Tanda Tangan PPK">Menunggu Tanda Tangan PPK</option>
                            <option value="Lengkap & Sudah Diajukan">Lengkap & Sudah Diajukan</option>
                            <option value="Revisi Berkas Pajak/SPP">Revisi Berkas Pajak/SPP</option>
                        </select>
                    </div>

                    <div>
                        <label class="font-bold text-slate-700 block mb-1">Status Pembayaran *</label>
                        <select v-model="form.pay_status" required class="w-full p-2.5 border border-slate-300 rounded-lg bg-white text-xs">
                            <option value="Belum Diajukan">Belum Diajukan</option>
                            <option value="Menunggu Pencairan (SP2D)">Menunggu Pencairan (SP2D)</option>
                            <option value="Dibayar Sebagian">Dibayar Sebagian</option>
                            <option value="Terlambat Cair (Perlu Follow-Up)">Terlambat Cair (Perlu Follow-Up)</option>
                            <option value="Lunas">Lunas</option>
                        </select>
                    </div>

                    <div>
                        <label class="font-bold text-slate-700 block mb-1">PIC Penagihan *</label>
                        <select v-model="form.pic_user_id" required class="w-full p-2.5 border border-slate-300 rounded-lg bg-white text-xs">
                            <option value="" disabled>Pilih PIC...</option>
                            <option v-for="u in users" :key="u.id" :value="u.id">{{ u.name }}</option>
                        </select>
                    </div>

                    <div class="md:col-span-2">
                        <label class="font-bold text-slate-700 block mb-1">Tanggal Follow-Up Berikutnya *</label>
                        <input
                            type="date"
                            v-model="form.follow_up_date"
                            required
                            class="w-full p-2.5 border border-slate-300 rounded-lg text-xs"
                        />
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
                        class="px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg font-semibold flex items-center gap-1.5"
                    >
                        <i class="fa-solid fa-floppy-disk"></i>
                        {{ isEdit ? 'Simpan Perubahan' : 'Simpan Penagihan' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>
