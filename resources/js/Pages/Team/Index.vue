<script setup>
import { ref } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import MemberModal from '@/Components/Modals/MemberModal.vue';

const props = defineProps({
    members: Array,
    divisions: Array,
    sidebarBadges: Object,
});

const showModal = ref(false);
const activeMember = ref(null);

function openCreateModal() {
    activeMember.value = null;
    showModal.value = true;
}

function openEditModal(m) {
    activeMember.value = m;
    showModal.value = true;
}

function deleteMember(m) {
    if (!confirm(`Hapus anggota tim "${m.name}"?`)) return;
    router.delete(route('team.destroy', m.id), { preserveScroll: true });
}
</script>

<template>
    <AppLayout
        title="Kontrol Tim & PIC"
        subtitle="Distribusi Beban Kerja Objektif, Ketepatan Deadline, dan Evaluasi Anggota Tim"
        :badges="sidebarBadges"
    >
        <template #header-actions>
            <button
                @click="openCreateModal"
                class="px-3.5 py-2 bg-sky-600 hover:bg-sky-700 text-white rounded-lg text-xs font-semibold flex items-center gap-1.5 shadow-sm transition active:scale-95"
            >
                <i class="fa-solid fa-user-plus text-xs"></i>
                <span class="inline">Anggota Baru</span>
            </button>
        </template>

        <Head title="Kontrol Tim & PIC — Salsabila Control Center" />

        <div class="space-y-6">
            <div class="flex flex-wrap items-center justify-between gap-4 bg-white p-4 rounded-xl border border-slate-200 shadow-sm">
                <div>
                    <h3 class="text-sm font-bold text-slate-800">Kontrol Anggota Tim, Beban Kerja & Evaluasi Objektif</h3>
                    <p class="text-xs text-slate-500">Evaluasi berbasis ketepatan deadline, penyelesaian tugas, dan kinerja teknis</p>
                </div>
                <button
                    @click="openCreateModal"
                    class="px-4 py-2 bg-sky-600 hover:bg-sky-700 text-white rounded-lg text-xs font-semibold flex items-center gap-2 shadow-sm transition"
                >
                    <i class="fa-solid fa-user-plus"></i> Tambah Anggota Tim
                </button>
            </div>

            <!-- Team Cards Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div
                    v-for="m in members"
                    :key="m.id"
                    class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm space-y-4 hover:shadow-md transition"
                >
                    <!-- Header -->
                    <div class="flex items-start justify-between gap-3">
                        <div class="flex items-center gap-3">
                            <div class="w-11 h-11 rounded-full bg-slate-900 text-white flex items-center justify-center font-bold text-sm shadow">
                                {{ m.name.charAt(0) }}
                            </div>
                            <div>
                                <h3 class="text-sm font-bold text-slate-900">{{ m.name }}</h3>
                                <p class="text-xs text-slate-500">{{ m.role }}</p>
                                <span class="inline-block mt-1 px-2 py-0.5 rounded text-[10px] font-semibold bg-sky-50 text-sky-700 border border-sky-200">
                                    Divisi {{ m.divisi }}
                                </span>
                            </div>
                        </div>

                        <div class="flex items-center gap-1">
                            <button
                                @click="openEditModal(m)"
                                class="p-1.5 rounded text-slate-400 hover:text-sky-600 hover:bg-slate-100 transition text-xs"
                                title="Edit Anggota"
                            >
                                <i class="fa-solid fa-pen-to-square"></i>
                            </button>
                            <button
                                @click="deleteMember(m)"
                                class="p-1.5 rounded text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition text-xs"
                                title="Hapus Anggota"
                            >
                                <i class="fa-solid fa-trash-can"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Workload KPI Stats -->
                    <div class="grid grid-cols-4 gap-2 text-center text-xs pt-3 border-t border-slate-100">
                        <div class="p-2 rounded-lg bg-slate-50">
                            <span class="text-[10px] text-slate-400 block font-semibold uppercase">Total Tugas</span>
                            <span class="text-base font-black text-slate-800">{{ m.total_tasks }}</span>
                        </div>
                        <div class="p-2 rounded-lg bg-sky-50 text-sky-900">
                            <span class="text-[10px] text-sky-600 block font-semibold uppercase">Tugas Aktif</span>
                            <span class="text-base font-black text-sky-700">{{ m.active_tasks }}</span>
                        </div>
                        <div class="p-2 rounded-lg bg-emerald-50 text-emerald-900">
                            <span class="text-[10px] text-emerald-600 block font-semibold uppercase">Selesai</span>
                            <span class="text-base font-black text-emerald-700">{{ m.done_tasks }}</span>
                        </div>
                        <div class="p-2 rounded-lg" :class="m.overdue_tasks > 0 ? 'bg-rose-50 text-rose-900' : 'bg-slate-50'">
                            <span class="text-[10px] text-slate-400 block font-semibold uppercase" :class="{ 'text-rose-600': m.overdue_tasks > 0 }">Terlambat</span>
                            <span class="text-base font-black" :class="m.overdue_tasks > 0 ? 'text-rose-700' : 'text-slate-800'">{{ m.overdue_tasks }}</span>
                        </div>
                    </div>

                    <!-- Evaluasi Objektif Team Leader -->
                    <div class="p-3 bg-slate-50 rounded-lg border border-slate-200/80 text-xs space-y-1">
                        <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider block">
                            <i class="fa-solid fa-clipboard-user text-sky-600"></i> Catatan Evaluasi Team Leader:
                        </span>
                        <p class="text-slate-700 italic">
                            {{ m.eval_notes || 'Belum ada catatan evaluasi khusus untuk anggota tim ini.' }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <template #modals>
            <MemberModal
                :show="showModal"
                :member="activeMember"
                :divisions="divisions"
                @close="showModal = false"
            />
        </template>
    </AppLayout>
</template>
