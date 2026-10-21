<script setup>
import { ref } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import StatusBadge from '@/Components/Common/StatusBadge.vue';
import IssueModal from '@/Components/Modals/IssueModal.vue';

const props = defineProps({
    issues: Array,
    projects: Array,
    users: Array,
    categories: Array,
    filters: Object,
    sidebarBadges: Object,
});

const categoryFilter = ref(props.filters?.category || 'ALL');

function onFilterCategory() {
    router.get(route('issues.index'), { category: categoryFilter.value }, { preserveState: true });
}

const showModal = ref(false);
const activeIssue = ref(null);

function openCreateModal() {
    activeIssue.value = null;
    showModal.value = true;
}

function openEditModal(issue) {
    activeIssue.value = issue;
    showModal.value = true;
}

function deleteIssue(issue) {
    if (!confirm('Hapus log kendala ini?')) return;
    router.delete(route('issues.destroy', issue.id), { preserveScroll: true });
}

function formatDateID(dateStr) {
    if (!dateStr) return '-';
    const d = new Date(dateStr + 'T00:00:00');
    return d.toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' });
}
</script>

<template>
    <AppLayout
        title="Kendala & Keputusan Team Leader"
        subtitle="Log Kendala Lapangan, Teknis, dan Keputusan Mendesak Team Leader"
        :badges="sidebarBadges"
    >
        <template #header-actions>
            <button
                @click="openCreateModal"
                class="px-3.5 py-2 bg-rose-600 hover:bg-rose-700 text-white rounded-lg text-xs font-semibold flex items-center gap-1.5 shadow-sm transition active:scale-95"
            >
                <i class="fa-solid fa-triangle-exclamation text-xs"></i>
                <span class="inline">Catat Kendala</span>
            </button>
        </template>

        <Head title="Kendala & Keputusan — Salsabila Control Center" />

        <div class="space-y-6">
            <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm flex flex-wrap items-center justify-between gap-4">
                <div>
                    <h3 class="text-sm font-bold text-slate-800">Log Kendala & Keputusan Mendesak</h3>
                    <p class="text-xs text-slate-500">Diurutkan otomatis dari kendala paling mendesak yang membutuhkan keputusan atau intervensi TL</p>
                </div>

                <div class="flex items-center gap-2">
                    <select
                        v-model="categoryFilter"
                        @change="onFilterCategory"
                        class="px-3 py-1.5 border border-slate-300 rounded-lg text-xs bg-white"
                    >
                        <option value="ALL">Semua Kategori Kendala</option>
                        <option v-for="cat in categories" :key="cat" :value="cat">{{ cat }}</option>
                    </select>

                    <button
                        @click="openCreateModal"
                        class="px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white rounded-lg text-xs font-semibold flex items-center gap-1.5 transition shadow-sm"
                    >
                        <i class="fa-solid fa-plus"></i> Catat Kendala / Keputusan
                    </button>
                </div>
            </div>

            <!-- Issues Table -->
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="bg-slate-900 text-white uppercase text-[11px] tracking-wider">
                            <th class="p-3 whitespace-nowrap">Kategori & Urgensi</th>
                            <th class="p-3 whitespace-nowrap">Proyek</th>
                            <th class="p-3 min-w-[200px]">Permasalahan & Dampak</th>
                            <th class="p-3 whitespace-nowrap">PIC Pelapor</th>
                            <th class="p-3 min-w-[160px]">Keputusan Dibutuhkan</th>
                            <th class="p-3 min-w-[160px]">Solusi / Arahan TL</th>
                            <th class="p-3 whitespace-nowrap">Target Selesai</th>
                            <th class="p-3 whitespace-nowrap">Status</th>
                            <th class="p-3 text-center whitespace-nowrap">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        <tr
                            v-for="iss in issues"
                            :key="iss.id"
                            class="hover:bg-slate-50/80 transition"
                        >
                            <td class="p-3 whitespace-nowrap">
                                <span
                                    class="inline-block px-2 py-0.5 rounded text-[10px] font-bold uppercase mb-1"
                                    :class="[
                                        iss.urgency.includes('Mendesak') ? 'bg-rose-600 text-white animate-pulse' :
                                        iss.urgency.includes('Tinggi') ? 'bg-amber-500 text-slate-950 font-bold' :
                                        'bg-slate-200 text-slate-700'
                                    ]"
                                >
                                    {{ iss.urgency }}
                                </span>
                                <p class="text-[10px] text-slate-500">{{ iss.category }}</p>
                            </td>

                            <td class="p-3 whitespace-nowrap font-bold text-slate-800">
                                {{ iss.project?.name }}
                            </td>

                            <td class="p-3">
                                <p class="font-bold text-slate-900">{{ iss.problem }}</p>
                                <p v-if="iss.impact" class="text-[11px] text-rose-600 mt-1">
                                    <strong>Dampak:</strong> {{ iss.impact }}
                                </p>
                            </td>

                            <td class="p-3 whitespace-nowrap">
                                <p class="font-semibold text-slate-800">{{ iss.reporter?.name }}</p>
                                <span class="text-[10px] text-slate-400 block font-mono">
                                    {{ formatDateID(iss.reported_date) }}
                                </span>
                            </td>

                            <td class="p-3 text-[11px] text-slate-700">
                                {{ iss.decision_needed || '-' }}
                            </td>

                            <td class="p-3 text-[11px] text-slate-800 font-medium bg-emerald-50/40">
                                {{ iss.solution || 'Belum ada solusi tertulis' }}
                            </td>

                            <td class="p-3 whitespace-nowrap font-bold text-slate-700">
                                {{ formatDateID(iss.target_date) }}
                            </td>

                            <td class="p-3 whitespace-nowrap">
                                <StatusBadge :status="iss.status" />
                            </td>

                            <td class="p-3 text-center whitespace-nowrap">
                                <div class="flex items-center justify-center gap-1">
                                    <button
                                        @click="openEditModal(iss)"
                                        class="p-1.5 rounded text-slate-400 hover:text-sky-600 hover:bg-slate-100 transition text-xs"
                                        title="Edit Kendala"
                                    >
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </button>
                                    <button
                                        @click="deleteIssue(iss)"
                                        class="p-1.5 rounded text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition text-xs"
                                        title="Hapus Kendala"
                                    >
                                        <i class="fa-solid fa-trash-can"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>

                        <tr v-if="issues.length === 0">
                            <td colspan="9" class="p-8 text-center text-slate-400">
                                Tidak ada log kendala aktif.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <template #modals>
            <IssueModal
                :show="showModal"
                :issue="activeIssue"
                :projects="projects"
                :users="users"
                :categories="categories"
                @close="showModal = false"
            />
        </template>
    </AppLayout>
</template>
