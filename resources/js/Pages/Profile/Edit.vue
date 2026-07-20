<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, useForm, usePage } from "@inertiajs/vue3";
import { computed, ref } from "vue";

const page = usePage();
const user = computed(() => page.props.auth.user);

const peranLabel = computed(() =>
    user.value.peran === "pemilik_usaha" ? "Pemilik Usaha" : "Kasir"
);

// ── Form Info Profil ──────────────────────────────────────────
const infoForm = useForm({
    name: user.value.name,
    email: user.value.email,
});

function submitInfo() {
    infoForm.patch(route("profile.update"), { preserveScroll: true });
}

// ── Form Ganti Password ───────────────────────────────────────
const passwordForm = useForm({
    current_password: "",
    password: "",
    password_confirmation: "",
});

function submitPassword() {
    passwordForm.put(route("password.update"), {
        preserveScroll: true,
        onSuccess: () => passwordForm.reset(),
        onError: () => {
            if (passwordForm.errors.password)
                passwordForm.reset("password", "password_confirmation");
            if (passwordForm.errors.current_password)
                passwordForm.reset("current_password");
        },
    });
}

// ── Hapus Akun ────────────────────────────────────────────────
const showDeleteModal = ref(false);
const deleteForm = useForm({ password: "" });

function confirmDeleteAccount() {
    showDeleteModal.value = true;
}

function closeDeleteModal() {
    showDeleteModal.value = false;
    deleteForm.reset();
    deleteForm.clearErrors();
}

function submitDelete() {
    deleteForm.delete(route("profile.destroy"), {
        onError: () => {},
        onFinish: () => deleteForm.reset(),
    });
}
</script>

