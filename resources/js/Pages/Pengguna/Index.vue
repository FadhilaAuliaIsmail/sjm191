<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, useForm, usePage } from "@inertiajs/vue3";
import { computed, ref } from "vue";

const props = defineProps({
    pengguna: Array,
});

const page = usePage();
const authUserId = computed(() => page.props.auth.user.id);

const search = ref("");
const filtered = computed(() => {
    if (!search.value) return props.pengguna;
    const q = search.value.toLowerCase();
    return props.pengguna.filter(
        (p) =>
            p.name.toLowerCase().includes(q) ||
            p.email.toLowerCase().includes(q)
    );
});

function peranLabel(peran) {
    return peran === "pemilik_usaha" ? "Pemilik Usaha" : "Kasir";
}

function peranBadge(peran) {
    return peran === "pemilik_usaha"
        ? "bg-purple-50 text-purple-600"
        : "bg-red-50 text-red-500";
}

// ── Modal Tambah/Edit ─────────────────────────────────────────
const showModal = ref(false);
const isEditing = ref(false);
const editingId = ref(null);

const form = useForm({
    name: "",
    email: "",
    password: "",
    peran: "kasir",
});

function openCreate() {
    isEditing.value = false;
    editingId.value = null;
    form.reset();
    form.clearErrors();
    showModal.value = true;
}

function openEdit(item) {
    isEditing.value = true;
    editingId.value = item.id;
    form.name = item.name;
    form.email = item.email;
    form.password = "";
    form.peran = item.peran;
    form.clearErrors();
    showModal.value = true;
}

function closeModal() {
    showModal.value = false;
    form.reset();
    form.clearErrors();
}

function submit() {
    if (isEditing.value) {
        form.put(route("pengguna.update", editingId.value), {
            onSuccess: () => closeModal(),
        });
    } else {
        form.post(route("pengguna.store"), {
            onSuccess: () => closeModal(),
        });
    }
}

// ── Hapus ─────────────────────────────────────────────────────
const showDeleteConfirm = ref(false);
const deleteTarget = ref(null);
const deleteForm = useForm({});

function confirmDelete(item) {
    deleteTarget.value = item;
    showDeleteConfirm.value = true;
}

function executeDelete() {
    deleteForm.delete(route("pengguna.destroy", deleteTarget.value.id), {
        onSuccess: () => {
            showDeleteConfirm.value = false;
            deleteTarget.value = null;
        },
    });
}
</script>

