<script setup>
defineProps({
    type: {
        type: String,
        default: 'submit',
    },
});
</script>

<template>
    <button
        :type="type"
        class="inline-flex items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-sky-500 to-sky-600 px-5 py-2.5 text-xs font-bold uppercase tracking-wider text-white shadow-md shadow-sky-500/25 transition-all duration-150 hover:from-sky-400 hover:to-sky-500 hover:shadow-lg hover:shadow-sky-500/30 focus:outline-none focus:ring-2 focus:ring-sky-500 focus:ring-offset-2 active:scale-[0.98] disabled:opacity-50 disabled:pointer-events-none disabled:active:scale-100"
    >
        <slot />
    </button>
</template>
