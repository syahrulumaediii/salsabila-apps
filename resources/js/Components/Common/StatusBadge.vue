<script setup>
import { computed } from 'vue';

const props = defineProps({
    status: {
        type: String,
        default: 'Belum Dimulai',
    },
});

const badgeClass = computed(() => {
    const map = {
        'Belum Dimulai': 'bg-slate-100 text-slate-700 border-slate-300',
        'Sedang Dikerjakan': 'bg-sky-100 text-sky-800 border-sky-300',
        'Menunggu Pemeriksaan': 'bg-purple-100 text-purple-800 border-purple-300',
        'Revisi': 'bg-amber-100 text-amber-800 border-amber-300',
        'Terhambat': 'bg-rose-100 text-rose-800 border-rose-300',
        'Selesai': 'bg-emerald-100 text-emerald-800 border-emerald-300',

        // Project Statuses
        'Aktif Berjalan': 'bg-emerald-100 text-emerald-800 border-emerald-300',
        'Menunggu Review Klien': 'bg-purple-100 text-purple-800 border-purple-300',
        'Selesai Teknis': 'bg-teal-100 text-teal-800 border-teal-300',
        'Selesai & Lunas': 'bg-emerald-100 text-emerald-800 border-emerald-300',

        // Billing / Document Statuses
        'Penyusunan BAST & Berkas': 'bg-slate-100 text-slate-700 border-slate-300',
        'Menunggu Tanda Tangan PPK': 'bg-amber-100 text-amber-800 border-amber-300',
        'Lengkap & Sudah Diajukan': 'bg-sky-100 text-sky-800 border-sky-300',
        'Revisi Berkas Pajak/SPP': 'bg-rose-100 text-rose-800 border-rose-300',

        // Billing Pay Statuses
        'Menunggu Pencairan (SP2D)': 'bg-amber-100 text-amber-800 border-amber-300',
        'Dibayar Sebagian': 'bg-indigo-100 text-indigo-800 border-indigo-300',
        'Terlambat Cair (Perlu Follow-Up)': 'bg-rose-100 text-rose-800 border-rose-300',
        'Lunas': 'bg-emerald-100 text-emerald-800 border-emerald-300',

        // Issues
        'Menunggu Keputusan TL': 'bg-rose-100 text-rose-800 border-rose-300',
        'Sedang Ditindaklanjuti': 'bg-amber-100 text-amber-800 border-amber-300',
        'Menunggu Konfirmasi Instansi': 'bg-purple-100 text-purple-800 border-purple-300',
        'Selesai Teratasi': 'bg-emerald-100 text-emerald-800 border-emerald-300',
    };
    return map[props.status] || 'bg-slate-100 text-slate-700 border-slate-300';
});
</script>

<template>
    <span
        :class="[
            'inline-flex items-center px-2 py-0.5 text-[11px] font-semibold rounded-full border whitespace-nowrap',
            badgeClass
        ]"
    >
        {{ status }}
    </span>
</template>
