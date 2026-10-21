<script setup>
import { ref } from 'vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, useForm } from '@inertiajs/vue3';

const props = defineProps({
    email: {
        type: String,
        required: true,
    },
    token: {
        type: String,
        required: true,
    },
});

const form = useForm({
    token: props.token,
    email: props.email,
    password: '',
    password_confirmation: '',
});

const showPassword = ref(false);
const showPasswordConfirmation = ref(false);

const submit = () => {
    form.post(route('password.store'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <GuestLayout
        title="Atur Ulang Kata Sandi"
        subtitle="Buat kata sandi baru yang kuat untuk mengamankan akun kendali proyek Anda."
        badge="Pengaturan Keamanan"
        badge-icon="fa-lock-open"
    >
        <Head title="Reset Kata Sandi — Salsabila Control Center" />

        <form @submit.prevent="submit" class="space-y-4">
            <!-- Email (readonly or prefilled) -->
            <div>
                <InputLabel for="email" value="Alamat Email Akun" />
                <div class="relative mt-1">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400 text-xs">
                        <i class="fa-regular fa-envelope"></i>
                    </div>
                    <TextInput
                        id="email"
                        type="email"
                        class="pl-9 pr-3.5 bg-slate-50"
                        v-model="form.email"
                        required
                        autofocus
                        autocomplete="username"
                    />
                </div>
                <InputError class="mt-1.5" :message="form.errors.email" />
            </div>

            <!-- Password -->
            <div>
                <InputLabel for="password" value="Kata Sandi Baru" />
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

            <!-- Password Confirmation -->
            <div>
                <InputLabel for="password_confirmation" value="Ulangi Kata Sandi Baru" />
                <div class="relative mt-1">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400 text-xs">
                        <i class="fa-solid fa-shield-halved"></i>
                    </div>
                    <TextInput
                        id="password_confirmation"
                        :type="showPasswordConfirmation ? 'text' : 'password'"
                        class="pl-9 pr-10"
                        v-model="form.password_confirmation"
                        placeholder="Ketik ulang kata sandi baru..."
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
                        <span>Menyimpan Kata Sandi...</span>
                    </template>
                    <template v-else>
                        <span>Simpan Kata Sandi Baru</span>
                        <i class="fa-solid fa-check text-xs ml-1"></i>
                    </template>
                </PrimaryButton>
            </div>
        </form>
    </GuestLayout>
</template>
