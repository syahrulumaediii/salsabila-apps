<script setup>
import { computed } from 'vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps({
    status: {
        type: String,
        default: null,
    },
});

const form = useForm({});

const submit = () => {
    form.post(route('verification.send'));
};

const verificationLinkSent = computed(
    () => props.status === 'verification-link-sent',
);
</script>

<template>
    <GuestLayout
        title="Verifikasi Alamat Email"
        subtitle="Satu langkah lagi untuk mengaktifkan akses penuh ke panel kendali proyek Anda."
        badge="Aktivasi Akun"
        badge-icon="fa-envelope-circle-check"
    >
        <Head title="Verifikasi Email — Salsabila Control Center" />

        <div class="mb-5 p-4 rounded-xl bg-sky-50 border border-sky-100 flex items-start gap-3 text-xs text-sky-900 leading-relaxed">
            <i class="fa-solid fa-envelope-open-text text-sky-600 mt-0.5 text-base shrink-0"></i>
            <div>
                <p class="font-bold text-sky-950">Terima kasih telah bergabung!</p>
                <p class="mt-0.5 text-sky-800">
                    Sebelum memulai, silakan verifikasi alamat email Anda dengan mengeklik tautan konfirmasi yang baru saja kami kirimkan ke kotak masuk Anda.
                </p>
            </div>
        </div>

        <transition
            enter-active-class="transition duration-200 ease-out"
            enter-from-class="opacity-0 -translate-y-2"
            enter-to-class="opacity-100 translate-y-0"
        >
            <div
                class="mb-5 p-3.5 rounded-xl bg-emerald-50 border border-emerald-200 flex items-start gap-2.5 text-xs text-emerald-800"
                v-if="verificationLinkSent"
            >
                <i class="fa-solid fa-circle-check text-emerald-600 mt-0.5 text-sm shrink-0"></i>
                <span class="leading-relaxed font-medium">
                    Tautan verifikasi baru telah berhasil dikirim ke alamat email yang Anda daftarkan. Silakan periksa inbox atau folder spam Anda.
                </span>
            </div>
        </transition>

        <form @submit.prevent="submit" class="space-y-4">
            <div>
                <PrimaryButton
                    class="w-full !py-3 text-xs tracking-wider font-bold shadow-lg shadow-sky-500/25"
                    :class="{ 'opacity-70': form.processing }"
                    :disabled="form.processing"
                >
                    <template v-if="form.processing">
                        <i class="fa-solid fa-circle-notch fa-spin text-sm"></i>
                        <span>Mengirimkan Tautan Baru...</span>
                    </template>
                    <template v-else>
                        <i class="fa-solid fa-paper-plane text-xs mr-1"></i>
                        <span>Kirim Ulang Email Verifikasi</span>
                    </template>
                </PrimaryButton>
            </div>

            <div class="pt-3 text-center border-t border-slate-100">
                <Link
                    :href="route('logout')"
                    method="post"
                    as="button"
                    class="inline-flex items-center gap-1.5 text-xs text-slate-500 hover:text-rose-600 font-semibold transition"
                >
                    <i class="fa-solid fa-arrow-right-from-bracket text-[11px]"></i>
                    <span>Keluar dari Akun Ini (Log Out)</span>
                </Link>
            </div>
        </form>
    </GuestLayout>
</template>
