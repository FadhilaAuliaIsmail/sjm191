<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, useForm } from "@inertiajs/vue3";
import { computed, ref } from "vue";

const props = defineProps({
    dataMitra: Array,
});

// ===== Search =====
const search = ref("");
const dataFiltered = computed(() => {
    if (!search.value) return props.dataMitra;
    const q = search.value.toLowerCase();
    return props.dataMitra.filter(
        (m) =>
            m.cabang.toLowerCase().includes(q) ||
            m.alamat.toLowerCase().includes(q)
    );
});

// ===== Modal Tambah/Edit =====
const showModal = ref(false);
const isEditing = ref(false);
const editingId = ref(null);

const form = useForm({
    cabang: "",
    alamat: "",
    no_telp: "",
});

function openCreateModal() {
    isEditing.value = false;
    editingId.value = null;
    form.reset();
    form.clearErrors();
    showModal.value = true;
}

function openEditModal(item) {
    isEditing.value = true;
    editingId.value = item.id;
    form.cabang = item.cabang;
    form.alamat = item.alamat;
    form.no_telp = item.no_telp;
    form.clearErrors();
    showModal.value = true;
}

function closeModal() {
    showModal.value = false;
    form.reset();
    form.clearErrors();
}

function submitForm() {
    if (isEditing.value) {
        form.put(route("data-mitra.update", editingId.value), {
            onSuccess: () => closeModal(),
        });
    } else {
        form.post(route("data-mitra.store"), {
            onSuccess: () => closeModal(),
        });
    }
}

// ===== Hapus =====
const showDeleteConfirm = ref(false);
const deleteTarget = ref(null);

function confirmDelete(item) {
    deleteTarget.value = item;
    showDeleteConfirm.value = true;
}

function executeDelete() {
    form.delete(route("data-mitra.destroy", deleteTarget.value.id), {
        onSuccess: () => {
            showDeleteConfirm.value = false;
            deleteTarget.value = null;
        },
    });
}
</script>

