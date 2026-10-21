<script setup>
defineProps({
    message: {
        type: String,
    },
});
</script>

<template>
    <transition
        enter-active-class="transition-all duration-200 ease-out"
        enter-from-class="opacity-0 -translate-y-1"
        enter-to-class="opacity-100 translate-y-0"
        leave-active-class="transition-all duration-150 ease-in"
        leave-from-class="opacity-100 translate-y-0"
        leave-to-class="opacity-0 -translate-y-1"
    >
        <div v-if="message" class="flex items-center gap-1.5 mt-1.5 text-xs text-rose-600 font-medium">
            <i class="fa-solid fa-circle-exclamation shrink-0 text-[11px]"></i>
            <span>{{ message }}</span>
        </div>
    </transition>
</template>
