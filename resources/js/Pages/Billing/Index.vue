<script setup>
import { ref } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import StatusBadge from '@/Components/Common/StatusBadge.vue';
import BillingModal from '@/Components/Modals/BillingModal.vue';

const props = defineProps({
    billings: Array,
    projects: Array,
    users: Array,
    summary: Object,
    sidebarBadges: Object,
});

const showModal = ref(false);
const activeBilling = ref(null);

function openCreateModal() {
    activeBilling.value = null;
    showModal.value = true;
}

function openEditModal(b) {
    activeBilling.value = b;
    showModal.value = true;
}

function deleteBilling(b) {
    if (!confirm('Hapus data termin penagihan ini?')) return;
    router.delete(route('billing.destroy', b.id), { preserveScroll: true });
}

function formatRupiah(num) {
    return 'Rp ' + Number(num || 0).toLocaleString('id-ID');
}

function formatDateID(dateStr) {
    if (!dateStr) return '-';
    const d = new Date(dateStr + 'T00:00:00');
    return d.toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' });
}
</script>

<template>
    <AppLayout
        title="Penagihan & Termin Proyek"
        subtitle="Pemisahan Status Teknis vs Status Pembayaran Termin & Monitoring Piutang Proyek"
        :badges="sidebarBadges"
    >
        <template #header-actions>
            <button
                @click="openCreateModal"
                class="px-3.5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-xs font-semibold flex items-center gap-1.5 shadow-sm transition active:scale-95"
            >
                <i class="fa-solid fa-file-circle-plus text-xs"></i>
                <span class="inline">Termin Baru</span>
            </button>
        </template>

        <Head title="Penagihan & Termin — Salsabila Control Center" />

        <div class="space-y-6">
            <!-- Ringkasan Finansial 4 Kartu -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm space-y-1">
                    <span class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider block">Total Nilai Kontrak</span>
                    <h3 class="text-lg font-black text-slate-900 truncate" :title="formatRupiah(summary.total_contract)">
                        {{ formatRupiah(summary.total_contract) }}
                    </h3>
                    <span class="text-[10px] text-slate-400">Seluruh proyek terdaftar</span>
                </div>

                <div class="bg-white p-4 rounded-xl border border-sky-200 bg-sky-50/20 shadow-sm space-y-1">
                    <span class="text-[11px] font-semibold text-sky-800 uppercase tracking-wider block">Total Tagihan Diajukan</span>
                    <h3 class="text-lg font-black text-sky-700 truncate" :title="formatRupiah(summary.total_invoiced)">
                        {{ formatRupiah(summary.total_invoiced) }}
                    </h3>
                    <span class="text-[10px] text-sky-600">Berkas termin berjalan</span>
                </div>

                <div class="bg-white p-4 rounded-xl border border-emerald-200 bg-emerald-50/20 shadow-sm space-y-1">
                    <span class="text-[11px] font-semibold text-emerald-800 uppercase tracking-wider block">Total Pembayaran Cair</span>
                    <h3 class="text-lg font-black text-emerald-700 truncate" :title="formatRupiah(summary.total_paid)">
                        {{ formatRupiah(summary.total_paid) }}
                    </h3>
                    <span class="text-[10px] text-emerald-600">Sudah masuk rekening</span>
                </div>

                <div class="bg-white p-4 rounded-xl border border-rose-200 bg-rose-50/30 shadow-sm space-y-1">
                    <span class="text-[11px] font-semibold text-rose-800 uppercase tracking-wider block">Sisa Piutang Berjalan</span>
                    <h3 class="text-lg font-black text-rose-700 truncate" :title="formatRupiah(summary.total_receivable)">
                        {{ formatRupiah(summary.total_receivable) }}
                    </h3>
                    <span class="text-[10px] text-rose-600 font-semibold">Perlu dikawal pencairannya</span>
                </div>
            </div>

            <!-- Header Action -->
            <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm flex flex-wrap items-center justify-between gap-4">
                <div>
                    <h3 class="text-sm font-bold text-slate-800">Daftar Termin & Administrasi Pembayaran</h3>
                    <p class="text-xs text-slate-500">
                        Status Pekerjaan Teknis dipisahkan dari Status Pembayaran agar proyek selesai teknis yang tertahan pencairannya tetap terpantau.
                    </p>
                </div>
                <button
                    @click="openCreateModal"
                    class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-xs font-semibold flex items-center gap-1.5 transition shadow-sm"
                >
                    <i class="fa-solid fa-plus"></i> Tambah Termin / Tagihan
                </button>
            </div>

            <!-- Billings Table -->
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="bg-slate-900 text-white uppercase text-[11px] tracking-wider">
                            <th class="p-3 whitespace-nowrap">Proyek & Klien</th>
                            <th class="p-3 whitespace-nowrap">Nilai Kontrak</th>
                            <th class="p-3 whitespace-nowrap">Termin & Nilai Tagihan</th>
                            <th class="p-3 whitespace-nowrap">Status Teknis</th>
                            <th class="p-3 whitespace-nowrap">Status Dokumen</th>
                            <th class="p-3 whitespace-nowrap">Status Bayar</th>
                            <th class="p-3 whitespace-nowrap">Sudah Diterima</th>
                            <th class="p-3 whitespace-nowrap">Sisa Tagihan</th>
                            <th class="p-3 whitespace-nowrap">PIC & Follow-Up</th>
                            <th class="p-3 text-center whitespace-nowrap">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        <tr
                            v-for="b in billings"
                            :key="b.id"
                            class="hover:bg-slate-50/80 transition"
                        >
                            <td class="p-3 whitespace-nowrap">
                                <p class="font-bold text-slate-900">{{ b.project?.name }}</p>
                                <p class="text-[10px] text-slate-500">Klien: {{ b.client }}</p>
                            </td>

                            <td class="p-3 whitespace-nowrap font-semibold text-slate-800">
                                {{ formatRupiah(b.contract_val) }}
                            </td>

                            <td class="p-3 whitespace-nowrap">
                                <p class="font-bold text-slate-900">{{ b.term }}</p>
                                <p class="text-[10px] text-sky-600 font-bold">{{ formatRupiah(b.invoice_val) }}</p>
                            </td>

                            <td class="p-3 whitespace-nowrap">
                                <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-slate-100 text-slate-800 border border-slate-200">
                                    {{ b.project?.status }}
                                </span>
                            </td>

                            <td class="p-3 whitespace-nowrap">
                                <StatusBadge :status="b.doc_status" />
                                <span v-if="b.submit_date" class="text-[10px] text-slate-400 block mt-0.5">
                                    Tgl: {{ formatDateID(b.submit_date) }}
                                </span>
                            </td>

                            <td class="p-3 whitespace-nowrap">
                                <StatusBadge :status="b.pay_status" />
                            </td>

                            <td class="p-3 whitespace-nowrap font-bold text-emerald-700">
                                {{ formatRupiah(b.paid_val) }}
                            </td>

                            <td class="p-3 whitespace-nowrap font-black text-rose-700">
                                {{ formatRupiah(b.remaining_val) }}
                            </td>

                            <td class="p-3 whitespace-nowrap">
                                <p class="font-bold text-slate-800">{{ b.pic?.name || '-' }}</p>
                                <span class="text-[10px] text-amber-600 font-semibold block">
                                    Follow-Up: {{ formatDateID(b.follow_up_date) }}
                                </span>
                            </td>

                            <td class="p-3 text-center whitespace-nowrap">
                                <div class="flex items-center justify-center gap-1">
                                    <button
                                        @click="openEditModal(b)"
                                        class="p-1.5 rounded text-slate-400 hover:text-sky-600 hover:bg-slate-100 transition text-xs"
                                        title="Edit Penagihan"
                                    >
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </button>
                                    <button
                                        @click="deleteBilling(b)"
                                        class="p-1.5 rounded text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition text-xs"
                                        title="Hapus Penagihan"
                                    >
                                        <i class="fa-solid fa-trash-can"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>

                        <tr v-if="billings.length === 0">
                            <td colspan="10" class="p-8 text-center text-slate-400">
                                Belum ada data penagihan termin.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <template #modals>
            <BillingModal
                :show="showModal"
                :billing="activeBilling"
                :projects="projects"
                :users="users"
                @close="showModal = false"
            />
        </template>
    </AppLayout>
</template>