<template>
    <Head title="Kelola Pengguna" />
    <AuthenticatedLayout>
        <template #header>
            <div>
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    Kelola Pengguna
                </h2>
                <p class="text-sm text-gray-400 mt-0.5">
                    {{ pengguna.length }} pengguna terdaftar
                </p>
            </div>
        </template>

        <!-- Aksi + Search -->
        <div class="flex items-center justify-between mb-4">
            <input
                v-model="search"
                type="text"
                placeholder="Cari nama atau email..."
                class="w-full max-w-sm rounded-xl border border-gray-200 px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-red-300"
            />
            <button
                @click="openCreate"
                class="flex items-center gap-2 bg-red-600 hover:bg-red-700 text-white text-sm font-semibold px-4 py-2.5 rounded-xl transition ml-4"
            >
                <svg
                    class="w-4 h-4"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M12 4v16m8-8H4"
                    />
                </svg>
                Tambah Pengguna
            </button>
        </div>

        <!-- Tabel -->
        <div
            class="overflow-hidden rounded-2xl bg-white shadow-sm border border-gray-100"
        >
            <table class="w-full text-sm">
                <thead
                    class="bg-gray-50 text-left text-xs font-semibold uppercase text-gray-500"
                >
                    <tr>
                        <th class="px-5 py-3">Nama</th>
                        <th class="px-5 py-3">Email</th>
                        <th class="px-5 py-3">Peran</th>
                        <th class="px-5 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <tr
                        v-for="item in filtered"
                        :key="item.id"
                        class="hover:bg-gray-50"
                    >
                        <td class="px-5 py-3 font-medium text-gray-800">
                            {{ item.name }}
                            <span
                                v-if="item.id === authUserId"
                                class="ml-1.5 rounded-full bg-gray-100 px-2 py-0.5 text-[10px] font-semibold text-gray-500"
                                >Anda</span
                            >
                        </td>
                        <td class="px-5 py-3 text-gray-500">
                            {{ item.email }}
                        </td>
                        <td class="px-5 py-3">
                            <span
                                :class="peranBadge(item.peran)"
                                class="rounded-full px-2.5 py-1 text-xs font-semibold"
                            >
                                {{ peranLabel(item.peran) }}
                            </span>
                        </td>
                        <td class="px-5 py-3 text-right">
                            <button
                                @click="openEdit(item)"
                                class="mr-3 text-sm font-medium text-red-600 hover:text-red-800"
                            >
                                Edit
                            </button>
                            <button
                                v-if="item.id !== authUserId"
                                @click="confirmDelete(item)"
                                class="text-sm font-medium text-red-500 hover:text-red-700"
                            >
                                Hapus
                            </button>
                            <span v-else class="text-xs text-gray-300">—</span>
                        </td>
                    </tr>
                    <tr v-if="filtered.length === 0">
                        <td
                            colspan="4"
                            class="px-5 py-10 text-center text-sm text-gray-400"
                        >
                            Tidak ada pengguna ditemukan.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- ══ MODAL TAMBAH / EDIT ════════════════════════════════ -->
        <Teleport to="body">
            <div
                v-if="showModal"
                class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm px-4"
                @click.self="closeModal"
            >
                <div
                    class="max-h-[90vh] w-full max-w-md overflow-y-auto rounded-2xl bg-white p-6 shadow-xl"
                >
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-800">
                            {{
                                isEditing ? "Edit Pengguna" : "Tambah Pengguna"
                            }}
                        </h3>
                        <button
                            @click="closeModal"
                            class="text-gray-400 hover:text-gray-600"
                        >
                            <svg
                                class="w-5 h-5"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12"
                                />
                            </svg>
                        </button>
                    </div>

                    <form @submit.prevent="submit" class="space-y-4">
                        <div>
                            <label
                                class="mb-1 block text-xs font-medium text-gray-500"
                                >Nama <span class="text-red-400">*</span></label
                            >
                            <input
                                v-model="form.name"
                                type="text"
                                placeholder="Nama lengkap"
                                class="w-full rounded-xl border border-gray-200 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-red-300"
                                :class="{ 'border-red-400': form.errors.name }"
                            />
                            <p
                                v-if="form.errors.name"
                                class="mt-1 text-xs text-red-500"
                            >
                                {{ form.errors.name }}
                            </p>
                        </div>

                        <div>
                            <label
                                class="mb-1 block text-xs font-medium text-gray-500"
                                >Email
                                <span class="text-red-400">*</span></label
                            >
                            <input
                                v-model="form.email"
                                type="email"
                                placeholder="nama@email.com"
                                class="w-full rounded-xl border border-gray-200 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-red-300"
                                :class="{ 'border-red-400': form.errors.email }"
                            />
                            <p
                                v-if="form.errors.email"
                                class="mt-1 text-xs text-red-500"
                            >
                                {{ form.errors.email }}
                            </p>
                        </div>

                        <div>
                            <label
                                class="mb-1 block text-xs font-medium text-gray-500"
                            >
                                Kata Sandi
                                <span
                                    v-if="isEditing"
                                    class="font-normal text-gray-400"
                                    >(kosongkan jika tidak diubah)</span
                                >
                                <span v-else class="text-red-400">*</span>
                            </label>
                            <input
                                v-model="form.password"
                                type="password"
                                placeholder="Minimal 8 karakter"
                                class="w-full rounded-xl border border-gray-200 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-red-300"
                                :class="{
                                    'border-red-400': form.errors.password,
                                }"
                            />
                            <p
                                v-if="form.errors.password"
                                class="mt-1 text-xs text-red-500"
                            >
                                {{ form.errors.password }}
                            </p>
                        </div>

                        <div>
                            <label
                                class="mb-1 block text-xs font-medium text-gray-500"
                                >Peran
                                <span class="text-red-400">*</span></label
                            >
                            <div class="flex gap-2">
                                <button
                                    type="button"
                                    @click="form.peran = 'kasir'"
                                    :class="
                                        form.peran === 'kasir'
                                            ? 'border-red-500 bg-red-50 text-red-600'
                                            : 'border-gray-200 text-gray-500 hover:bg-gray-50'
                                    "
                                    class="flex-1 rounded-xl border px-3 py-2 text-sm font-medium transition"
                                >
                                    Kasir
                                </button>
                                <button
                                    type="button"
                                    @click="form.peran = 'pemilik_usaha'"
                                    :class="
                                        form.peran === 'pemilik_usaha'
                                            ? 'border-red-500 bg-red-50 text-red-600'
                                            : 'border-gray-200 text-gray-500 hover:bg-gray-50'
                                    "
                                    class="flex-1 rounded-xl border px-3 py-2 text-sm font-medium transition"
                                >
                                    Pemilik Usaha
                                </button>
                            </div>
                            <p
                                v-if="form.errors.peran"
                                class="mt-1 text-xs text-red-500"
                            >
                                {{ form.errors.peran }}
                            </p>
                        </div>

                        <div class="flex gap-3 pt-1">
                            <button
                                type="button"
                                @click="closeModal"
                                class="flex-1 py-2.5 rounded-xl border border-gray-200 text-sm font-semibold text-gray-600 hover:bg-gray-50"
                            >
                                Batal
                            </button>
                            <button
                                type="submit"
                                :disabled="form.processing"
                                class="flex-1 py-2.5 rounded-xl bg-red-600 hover:bg-red-700 text-white text-sm font-semibold disabled:opacity-60"
                            >
                                {{
                                    form.processing
                                        ? "Menyimpan..."
                                        : isEditing
                                        ? "Simpan Perubahan"
                                        : "Tambah"
                                }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </Teleport>

        <!-- ══ MODAL HAPUS ════════════════════════════════════════ -->
        <Teleport to="body">
            <div
                v-if="showDeleteConfirm"
                class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm px-4"
                @click.self="showDeleteConfirm = false"
            >
                <div class="w-full max-w-sm rounded-2xl bg-white p-6 shadow-xl">
                    <div
                        class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center mb-4 mx-auto"
                    >
                        <svg
                            class="w-6 h-6 text-red-500"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M19 7l-.867 12.142A2 2 0 0 1 16.138 21H7.862a2 2 0 0 1-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 0 0-1-1h-4a1 1 0 0 0-1 1v3M4 7h16"
                            />
                        </svg>
                    </div>
                    <h3 class="text-center font-bold text-gray-800 mb-1">
                        Hapus Pengguna?
                    </h3>
                    <p class="text-center text-sm text-gray-500 mb-5">
                        Akun
                        <span class="font-semibold text-gray-700">{{
                            deleteTarget?.name
                        }}</span>
                        akan dihapus permanen.
                    </p>
                    <div class="flex gap-3">
                        <button
                            @click="showDeleteConfirm = false"
                            class="flex-1 py-2 rounded-xl border border-gray-200 text-sm font-semibold text-gray-600 hover:bg-gray-50"
                        >
                            Batal
                        </button>
                        <button
                            @click="executeDelete"
                            :disabled="deleteForm.processing"
                            class="flex-1 py-2 rounded-xl bg-red-500 text-white text-sm font-semibold hover:bg-red-600 disabled:opacity-60"
                        >
                            {{
                                deleteForm.processing ? "Menghapus..." : "Hapus"
                            }}
                        </button>
                    </div>
                </div>
            </div>
        </Teleport>
    </AuthenticatedLayout>
</template>