<template>
    <Head title="Data Mitra" />
    <AuthenticatedLayout>
        <template #header>
            <h2
                class="text-lg md:text-xl font-semibold leading-tight"
                style="font-family: 'Fraunces', serif; color: #5c4a3f"
            >
                Data Mitra / Cabang
            </h2>
        </template>

        <div class="py-4 md:py-8">
            <div class="mx-auto max-w-7xl space-y-4 px-4 sm:px-6 lg:px-8">
                <!-- Toolbar -->
                <div
                    class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between"
                >
                    <input
                        v-model="search"
                        type="text"
                        placeholder="Cari cabang atau alamat..."
                        class="w-full rounded-xl border-gray-200 text-sm shadow-sm focus:border-[#D98F8F] focus:ring-[#D98F8F] sm:w-72"
                    />
                    <button
                        @click="openCreateModal"
                        class="w-full sm:w-auto inline-flex items-center justify-center rounded-xl bg-[#A31D22] px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-[#7C1519]"
                    >
                        + Tambah Mitra
                    </button>
                </div>

                <!-- Versi Card (mobile & tablet) -->
                <div class="lg:hidden space-y-3">
                    <div
                        v-for="item in dataFiltered"
                        :key="item.id"
                        class="rounded-2xl bg-white shadow-sm ring-1 ring-gray-100 p-4"
                    >
                        <p class="font-semibold text-gray-800 text-sm">
                            {{ item.cabang }}
                        </p>
                        <p class="text-sm text-gray-500 mt-1">
                            {{ item.alamat }}
                        </p>
                        <p class="text-sm text-gray-500 mt-1">
                            {{ item.no_telp }}
                        </p>
                        <div
                            class="flex gap-2 mt-3 pt-3 border-t border-gray-100"
                        >
                            <button
                                @click="openEditModal(item)"
                                class="flex-1 text-center text-xs font-semibold text-[#A31D22] border border-[#F0C6C6] rounded-lg py-2 hover:bg-[#FDF2F2] transition"
                            >
                                Edit
                            </button>
                            <button
                                @click="confirmDelete(item)"
                                class="flex-1 text-center text-xs font-semibold text-red-500 border border-red-200 rounded-lg py-2 hover:bg-red-50 transition"
                            >
                                Hapus
                            </button>
                        </div>
                    </div>

                    <div
                        v-if="dataFiltered.length === 0"
                        class="rounded-2xl bg-white shadow-sm ring-1 ring-gray-100 py-10 text-center text-sm text-gray-400"
                    >
                        Belum ada data mitra.
                    </div>
                </div>

                <!-- Versi Tabel (desktop) -->
                <div
                    class="hidden lg:block overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-gray-100"
                >
                    <table class="w-full text-sm">
                        <thead
                            class="bg-gray-50 text-left text-xs font-semibold uppercase text-gray-500"
                        >
                            <tr>
                                <th class="px-5 py-3">Cabang</th>
                                <th class="px-5 py-3">Alamat</th>
                                <th class="px-5 py-3">No. Telp</th>
                                <th class="px-5 py-3 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <tr
                                v-for="item in dataFiltered"
                                :key="item.id"
                                class="hover:bg-gray-50"
                            >
                                <td class="px-5 py-3 font-medium text-gray-800">
                                    {{ item.cabang }}
                                </td>
                                <td class="px-5 py-3 text-gray-600">
                                    {{ item.alamat }}
                                </td>
                                <td class="px-5 py-3 text-gray-600">
                                    {{ item.no_telp }}
                                </td>
                                <td class="px-5 py-3 text-right">
                                    <button
                                        @click="openEditModal(item)"
                                        class="mr-3 text-sm font-medium text-[#A31D22] hover:text-[#5C0F12]"
                                    >
                                        Edit
                                    </button>
                                    <button
                                        @click="confirmDelete(item)"
                                        class="text-sm font-medium text-red-500 hover:text-red-700"
                                    >
                                        Hapus
                                    </button>
                                </td>
                            </tr>
                            <tr v-if="dataFiltered.length === 0">
                                <td
                                    colspan="4"
                                    class="px-5 py-10 text-center text-sm text-gray-400"
                                >
                                    Belum ada data mitra.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Modal Tambah/Edit -->
        <div
            v-if="showModal"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 px-4"
            @click.self="closeModal"
        >
            <div
                class="w-full max-w-md rounded-2xl bg-white p-5 md:p-6 shadow-xl max-h-[90vh] overflow-y-auto"
            >
                <h3 class="mb-4 text-lg font-semibold text-gray-800">
                    {{ isEditing ? "Edit Mitra" : "Tambah Mitra" }}
                </h3>

                <form @submit.prevent="submitForm" class="space-y-4">
                    <div>
                        <label
                            class="mb-1 block text-sm font-medium text-gray-700"
                            >Nama Cabang</label
                        >
                        <input
                            v-model="form.cabang"
                            type="text"
                            placeholder="Misal: Cabang Kramat Jati"
                            class="w-full rounded-xl border-gray-200 text-sm shadow-sm focus:border-[#D98F8F] focus:ring-[#D98F8F]"
                        />
                        <p
                            v-if="form.errors.cabang"
                            class="mt-1 text-xs text-red-500"
                        >
                            {{ form.errors.cabang }}
                        </p>
                    </div>

                    <div>
                        <label
                            class="mb-1 block text-sm font-medium text-gray-700"
                            >Alamat</label
                        >
                        <textarea
                            v-model="form.alamat"
                            rows="2"
                            placeholder="Alamat lengkap cabang/mitra"
                            class="w-full rounded-xl border-gray-200 text-sm shadow-sm focus:border-[#D98F8F] focus:ring-[#D98F8F]"
                        ></textarea>
                        <p
                            v-if="form.errors.alamat"
                            class="mt-1 text-xs text-red-500"
                        >
                            {{ form.errors.alamat }}
                        </p>
                    </div>

                    <div>
                        <label
                            class="mb-1 block text-sm font-medium text-gray-700"
                            >No. Telp</label
                        >
                        <input
                            v-model="form.no_telp"
                            type="text"
                            placeholder="Misal: 081234567890"
                            class="w-full rounded-xl border-gray-200 text-sm shadow-sm focus:border-[#D98F8F] focus:ring-[#D98F8F]"
                        />
                        <p
                            v-if="form.errors.no_telp"
                            class="mt-1 text-xs text-red-500"
                        >
                            {{ form.errors.no_telp }}
                        </p>
                    </div>

                    <div
                        class="flex flex-col-reverse sm:flex-row sm:justify-end gap-2 pt-2"
                    >
                        <button
                            type="button"
                            @click="closeModal"
                            class="rounded-xl px-4 py-2.5 sm:py-2 text-sm font-medium text-gray-600 hover:bg-gray-100"
                        >
                            Batal
                        </button>
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="rounded-xl bg-[#A31D22] px-4 py-2.5 sm:py-2 text-sm font-semibold text-white hover:bg-[#7C1519] disabled:opacity-50"
                        >
                            {{ isEditing ? "Simpan" : "Tambah" }}
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Modal Konfirmasi Hapus -->
        <div
            v-if="showDeleteConfirm"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 px-4"
            @click.self="showDeleteConfirm = false"
        >
            <div class="w-full max-w-sm rounded-2xl bg-white p-6 shadow-xl">
                <h3 class="text-lg font-semibold text-gray-800">
                    Hapus Mitra?
                </h3>
                <p class="mt-2 text-sm text-gray-500">
                    Data mitra "{{ deleteTarget?.cabang }}" akan dihapus
                    permanen. Tindakan ini tidak bisa dibatalkan.
                </p>
                <div
                    class="mt-5 flex flex-col-reverse sm:flex-row sm:justify-end gap-2"
                >
                    <button
                        @click="showDeleteConfirm = false"
                        class="rounded-xl px-4 py-2.5 sm:py-2 text-sm font-medium text-gray-600 hover:bg-gray-100"
                    >
                        Batal
                    </button>
                    <button
                        @click="executeDelete"
                        class="rounded-xl bg-red-500 px-4 py-2.5 sm:py-2 text-sm font-semibold text-white hover:bg-red-600"
                    >
                        Hapus
                    </button>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
