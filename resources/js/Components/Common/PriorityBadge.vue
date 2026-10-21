<script setup>
import { computed } from 'vue';

const props = defineProps({
    priority: {
        type: String,
        default: 'Sedang',
    },
});

const priorityClass = computed(() => {
    const map = {
        'Kritis': 'bg-rose-600 text-white',
        'Tinggi': 'bg-amber-500 text-slate-950',
        'Sedang': 'bg-sky-500 text-white',
        'Rendah': 'bg-slate-200 text-slate-700',
    };
    return map[props.priority] || 'bg-slate-200 text-slate-700';
});
</script>

<template>
    <span
        :class="[
            'inline-flex items-center px-2 py-0.5 text-[10px] font-bold uppercase rounded tracking-wider',
            priorityClass
        ]"
    >
        {{ priority }}
    </span>
</template>
