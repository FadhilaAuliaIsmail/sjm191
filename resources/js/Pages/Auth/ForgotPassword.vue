<script setup>
import InputError from "@/Components/InputError.vue";
import GuestLayout from "@/Layouts/GuestLayout.vue";
import { Head, Link, useForm } from "@inertiajs/vue3";

const form = useForm({
    email: "",
});

const submit = () => {
    form.post(route("password.email"));
};
</script>

<template>
    <GuestLayout>
        <Head title="Lupa Kata Sandi" />

        <p class="mb-5 text-sm leading-relaxed text-gray-600">
            Masukkan email akun kamu. Kami akan tampilkan pertanyaan keamanan
            yang sudah kamu atur untuk verifikasi.
        </p>

        <form @submit.prevent="submit" class="space-y-4">
            <div>
                <label
                    for="email"
                    class="mb-1 block text-sm font-medium text-gray-700"
                    >Email</label
                >
                <input
                    id="email"
                    type="email"
                    v-model="form.email"
                    required
                    autofocus
                    autocomplete="username"
                    class="w-full rounded-xl border-gray-200 text-sm shadow-sm focus:border-red-500 focus:ring-red-500"
                />
                <InputError class="mt-1.5" :message="form.errors.email" />
            </div>

            <button
                type="submit"
                :disabled="form.processing"
                class="w-full rounded-xl bg-red-600 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-red-700 disabled:opacity-50"
            >
                Lanjutkan
            </button>

            <Link
                :href="route('login')"
                class="block text-center text-sm font-medium text-gray-500 hover:text-red-600"
            >
                ← Kembali ke halaman masuk
            </Link>
        </form>
    </GuestLayout>
</template>
