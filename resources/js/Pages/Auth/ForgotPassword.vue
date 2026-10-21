<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

defineProps({
    status: {
        type: String,
        default: null,
    },
});

const form = useForm({
    email: '',
});

const submit = () => {
    form.post(route('password.email'));
};
</script>

<template>
    <GuestLayout
        title="Pemulihan Kata Sandi"
        subtitle="Masukkan alamat email Anda untuk menerima tautan pembuatan kata sandi baru."
        badge="Bantuan Akses Akun"
        badge-icon="fa-key"
    >
        <Head title="Lupa Kata Sandi — Salsabila Control Center" />

        <div class="mb-5 p-3.5 rounded-xl bg-sky-50/70 border border-sky-100 flex items-start gap-2.5 text-xs text-sky-800 leading-relaxed">
            <i class="fa-solid fa-circle-info text-sky-600 mt-0.5 text-sm shrink-0"></i>
            <span>
                Lupa kata sandi? Masukkan email akun Anda. Kami akan mengirimkan tautan verifikasi aman untuk mengatur ulang kata sandi Anda.
            </span>
        </div>

        <!-- Success Status Banner -->
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
            <!-- Email -->
            <div>
                <InputLabel for="email" value="Alamat Email Terdaftar" />
                <div class="relative mt-1">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400 text-xs">
                        <i class="fa-regular fa-envelope"></i>
                    </div>
                    <TextInput
                        id="email"
                        type="email"
                        class="pl-9 pr-3.5"
                        v-model="form.email"
                        placeholder="nama@salsabila.com"
                        required
                        autofocus
                        autocomplete="username"
                    />
                </div>
                <InputError class="mt-1.5" :message="form.errors.email" />
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
                        <span>Mengirim Tautan...</span>
                    </template>
                    <template v-else>
                        <span>Kirim Tautan Reset Password</span>
                        <i class="fa-solid fa-paper-plane text-xs ml-1"></i>
                    </template>
                </PrimaryButton>
            </div>

            <!-- Back to Login -->
            <div class="pt-4 text-center border-t border-slate-100">
                <Link
                    :href="route('login')"
                    class="inline-flex items-center gap-1.5 text-xs text-slate-600 hover:text-sky-600 font-semibold transition"
                >
                    <i class="fa-solid fa-arrow-left text-[11px]"></i>
                    <span>Kembali ke Halaman Masuk</span>
                </Link>
            </div>
        </form>
    </GuestLayout>
</template>
