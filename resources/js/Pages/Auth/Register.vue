<script setup>
import { ref } from 'vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
});

const showPassword = ref(false);
const showPasswordConfirmation = ref(false);

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <GuestLayout
        title="Daftar Akun Tim"
        subtitle="Lengkapi data di bawah ini untuk mendaftarkan akun baru pada sistem kendali proyek."
        badge="Registrasi Personil Baru"
        badge-icon="fa-user-plus"
    >
        <Head title="Daftar Akun — Salsabila Control Center" />

        <form @submit.prevent="submit" class="space-y-4">
            <!-- Name -->
            <div>
                <InputLabel for="name" value="Nama Lengkap" />
                <div class="relative mt-1">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400 text-xs">
                        <i class="fa-regular fa-user"></i>
                    </div>
                    <TextInput
                        id="name"
                        type="text"
                        class="pl-9 pr-3.5"
                        v-model="form.name"
                        placeholder="Contoh: Ir. Budi Santoso, S.T."
                        required
                        autofocus
                        autocomplete="name"
                    />
                </div>
                <InputError class="mt-1.5" :message="form.errors.name" />
            </div>

            <!-- Email -->
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
                        placeholder="nama@salsabila.com"
                        required
                        autocomplete="username"
                    />
                </div>
                <InputError class="mt-1.5" :message="form.errors.email" />
            </div>

            <!-- Password -->
            <div>
                <InputLabel for="password" value="Kata Sandi" />
                <div class="relative mt-1">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400 text-xs">
                        <i class="fa-solid fa-lock"></i>
                    </div>
                    <TextInput
                        id="password"
                        :type="showPassword ? 'text' : 'password'"
                        class="pl-9 pr-10"
                        v-model="form.password"
                        placeholder="Minimal 8 karakter..."
                        required
                        autocomplete="new-password"
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

            <!-- Confirm Password -->
            <div>
                <InputLabel for="password_confirmation" value="Ulangi Kata Sandi" />
                <div class="relative mt-1">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400 text-xs">
                        <i class="fa-solid fa-shield-halved"></i>
                    </div>
                    <TextInput
                        id="password_confirmation"
                        :type="showPasswordConfirmation ? 'text' : 'password'"
                        class="pl-9 pr-10"
                        v-model="form.password_confirmation"
                        placeholder="Ketik ulang kata sandi..."
                        required
                        autocomplete="new-password"
                    />
                    <button
                        type="button"
                        @click="showPasswordConfirmation = !showPasswordConfirmation"
                        class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-600 focus:outline-none transition"
                        title="Tampilkan / Sembunyikan Kata Sandi"
                        tabindex="-1"
                    >
                        <i :class="['fa-solid text-xs', showPasswordConfirmation ? 'fa-eye-slash' : 'fa-eye']"></i>
                    </button>
                </div>
                <InputError class="mt-1.5" :message="form.errors.password_confirmation" />
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
                        <span>Mendaftarkan Akun...</span>
                    </template>
                    <template v-else>
                        <span>Daftar Akun Baru</span>
                        <i class="fa-solid fa-user-check text-xs ml-1"></i>
                    </template>
                </PrimaryButton>
            </div>

            <!-- Already registered -->
            <div class="pt-4 text-center border-t border-slate-100">
                <p class="text-xs text-slate-500">
                    Sudah memiliki akun terdaftar?
                    <Link
                        :href="route('login')"
                        class="text-sky-600 hover:text-sky-700 font-bold hover:underline transition ml-1"
                    >
                        Masuk di Sini
                    </Link>
                </p>
            </div>
        </form>
    </GuestLayout>
</template>
