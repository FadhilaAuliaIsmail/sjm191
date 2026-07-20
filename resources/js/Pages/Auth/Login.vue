<script setup>
import InputError from "@/Components/InputError.vue";
import GuestLayout from "@/Layouts/GuestLayout.vue";
import { Head, useForm } from "@inertiajs/vue3";

defineProps({
    status: { type: String },
});

const form = useForm({
    email: "",
    password: "",
    remember: false,
});

const submit = () => {
    form.post(route("login"), {
        onFinish: () => form.reset("password"),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Masuk" />

        <div
            v-if="status"
            class="mb-4 rounded-lg bg-green-50 px-3 py-2 text-sm font-medium text-green-700"
        >
            {{ status }}
        </div>

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

            <div>
                <label
                    for="password"
                    class="mb-1 block text-sm font-medium text-gray-700"
                    >Kata Sandi</label
                >
                <input
                    id="password"
                    type="password"
                    v-model="form.password"
                    required
                    autocomplete="current-password"
                    class="w-full rounded-xl border-gray-200 text-sm shadow-sm focus:border-red-500 focus:ring-red-500"
                />
                <InputError class="mt-1.5" :message="form.errors.password" />
            </div>

            <button
                type="submit"
                :disabled="form.processing"
                class="w-full rounded-xl bg-red-600 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-red-700 disabled:opacity-50"
            >
                Masuk
            </button>
        </form>
    </GuestLayout>
</template>