<template>
    <Head title="Profil Saya" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Profil Saya
            </h2>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-2xl space-y-5 px-4 sm:px-6 lg:px-8">
                <!-- Avatar + Ringkasan -->
                <div
                    class="flex items-center gap-4 rounded-2xl bg-white p-5 shadow-sm ring-1 ring-gray-100"
                >
                    <div
                        class="flex h-14 w-14 items-center justify-center rounded-full bg-red-600 text-xl font-semibold text-white"
                    >
                        {{ user.name.charAt(0) }}
                    </div>
                    <div>
                        <p class="font-semibold text-gray-800">
                            {{ user.name }}
                        </p>
                        <p class="text-sm text-gray-400">{{ user.email }}</p>
                        <span
                            class="mt-1 inline-block rounded-full bg-red-50 px-2.5 py-0.5 text-xs font-semibold text-red-600"
                        >
                            {{ peranLabel }}
                        </span>
                    </div>
                </div>

                <!-- Update Info -->
                <div
                    class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-gray-100"
                >
                    <h3 class="text-sm font-semibold text-gray-700">
                        Informasi Profil
                    </h3>
                    <p class="mt-0.5 text-xs text-gray-400">
                        Perbarui nama dan email akun Anda.
                    </p>

                    <form @submit.prevent="submitInfo" class="mt-4 space-y-4">
                        <div>
                            <label
                                class="mb-1 block text-sm font-medium text-gray-700"
                                >Nama</label
                            >
                            <input
                                v-model="infoForm.name"
                                type="text"
                                class="w-full rounded-xl border-gray-200 text-sm shadow-sm focus:border-red-400 focus:ring-red-400"
                            />
                            <p
                                v-if="infoForm.errors.name"
                                class="mt-1 text-xs text-red-500"
                            >
                                {{ infoForm.errors.name }}
                            </p>
                        </div>
                        <div>
                            <label
                                class="mb-1 block text-sm font-medium text-gray-700"
                                >Email</label
                            >
                            <input
                                v-model="infoForm.email"
                                type="email"
                                class="w-full rounded-xl border-gray-200 text-sm shadow-sm focus:border-red-400 focus:ring-red-400"
                            />
                            <p
                                v-if="infoForm.errors.email"
                                class="mt-1 text-xs text-red-500"
                            >
                                {{ infoForm.errors.email }}
                            </p>
                        </div>

                        <div class="flex items-center gap-3 pt-1">
                            <button
                                type="submit"
                                :disabled="infoForm.processing"
                                class="rounded-xl bg-red-600 px-4 py-2 text-sm font-semibold text-white hover:bg-red-700 disabled:opacity-50"
                            >
                                Simpan Perubahan
                            </button>
                            <p
                                v-if="infoForm.recentlySuccessful"
                                class="text-xs text-green-600"
                            >
                                Tersimpan.
                            </p>
                        </div>
                    </form>
                </div>

                <!-- Ganti Password -->
                <div
                    class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-gray-100"
                >
                    <h3 class="text-sm font-semibold text-gray-700">
                        Ganti Password
                    </h3>
                    <p class="mt-0.5 text-xs text-gray-400">
                        Gunakan password yang kuat dan belum pernah dipakai
                        sebelumnya.
                    </p>

                    <form
                        @submit.prevent="submitPassword"
                        class="mt-4 space-y-4"
                    >
                        <div>
                            <label
                                class="mb-1 block text-sm font-medium text-gray-700"
                                >Password Saat Ini</label
                            >
                            <input
                                v-model="passwordForm.current_password"
                                type="password"
                                class="w-full rounded-xl border-gray-200 text-sm shadow-sm focus:border-red-400 focus:ring-red-400"
                            />
                            <p
                                v-if="passwordForm.errors.current_password"
                                class="mt-1 text-xs text-red-500"
                            >
                                {{ passwordForm.errors.current_password }}
                            </p>
                        </div>
                        <div>
                            <label
                                class="mb-1 block text-sm font-medium text-gray-700"
                                >Password Baru</label
                            >
                            <input
                                v-model="passwordForm.password"
                                type="password"
                                placeholder="Minimal 8 karakter"
                                class="w-full rounded-xl border-gray-200 text-sm shadow-sm focus:border-red-400 focus:ring-red-400"
                            />
                            <p
                                v-if="passwordForm.errors.password"
                                class="mt-1 text-xs text-red-500"
                            >
                                {{ passwordForm.errors.password }}
                            </p>
                        </div>
                        <div>
                            <label
                                class="mb-1 block text-sm font-medium text-gray-700"
                                >Konfirmasi Password Baru</label
                            >
                            <input
                                v-model="passwordForm.password_confirmation"
                                type="password"
                                class="w-full rounded-xl border-gray-200 text-sm shadow-sm focus:border-red-400 focus:ring-red-400"
                            />
                        </div>

                        <div class="flex items-center gap-3 pt-1">
                            <button
                                type="submit"
                                :disabled="passwordForm.processing"
                                class="rounded-xl bg-red-600 px-4 py-2 text-sm font-semibold text-white hover:bg-red-700 disabled:opacity-50"
                            >
                                Ubah Password
                            </button>
                            <p
                                v-if="passwordForm.recentlySuccessful"
                                class="text-xs text-green-600"
                            >
                                Password diperbarui.
                            </p>
                        </div>
                    </form>
                </div>

                <!-- Hapus Akun -->
                <div
                    class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-red-100"
                >
                    <h3 class="text-sm font-semibold text-red-600">
                        Hapus Akun
                    </h3>
                    <p class="mt-0.5 text-xs text-gray-400">
                        Setelah akun dihapus, semua data terkait akan hilang
                        permanen dan Anda akan keluar dari sistem.
                    </p>
                    <button
                        @click="confirmDeleteAccount"
                        class="mt-4 rounded-xl border border-red-200 px-4 py-2 text-sm font-semibold text-red-600 hover:bg-red-50"
                    >
                        Hapus Akun Saya
                    </button>
                </div>
            </div>
        </div>

        <!-- ══ MODAL HAPUS AKUN ══════════════════════════════════ -->
        <div
            v-if="showDeleteModal"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 px-4"
            @click.self="closeDeleteModal"
        >
            <div class="w-full max-w-sm rounded-2xl bg-white p-6 shadow-xl">
                <h3 class="text-lg font-semibold text-gray-800">
                    Hapus Akun Anda?
                </h3>
                <p class="mt-2 text-sm text-gray-500">
                    Tindakan ini permanen. Masukkan password Anda untuk
                    konfirmasi.
                </p>
                <form @submit.prevent="submitDelete" class="mt-4 space-y-3">
                    <input
                        v-model="deleteForm.password"
                        type="password"
                        placeholder="Password Anda"
                        class="w-full rounded-xl border-gray-200 text-sm shadow-sm focus:border-red-400 focus:ring-red-400"
                    />
                    <p
                        v-if="deleteForm.errors.password"
                        class="text-xs text-red-500"
                    >
                        {{ deleteForm.errors.password }}
                    </p>

                    <div class="flex justify-end gap-2 pt-1">
                        <button
                            type="button"
                            @click="closeDeleteModal"
                            class="rounded-xl px-4 py-2 text-sm font-medium text-gray-600 hover:bg-gray-100"
                        >
                            Batal
                        </button>
                        <button
                            type="submit"
                            :disabled="deleteForm.processing"
                            class="rounded-xl bg-red-500 px-4 py-2 text-sm font-semibold text-white hover:bg-red-600 disabled:opacity-50"
                        >
                            Hapus Akun
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
