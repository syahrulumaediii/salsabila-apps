<script setup>
import { ref } from 'vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, useForm } from '@inertiajs/vue3';

const form = useForm({
    password: '',
});

const showPassword = ref(false);

const submit = () => {
    form.post(route('password.confirm'), {
        onFinish: () => form.reset(),
    });
};
</script>

<template>
    <GuestLayout
        title="Konfirmasi Kata Sandi"
        subtitle="Ini adalah area terproteksi. Konfirmasikan kata sandi Anda untuk melanjutkan otorisasi."
        badge="Verifikasi Keamanan"
        badge-icon="fa-shield-halved"
    >
        <Head title="Konfirmasi Kata Sandi — Salsabila Control Center" />

        <div class="mb-5 p-3.5 rounded-xl bg-amber-50 border border-amber-200 flex items-start gap-2.5 text-xs text-amber-800 leading-relaxed">
            <i class="fa-solid fa-triangle-exclamation text-amber-600 mt-0.5 text-sm shrink-0"></i>
            <span>
                Akses ini memerlukan verifikasi tambahan demi menjaga kerahasiaan data proyek dan integritas sistem kendali.
            </span>
        </div>

        <form @submit.prevent="submit" class="space-y-4">
            <div>
                <InputLabel for="password" value="Kata Sandi Saat Ini" />
                <div class="relative mt-1">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400 text-xs">
                        <i class="fa-solid fa-lock"></i>
                    </div>
                    <TextInput
                        id="password"
                        :type="showPassword ? 'text' : 'password'"
                        class="pl-9 pr-10"
                        v-model="form.password"
                        placeholder="Masukkan kata sandi akun..."
                        required
                        autocomplete="current-password"
                        autofocus
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

            <div class="pt-2">
                <PrimaryButton
                    class="w-full !py-3 text-xs tracking-wider font-bold shadow-lg shadow-sky-500/25"
                    :class="{ 'opacity-70': form.processing }"
                    :disabled="form.processing"
                >
                    <template v-if="form.processing">
                        <i class="fa-solid fa-circle-notch fa-spin text-sm"></i>
                        <span>Memverifikasi...</span>
                    </template>
                    <template v-else>
                        <span>Konfirmasi & Lanjutkan</span>
                        <i class="fa-solid fa-arrow-right text-xs ml-1"></i>
                    </template>
                </PrimaryButton>
            </div>
        </form>
    </GuestLayout>
</template>
