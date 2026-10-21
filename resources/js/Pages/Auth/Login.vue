<script setup>
import { ref } from 'vue';
import Checkbox from '@/Components/Checkbox.vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

defineProps({
    canResetPassword: {
        type: Boolean,
        default: true,
    },
    status: {
        type: String,
        default: null,
    },
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const showPassword = ref(false);

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <GuestLayout
        title="Masuk ke Akun Anda"
        subtitle="Masukkan email dan kata sandi untuk mengakses panel kontrol proyek."
        badge="Portal Akses Tim & PIC"
        badge-icon="fa-right-to-bracket"
    >
        <Head title="Masuk — Salsabila Control Center" />

        <!-- Flash Status Message (e.g. Password Reset link sent) -->
        <transition
            enter-active-class="transition duration-200 ease-out"
            enter-from-class="opacity-0 -translate-y-2"
            enter-to-class="opacity-100 translate-y-0"
        >
            <div
                v-if="status"
                class="mb-5 p-3.5 rounded-xl bg-emerald-50 border border-emerald-200 flex items-start gap-2.5 text-xs text-emerald-800"
            >
                <i class="fa-solid fa-circle-check text-emerald-600 mt-0.5 text-sm shrink-0"></i>
                <span class="leading-relaxed font-medium">{{ status }}</span>
            </div>
        </transition>

        <form @submit.prevent="submit" class="space-y-4">
            <!-- Email Field -->
            <div>
                <InputLabel for="email" value="Alamat Email" />
                <div class="relative mt-1">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400 text-xs">
                        <i class="fa-regular fa-envelope"></i>
                    </div>
                    <TextInput
                        id="email"
                        type="email"
                        class="pl-9 pr-3.5"
                        v-model="form.email"
                        placeholder="contoh: nama@salsabila.com"
                        required
                        autofocus
                        autocomplete="username"
                    />
                </div>
                <InputError class="mt-1.5" :message="form.errors.email" />
            </div>

            <!-- Password Field -->
            <div>
                <div class="flex items-center justify-between mb-1">
                    <InputLabel for="password" value="Kata Sandi" class="!mb-0" />
                    <Link
                        v-if="canResetPassword && route().has('password.request')"
                        :href="route('password.request')"
                        class="text-xs text-sky-600 hover:text-sky-700 font-semibold hover:underline transition"
                    >
                        Lupa kata sandi?
                    </Link>
                </div>
                <div class="relative mt-1">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400 text-xs">
                        <i class="fa-solid fa-lock"></i>
                    </div>
                    <TextInput
                        id="password"
                        :type="showPassword ? 'text' : 'password'"
                        class="pl-9 pr-10"
                        v-model="form.password"
                        placeholder="Masukkan kata sandi..."
                        required
                        autocomplete="current-password"
                    />
                    <button
                        type="button"
                        @click="showPassword = !showPassword"
                        class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-600 focus:outline-none transition"
                        title="Tampilkan / Sembunyikan Kata Sandi"
                        tabindex="-1"
                    >
                        <i :class="['fa-solid text-xs', showPassword ? 'fa-eye-slash' : 'fa-eye']"></i>
                    </button>
                </div>
                <InputError class="mt-1.5" :message="form.errors.password" />
            </div>

            <!-- Remember Me -->
            <div class="pt-1 flex items-center justify-between">
                <label class="flex items-center cursor-pointer select-none">
                    <Checkbox name="remember" v-model:checked="form.remember" />
                    <span class="ms-2 text-xs text-slate-600 font-medium">Ingat saya di perangkat ini</span>
                </label>
            </div>

            <!-- Submit Button -->
            <div class="pt-2">
                <PrimaryButton
                    class="w-full !py-3 text-xs tracking-wider font-bold shadow-lg shadow-sky-500/25"
                    :class="{ 'opacity-70': form.processing }"
                    :disabled="form.processing"
                >
                    <template v-if="form.processing">
                        <i class="fa-solid fa-circle-notch fa-spin text-sm"></i>
                        <span>Memverifikasi Akses...</span>
                    </template>
                    <template v-else>
                        <span>Masuk ke Dashboard</span>
                        <i class="fa-solid fa-arrow-right-to-bracket text-xs ml-1"></i>
                    </template>
                </PrimaryButton>
            </div>

            <!-- Register Link / Footnote -->
            <div v-if="route().has('register')" class="pt-4 text-center border-t border-slate-100">
                <p class="text-xs text-slate-500">
                    Belum memiliki akun tim?
                    <Link
                        :href="route('register')"
                        class="text-sky-600 hover:text-sky-700 font-bold hover:underline transition ml-1"
                    >
                        Daftar Akun Baru
                    </Link>
                </p>
            </div>
        </form>
    </GuestLayout>
</template>
