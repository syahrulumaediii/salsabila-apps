<script setup>
import { computed } from 'vue';

const props = defineProps({
    progress: {
        type: Number,
        default: 0,
    },
    showLabel: {
        type: Boolean,
        default: true,
    },
    heightClass: {
        type: String,
        default: 'h-2',
    },
});

const normalizedProgress = computed(() => {
    return Math.min(100, Math.max(0, Math.round(props.progress || 0)));
});

const barColor = computed(() => {
    const p = normalizedProgress.value;
    if (p >= 100) return 'bg-emerald-500';
    if (p >= 70) return 'bg-sky-500';
    if (p >= 40) return 'bg-amber-500';
    return 'bg-rose-500';
});
</script>

<template>
    <div class="w-full">
        <div v-if="showLabel" class="flex items-center justify-between text-[11px] font-semibold text-slate-600 mb-1">
            <span>Progress</span>
            <span :class="[normalizedProgress === 100 ? 'text-emerald-600 font-bold' : 'text-slate-800 font-bold']">
                {{ normalizedProgress }}%
            </span>
        </div>
        <div :class="['w-full bg-slate-200 rounded-full overflow-hidden', heightClass]">
            <div
                :class="['h-full transition-all duration-300 rounded-full', barColor]"
                :style="{ width: `${normalizedProgress}%` }"
            ></div>
        </div>
    </div>
</template>
