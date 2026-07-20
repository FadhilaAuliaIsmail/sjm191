<script setup>
import InputError from "@/Components/InputError.vue";
import GuestLayout from "@/Layouts/GuestLayout.vue";
import { Head, Link, useForm } from "@inertiajs/vue3";

defineProps({
    pertanyaan: String,
});

const form = useForm({
    jawaban_keamanan: "",
    password: "",
    password_confirmation: "",
});

const submit = () => {
    form.post(route("password.security.update"));
};
</script>

<template>
    <GuestLayout>
        <Head title="Verifikasi Keamanan" />

        <div class="mb-5 rounded-xl bg-red-50 px-4 py-3">
            <p class="text-xs font-medium text-red-500">Pertanyaan Keamanan</p>
            <p class="mt-0.5 text-sm font-semibold text-gray-800">
                {{ pertanyaan }}
            </p>
        </div>

        <form @submit.prevent="submit" class="space-y-4">
            <div>
                <label
                    for="jawaban"
                    class="mb-1 block text-sm font-medium text-gray-700"
                    >Jawaban Anda</label
                >
                <input
                    id="jawaban"
                    type="text"
                    v-model="form.jawaban_keamanan"
                    required
                    autofocus
                    class="w-full rounded-xl border-gray-200 text-sm shadow-sm focus:border-red-500 focus:ring-red-500"
                />
                <InputError
                    class="mt-1.5"
                    :message="form.errors.jawaban_keamanan"
                />
            </div>

            <div>
                <label
                    for="password"
                    class="mb-1 block text-sm font-medium text-gray-700"
                    >Password Baru</label
                >
                <input
                    id="password"
                    type="password"
                    v-model="form.password"
                    required
                    placeholder="Minimal 8 karakter"
                    class="w-full rounded-xl border-gray-200 text-sm shadow-sm focus:border-red-500 focus:ring-red-500"
                />
                <InputError class="mt-1.5" :message="form.errors.password" />
            </div>

            <div>
                <label
                    for="password_confirmation"
                    class="mb-1 block text-sm font-medium text-gray-700"
                    >Konfirmasi Password Baru</label
                >
                <input
                    id="password_confirmation"
                    type="password"
                    v-model="form.password_confirmation"
                    required
                    class="w-full rounded-xl border-gray-200 text-sm shadow-sm focus:border-red-500 focus:ring-red-500"
                />
            </div>

            <button
                type="submit"
                :disabled="form.processing"
                class="w-full rounded-xl bg-red-600 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-red-700 disabled:opacity-50"
            >
                Simpan Password Baru
            </button>

            <Link
                :href="route('login')"
                class="block text-center text-sm font-medium text-gray-500 hover:text-red-600"
            >
                ← Batal, kembali ke halaman masuk
            </Link>
        </form>
    </GuestLayout>
</template>
