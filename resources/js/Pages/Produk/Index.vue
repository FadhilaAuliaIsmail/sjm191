<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, router, useForm } from "@inertiajs/vue3";
import { computed, ref } from "vue";

const props = defineProps({ produk: Array });

const unitOptions = [
    "pcs",
    "kg",
    "gram",
    "ons",
    "liter",
    "ml",
    "lusin",
    "karton",
    "dus",
    "botol",
    "pack",
    "lembar",
];

// ── Helpers ───────────────────────────────────────────────────
const search = ref("");
const filtered = computed(() => {
    if (!search.value) return props.produk;
    return props.produk.filter((p) =>
        p.nama_produk.toLowerCase().includes(search.value.toLowerCase())
    );
});

function formatRupiah(val) {
    return new Intl.NumberFormat("id-ID", {
        style: "currency",
        currency: "IDR",
        minimumFractionDigits: 0,
    }).format(val);
}

function stokBadge(stok) {
    if (stok === 0) return { text: "Habis", cls: "bg-red-100 text-red-600" };
    if (stok <= 10)
        return { text: "Hampir Habis", cls: "bg-yellow-100 text-yellow-700" };
    return { text: "Tersedia", cls: "bg-green-100 text-green-700" };
}

// ── Satuan helpers ────────────────────────────────────────────
function parseSatuan(satuan) {
    if (!satuan) return { angka: "", unit: "pcs", isCustom: false };
    const parts = satuan.trim().split(/\s+/);
    if (parts.length >= 2 && !isNaN(parts[0])) {
        const unit = parts.slice(1).join(" ");
        return { angka: parts[0], unit, isCustom: !unitOptions.includes(unit) };
    }
    return { angka: "", unit: satuan, isCustom: !unitOptions.includes(satuan) };
}

// ── Modal Tambah ──────────────────────────────────────────────
const showTambah = ref(false);
const satuanAngka = ref("");
const satuanUnit = ref("pcs");
const customUnit = ref("");
const isCustomUnit = ref(false);
const fotoPreview = ref(null);

const formTambah = useForm({
    nama_produk: "",
    harga: "",
    stok: "",
    satuan: "",
    deskripsi: "",
    foto: null,
});

const satuanFinal = computed(() => {
    const unit = isCustomUnit.value ? customUnit.value : satuanUnit.value;
    if (satuanAngka.value && unit) return `${satuanAngka.value} ${unit}`;
    return unit || "";
});

function openTambah() {
    formTambah.reset();
    formTambah.clearErrors();
    satuanAngka.value = "";
    satuanUnit.value = "pcs";
    customUnit.value = "";
    isCustomUnit.value = false;
    fotoPreview.value = null;
    showTambah.value = true;
}

function submitTambah() {
    formTambah.satuan = satuanFinal.value;
    formTambah.post(route("produk.store"), {
        forceFormData: true,
        onSuccess: () => {
            showTambah.value = false;
        },
    });
}

function onFotoChange(e) {
    const file = e.target.files[0];
    if (!file) return;
    formTambah.foto = file;
    fotoPreview.value = URL.createObjectURL(file);
}
function removeFoto() {
    formTambah.foto = null;
    fotoPreview.value = null;
}

function pilihUnit(u, isEdit = false) {
    if (isEdit) {
        if (u === "__custom__") {
            editIsCustomUnit.value = true;
            editSatuanUnit.value = "";
        } else {
            editIsCustomUnit.value = false;
            editSatuanUnit.value = u;
        }
    } else {
        if (u === "__custom__") {
            isCustomUnit.value = true;
            satuanUnit.value = "";
        } else {
            isCustomUnit.value = false;
            satuanUnit.value = u;
        }
    }
}

// ── Modal Edit ────────────────────────────────────────────────
const showEdit = ref(false);
const editSatuanAngka = ref("");
const editSatuanUnit = ref("pcs");
const editCustomUnit = ref("");
const editIsCustomUnit = ref(false);
const editFotoPreview = ref(null);

const formEdit = useForm({
    _method: "PUT",
    nama_produk: "",
    harga: "",
    stok: "",
    satuan: "",
    deskripsi: "",
    foto: null,
});
const editProdukId = ref(null);

const editSatuanFinal = computed(() => {
    const unit = editIsCustomUnit.value
        ? editCustomUnit.value
        : editSatuanUnit.value;
    if (editSatuanAngka.value && unit)
        return `${editSatuanAngka.value} ${unit}`;
    return unit || "";
});

function openEdit(p) {
    editProdukId.value = p.id;
    formEdit.nama_produk = p.nama_produk;
    formEdit.harga = p.harga;
    formEdit.stok = p.stok;
    formEdit.deskripsi = p.deskripsi ?? "";
    formEdit.foto = null;
    formEdit.clearErrors();
    editFotoPreview.value = p.foto ? `/storage/${p.foto}` : null;
    const s = parseSatuan(p.satuan);
    editSatuanAngka.value = s.angka;
    editSatuanUnit.value = s.isCustom ? "pcs" : s.unit;
    editCustomUnit.value = s.isCustom ? s.unit : "";
    editIsCustomUnit.value = s.isCustom;
    showEdit.value = true;
}

function submitEdit() {
    formEdit.satuan = editSatuanFinal.value;
    formEdit.post(route("produk.update", editProdukId.value), {
        forceFormData: true,
        onSuccess: () => {
            showEdit.value = false;
        },
    });
}

function onEditFotoChange(e) {
    const file = e.target.files[0];
    if (!file) return;
    formEdit.foto = file;
    editFotoPreview.value = URL.createObjectURL(file);
}
function removeEditFoto() {
    formEdit.foto = null;
    editFotoPreview.value = null;
}

// ── Modal Detail ──────────────────────────────────────────────
const showDetail = ref(false);
const detailProduk = ref(null);

function openDetail(p) {
    detailProduk.value = p;
    showDetail.value = true;
}

// ── Modal Hapus ───────────────────────────────────────────────
const showHapus = ref(false);
const produkToDelete = ref(null);

function confirmDelete(p) {
    produkToDelete.value = p;
    showHapus.value = true;
}

function doDelete() {
    router.delete(route("produk.destroy", produkToDelete.value.id), {
        onSuccess: () => {
            showHapus.value = false;
            produkToDelete.value = null;
        },
    });
}
</script>

<template>
    <Head title="Produk" />
    <AuthenticatedLayout>
        <template #header>
            <div>
                <h1
                    class="text-xl font-semibold"
                    style="font-family: 'Fraunces', serif; color: #5c4a3f"
                >
                    Kelola Produk
                </h1>
                <p class="text-sm text-gray-400 mt-0.5">
                    {{ produk.length }} produk terdaftar
                </p>
            </div>
        </template>

        <!-- Aksi -->
        <div class="flex justify-end mb-4">
            <button
                @click="openTambah"
                class="w-full sm:w-auto flex items-center justify-center gap-2 bg-[#A31D22] hover:bg-[#8A171B] text-white text-sm font-semibold px-4 py-2.5 rounded-xl transition"
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
                Tambah Produk
            </button>
        </div>

        <!-- Search -->
        <div class="mb-6">
            <div class="relative max-w-md">
                <svg
                    class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M21 21l-4.35-4.35M17 11A6 6 0 1 1 5 11a6 6 0 0 1 12 0z"
                    />
                </svg>
                <input
                    v-model="search"
                    type="text"
                    placeholder="Cari produk..."
                    class="w-full pl-10 pr-4 py-2.5 bg-white border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#D98F8F]"
                />
            </div>
        </div>

        <!-- Empty State -->
        <div
            v-if="filtered.length === 0"
            class="flex flex-col items-center justify-center py-24 text-center"
        >
            <div
                class="w-20 h-20 bg-[#FDF2F2] rounded-full flex items-center justify-center mb-4"
            >
                <svg
                    class="w-10 h-10 text-[#D98F8F]"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="1.5"
                        d="M20 7l-8-4-8 4m16 0v10l-8 4m0-14L4 17m8 4V11"
                    />
                </svg>
            </div>
            <p class="text-gray-500 font-medium">Belum ada produk</p>
            <p class="text-gray-400 text-sm mt-1">
                Tambahkan produk pertama Anda
            </p>
            <button
                @click="openTambah"
                class="mt-4 bg-[#A31D22] text-white text-sm font-semibold px-5 py-2 rounded-xl hover:bg-[#8A171B] transition"
            >
                Tambah Produk
            </button>
        </div>

        <!-- List -->
        <div
            v-else
            class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4"
        >
            <div
                v-for="p in filtered"
                :key="p.id"
                class="bg-white rounded-2xl overflow-hidden shadow-sm border border-gray-100 hover:shadow-md transition group flex flex-col"
            >
                <!-- Baris atas: Foto + Nama + Harga -->
                <div class="flex items-start gap-3 p-3">
                    <div
                        class="relative w-16 h-16 shrink-0 bg-gray-50 overflow-hidden cursor-pointer rounded-xl"
                        @click="openDetail(p)"
                    >
                        <img
                            v-if="p.foto"
                            :src="`/storage/${p.foto}`"
                            :alt="p.nama_produk"
                            class="w-full h-full object-cover group-hover:scale-105 transition duration-300"
                        />
                        <div
                            v-else
                            class="w-full h-full flex items-center justify-center"
                        >
                            <svg
                                class="w-7 h-7 text-gray-200"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="1.5"
                                    d="M4 16l4.586-4.586a2 2 0 0 1 2.828 0L16 16m-2-2 1.586-1.586a2 2 0 0 1 2.828 0L20 14m-6-6h.01M6 20h12a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2H6a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2z"
                                />
                            </svg>
                        </div>
                    </div>

                    <div class="flex-1 min-w-0">
                        <div class="flex items-start justify-between gap-2">
                            <p
                                class="font-semibold text-gray-800 text-sm truncate cursor-pointer hover:text-[#A31D22]"
                                @click="openDetail(p)"
                            >
                                {{ p.nama_produk }}
                            </p>
                            <span
                                :class="stokBadge(p.stok).cls"
                                class="shrink-0 text-[10px] font-semibold px-2 py-0.5 rounded-full whitespace-nowrap"
                            >
                                {{ stokBadge(p.stok).text }}
                            </span>
                        </div>
                        <p class="text-[#A31D22] font-bold text-sm mt-0.5">
                            {{ formatRupiah(p.harga) }}
                        </p>
                        <p class="text-xs text-gray-400 mt-0.5">
                            Stok: {{ p.stok }} · {{ p.satuan }}
                        </p>
                    </div>
                </div>

                <!-- Deskripsi, cuma tampil kalau ada -->
                <div v-if="p.deskripsi" class="px-3 pb-2">
                    <p class="text-xs text-gray-500 line-clamp-2">
                        {{ p.deskripsi }}
                    </p>
                </div>

                <!-- Tombol, full width sejajar -->
                <div
                    class="flex gap-2 p-3 pt-2 mt-auto border-t border-gray-50"
                >
                    <button
                        @click="openEdit(p)"
                        class="flex-1 text-center text-xs font-semibold text-[#A31D22] border border-[#F0C6C6] rounded-lg py-2 hover:bg-[#FDF2F2] transition"
                    >
                        Edit
                    </button>
                    <button
                        @click="confirmDelete(p)"
                        class="flex-1 text-center text-xs font-semibold text-red-500 border border-red-200 rounded-lg py-2 hover:bg-red-50 transition"
                    >
                        Hapus
                    </button>
                </div>
            </div>
        </div>
        <!-- ══ MODAL TAMBAH ══════════════════════════════════════ -->
        <Teleport to="body">
            <div
                v-if="showTambah"
                class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm p-4"
                @click.self="showTambah = false"
            >
                <div
                    class="bg-white rounded-2xl shadow-xl w-full max-w-md max-h-[90vh] overflow-y-auto"
                >
                    <div
                        class="flex items-center justify-between px-5 py-4 border-b border-gray-100"
                    >
                        <h2 class="font-bold text-gray-800">Tambah Produk</h2>
                        <button
                            @click="showTambah = false"
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
                    <form @submit.prevent="submitTambah" class="p-5 space-y-4">
                        <!-- Foto -->
                        <div class="flex items-center gap-4">
                            <div class="relative shrink-0">
                                <div
                                    v-if="!fotoPreview"
                                    @click="$refs.fotoInput.click()"
                                    class="w-16 h-16 rounded-xl border-2 border-dashed border-gray-200 flex items-center justify-center cursor-pointer hover:border-[#D98F8F] bg-gray-50"
                                >
                                    <svg
                                        class="w-6 h-6 text-gray-300"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="1.5"
                                            d="M12 4v16m8-8H4"
                                        />
                                    </svg>
                                </div>
                                <div v-else class="relative w-16 h-16">
                                    <img
                                        :src="fotoPreview"
                                        class="w-16 h-16 rounded-xl object-cover"
                                    />
                                    <button
                                        type="button"
                                        @click="removeFoto"
                                        class="absolute -top-1.5 -right-1.5 bg-white rounded-full p-0.5 shadow border border-gray-100"
                                    >
                                        <svg
                                            class="w-3 h-3 text-red-500"
                                            fill="none"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2.5"
                                                d="M6 18L18 6M6 6l12 12"
                                            />
                                        </svg>
                                    </button>
                                </div>
                                <input
                                    ref="fotoInput"
                                    type="file"
                                    accept="image/*"
                                    class="hidden"
                                    @change="onFotoChange"
                                />
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-700">
                                    Foto Produk
                                </p>
                                <button
                                    type="button"
                                    @click="$refs.fotoInput.click()"
                                    class="text-xs text-[#A31D22] hover:underline mt-0.5"
                                >
                                    {{
                                        fotoPreview
                                            ? "Ganti foto"
                                            : "Upload foto"
                                    }}
                                </button>
                                <p class="text-xs text-gray-400 mt-0.5">
                                    JPG, PNG, maks 2MB
                                </p>
                            </div>
                        </div>
                        <!-- Nama -->
                        <div>
                            <label
                                class="block text-xs font-medium text-gray-500 mb-1"
                                >Nama Produk
                                <span class="text-red-400">*</span></label
                            >
                            <input
                                v-model="formTambah.nama_produk"
                                type="text"
                                placeholder="Contoh: Susu Coklat Ultra"
                                class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#D98F8F]"
                                :class="{
                                    'border-red-400':
                                        formTambah.errors.nama_produk,
                                }"
                            />
                            <p
                                v-if="formTambah.errors.nama_produk"
                                class="text-red-500 text-xs mt-1"
                            >
                                {{ formTambah.errors.nama_produk }}
                            </p>
                        </div>
                        <!-- Harga & Stok -->
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label
                                    class="block text-xs font-medium text-gray-500 mb-1"
                                    >Harga
                                    <span class="text-red-400">*</span></label
                                >
                                <div class="relative">
                                    <span
                                        class="absolute left-3 top-1/2 -translate-y-1/2 text-xs text-gray-400"
                                        >Rp</span
                                    >
                                    <input
                                        v-model="formTambah.harga"
                                        type="number"
                                        placeholder="0"
                                        min="0"
                                        class="w-full border border-gray-200 rounded-xl pl-8 pr-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#D98F8F]"
                                        :class="{
                                            'border-red-400':
                                                formTambah.errors.harga,
                                        }"
                                    />
                                </div>
                                <p
                                    v-if="formTambah.errors.harga"
                                    class="text-red-500 text-xs mt-1"
                                >
                                    {{ formTambah.errors.harga }}
                                </p>
                            </div>
                            <div>
                                <label
                                    class="block text-xs font-medium text-gray-500 mb-1"
                                    >Stok
                                    <span class="text-red-400">*</span></label
                                >
                                <input
                                    v-model="formTambah.stok"
                                    type="number"
                                    placeholder="0"
                                    min="0"
                                    class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#D98F8F]"
                                    :class="{
                                        'border-red-400':
                                            formTambah.errors.stok,
                                    }"
                                />
                                <p
                                    v-if="formTambah.errors.stok"
                                    class="text-red-500 text-xs mt-1"
                                >
                                    {{ formTambah.errors.stok }}
                                </p>
                            </div>
                        </div>
                        <!-- Satuan -->
                        <div>
                            <label
                                class="block text-xs font-medium text-gray-500 mb-1"
                                >Satuan
                                <span class="text-red-400">*</span></label
                            >
                            <div class="flex gap-2">
                                <input
                                    v-model="satuanAngka"
                                    type="number"
                                    placeholder="Jumlah"
                                    min="0"
                                    class="w-24 border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#D98F8F]"
                                />
                                <input
                                    v-if="isCustomUnit"
                                    v-model="customUnit"
                                    type="text"
                                    placeholder="Satuan baru..."
                                    autofocus
                                    class="flex-1 border border-[#D98F8F] rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#D98F8F]"
                                />
                                <select
                                    v-else
                                    :value="satuanUnit"
                                    @change="pilihUnit($event.target.value)"
                                    class="flex-1 border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#D98F8F] bg-white"
                                >
                                    <option
                                        v-for="u in unitOptions"
                                        :key="u"
                                        :value="u"
                                    >
                                        {{ u }}
                                    </option>
                                    <option value="__custom__">
                                        + Tambah satuan...
                                    </option>
                                </select>
                                <button
                                    v-if="isCustomUnit"
                                    type="button"
                                    @click="
                                        isCustomUnit = false;
                                        satuanUnit = 'pcs';
                                    "
                                    class="px-2.5 rounded-xl border border-gray-200 text-gray-400 hover:bg-gray-50 text-xs"
                                >
                                    ✕
                                </button>
                            </div>
                            <div
                                v-if="satuanFinal"
                                class="mt-1.5 flex items-center gap-1.5"
                            >
                                <span class="text-xs text-gray-400"
                                    >Hasil:</span
                                >
                                <span
                                    class="bg-[#FDF2F2] text-[#8A171B] text-xs font-semibold px-2 py-0.5 rounded-full"
                                    >{{ satuanFinal }}</span
                                >
                            </div>
                            <p
                                v-if="formTambah.errors.satuan"
                                class="text-red-500 text-xs mt-1"
                            >
                                {{ formTambah.errors.satuan }}
                            </p>
                        </div>
                        <!-- Deskripsi -->
                        <div>
                            <label
                                class="block text-xs font-medium text-gray-500 mb-1"
                                >Deskripsi
                                <span class="text-gray-300"
                                    >(opsional)</span
                                ></label
                            >
                            <textarea
                                v-model="formTambah.deskripsi"
                                rows="2"
                                placeholder="Deskripsi singkat produk..."
                                class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#D98F8F] resize-none"
                            />
                        </div>
                        <!-- Tombol -->
                        <div class="flex gap-3 pt-1">
                            <button
                                type="button"
                                @click="showTambah = false"
                                class="flex-1 py-2.5 rounded-xl border border-gray-200 text-sm font-semibold text-gray-600 hover:bg-gray-50"
                            >
                                Batal
                            </button>
                            <button
                                type="submit"
                                :disabled="formTambah.processing"
                                class="flex-1 py-2.5 rounded-xl bg-[#A31D22] hover:bg-[#8A171B] text-white text-sm font-semibold disabled:opacity-60"
                            >
                                {{
                                    formTambah.processing
                                        ? "Menyimpan..."
                                        : "Simpan"
                                }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </Teleport>

        <!-- ══ MODAL EDIT ════════════════════════════════════════ -->
        <Teleport to="body">
            <div
                v-if="showEdit"
                class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm p-4"
                @click.self="showEdit = false"
            >
                <div
                    class="bg-white rounded-2xl shadow-xl w-full max-w-md max-h-[90vh] overflow-y-auto"
                >
                    <div
                        class="flex items-center justify-between px-5 py-4 border-b border-gray-100"
                    >
                        <h2 class="font-bold text-gray-800">Edit Produk</h2>
                        <button
                            @click="showEdit = false"
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
                    <form @submit.prevent="submitEdit" class="p-5 space-y-4">
                        <!-- Foto -->
                        <div class="flex items-center gap-4">
                            <div class="relative shrink-0">
                                <div
                                    v-if="!editFotoPreview"
                                    @click="$refs.editFotoInput.click()"
                                    class="w-16 h-16 rounded-xl border-2 border-dashed border-gray-200 flex items-center justify-center cursor-pointer hover:border-[#D98F8F] bg-gray-50"
                                >
                                    <svg
                                        class="w-6 h-6 text-gray-300"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="1.5"
                                            d="M12 4v16m8-8H4"
                                        />
                                    </svg>
                                </div>
                                <div v-else class="relative w-16 h-16">
                                    <img
                                        :src="editFotoPreview"
                                        class="w-16 h-16 rounded-xl object-cover"
                                    />
                                    <button
                                        type="button"
                                        @click="removeEditFoto"
                                        class="absolute -top-1.5 -right-1.5 bg-white rounded-full p-0.5 shadow border border-gray-100"
                                    >
                                        <svg
                                            class="w-3 h-3 text-red-500"
                                            fill="none"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2.5"
                                                d="M6 18L18 6M6 6l12 12"
                                            />
                                        </svg>
                                    </button>
                                </div>
                                <input
                                    ref="editFotoInput"
                                    type="file"
                                    accept="image/*"
                                    class="hidden"
                                    @change="onEditFotoChange"
                                />
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-700">
                                    Foto Produk
                                </p>
                                <button
                                    type="button"
                                    @click="$refs.editFotoInput.click()"
                                    class="text-xs text-[#A31D22] hover:underline mt-0.5"
                                >
                                    {{
                                        editFotoPreview
                                            ? "Ganti foto"
                                            : "Upload foto"
                                    }}
                                </button>
                                <p class="text-xs text-gray-400 mt-0.5">
                                    JPG, PNG, maks 2MB
                                </p>
                            </div>
                        </div>
                        <!-- Nama -->
                        <div>
                            <label
                                class="block text-xs font-medium text-gray-500 mb-1"
                                >Nama Produk
                                <span class="text-red-400">*</span></label
                            >
                            <input
                                v-model="formEdit.nama_produk"
                                type="text"
                                class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#D98F8F]"
                                :class="{
                                    'border-red-400':
                                        formEdit.errors.nama_produk,
                                }"
                            />
                            <p
                                v-if="formEdit.errors.nama_produk"
                                class="text-red-500 text-xs mt-1"
                            >
                                {{ formEdit.errors.nama_produk }}
                            </p>
                        </div>
                        <!-- Harga & Stok -->
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label
                                    class="block text-xs font-medium text-gray-500 mb-1"
                                    >Harga
                                    <span class="text-red-400">*</span></label
                                >
                                <div class="relative">
                                    <span
                                        class="absolute left-3 top-1/2 -translate-y-1/2 text-xs text-gray-400"
                                        >Rp</span
                                    >
                                    <input
                                        v-model="formEdit.harga"
                                        type="number"
                                        min="0"
                                        class="w-full border border-gray-200 rounded-xl pl-8 pr-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#D98F8F]"
                                        :class="{
                                            'border-red-400':
                                                formEdit.errors.harga,
                                        }"
                                    />
                                </div>
                                <p
                                    v-if="formEdit.errors.harga"
                                    class="text-red-500 text-xs mt-1"
                                >
                                    {{ formEdit.errors.harga }}
                                </p>
                            </div>
                            <div>
                                <label
                                    class="block text-xs font-medium text-gray-500 mb-1"
                                    >Stok
                                    <span class="text-red-400">*</span></label
                                >
                                <input
                                    v-model="formEdit.stok"
                                    type="number"
                                    min="0"
                                    class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#D98F8F]"
                                    :class="{
                                        'border-red-400': formEdit.errors.stok,
                                    }"
                                />
                                <p
                                    v-if="formEdit.errors.stok"
                                    class="text-red-500 text-xs mt-1"
                                >
                                    {{ formEdit.errors.stok }}
                                </p>
                            </div>
                        </div>
                        <!-- Satuan -->
                        <div>
                            <label
                                class="block text-xs font-medium text-gray-500 mb-1"
                                >Satuan
                                <span class="text-red-400">*</span></label
                            >
                            <div class="flex gap-2">
                                <input
                                    v-model="editSatuanAngka"
                                    type="number"
                                    placeholder="Jumlah"
                                    min="0"
                                    class="w-24 border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#D98F8F]"
                                />
                                <input
                                    v-if="editIsCustomUnit"
                                    v-model="editCustomUnit"
                                    type="text"
                                    placeholder="Satuan baru..."
                                    class="flex-1 border border-[#D98F8F] rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#D98F8F]"
                                />
                                <select
                                    v-else
                                    :value="editSatuanUnit"
                                    @change="
                                        pilihUnit($event.target.value, true)
                                    "
                                    class="flex-1 border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#D98F8F] bg-white"
                                >
                                    <option
                                        v-for="u in unitOptions"
                                        :key="u"
                                        :value="u"
                                    >
                                        {{ u }}
                                    </option>
                                    <option value="__custom__">
                                        + Tambah satuan...
                                    </option>
                                </select>
                                <button
                                    v-if="editIsCustomUnit"
                                    type="button"
                                    @click="
                                        editIsCustomUnit = false;
                                        editSatuanUnit = 'pcs';
                                    "
                                    class="px-2.5 rounded-xl border border-gray-200 text-gray-400 hover:bg-gray-50 text-xs"
                                >
                                    ✕
                                </button>
                            </div>
                            <div
                                v-if="editSatuanFinal"
                                class="mt-1.5 flex items-center gap-1.5"
                            >
                                <span class="text-xs text-gray-400"
                                    >Hasil:</span
                                >
                                <span
                                    class="bg-[#FDF2F2] text-[#8A171B] text-xs font-semibold px-2 py-0.5 rounded-full"
                                    >{{ editSatuanFinal }}</span
                                >
                            </div>
                            <p
                                v-if="formEdit.errors.satuan"
                                class="text-red-500 text-xs mt-1"
                            >
                                {{ formEdit.errors.satuan }}
                            </p>
                        </div>
                        <!-- Deskripsi -->
                        <div>
                            <label
                                class="block text-xs font-medium text-gray-500 mb-1"
                                >Deskripsi
                                <span class="text-gray-300"
                                    >(opsional)</span
                                ></label
                            >
                            <textarea
                                v-model="formEdit.deskripsi"
                                rows="2"
                                class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#D98F8F] resize-none"
                            />
                        </div>
                        <!-- Tombol -->
                        <div class="flex gap-3 pt-1">
                            <button
                                type="button"
                                @click="showEdit = false"
                                class="flex-1 py-2.5 rounded-xl border border-gray-200 text-sm font-semibold text-gray-600 hover:bg-gray-50"
                            >
                                Batal
                            </button>
                            <button
                                type="submit"
                                :disabled="formEdit.processing"
                                class="flex-1 py-2.5 rounded-xl bg-[#A31D22] hover:bg-[#8A171B] text-white text-sm font-semibold disabled:opacity-60"
                            >
                                {{
                                    formEdit.processing
                                        ? "Menyimpan..."
                                        : "Simpan Perubahan"
                                }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </Teleport>

        <!-- ══ MODAL DETAIL ══════════════════════════════════════ -->
        <Teleport to="body">
            <div
                v-if="showDetail && detailProduk"
                class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm p-4"
                @click.self="showDetail = false"
            >
                <div
                    class="bg-white rounded-2xl shadow-xl w-full overflow-hidden"
                    :class="detailProduk.deskripsi ? 'max-w-xl' : 'max-w-sm'"
                >
                    <!-- Layout dengan deskripsi: foto kiri, deskripsi kanan -->
                    <template v-if="detailProduk.deskripsi">
                        <div class="flex flex-col sm:flex-row">
                            <!-- Foto -->
                            <div
                                class="relative sm:w-56 shrink-0 aspect-square bg-gray-50"
                            >
                                <img
                                    v-if="detailProduk.foto"
                                    :src="`/storage/${detailProduk.foto}`"
                                    :alt="detailProduk.nama_produk"
                                    class="w-full h-full object-cover"
                                />
                                <div
                                    v-else
                                    class="w-full h-full flex items-center justify-center"
                                >
                                    <svg
                                        class="w-16 h-16 text-gray-200"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="1.5"
                                            d="M4 16l4.586-4.586a2 2 0 0 1 2.828 0L16 16m-2-2 1.586-1.586a2 2 0 0 1 2.828 0L20 14m-6-6h.01M6 20h12a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2H6a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2z"
                                        />
                                    </svg>
                                </div>
                                <button
                                    @click="showDetail = false"
                                    class="absolute top-3 right-3 bg-white/90 rounded-full p-1.5 shadow"
                                >
                                    <svg
                                        class="w-4 h-4 text-gray-600"
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
                                <span
                                    :class="stokBadge(detailProduk.stok).cls"
                                    class="absolute top-3 left-3 text-xs font-semibold px-2.5 py-1 rounded-full"
                                >
                                    {{ stokBadge(detailProduk.stok).text }}
                                </span>
                            </div>

                            <!-- Deskripsi -->
                            <div class="flex-1 p-5">
                                <h3 class="font-bold text-gray-800 text-lg">
                                    {{ detailProduk.nama_produk }}
                                </h3>
                                <p
                                    class="text-[#A31D22] font-bold text-xl mt-1"
                                >
                                    {{ formatRupiah(detailProduk.harga) }}
                                </p>
                                <div class="mt-3">
                                    <p class="text-xs text-gray-400 mb-1">
                                        Deskripsi
                                    </p>
                                    <p class="text-sm text-gray-600">
                                        {{ detailProduk.deskripsi }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Info harga/satuan/stok di bawah, full width -->
                        <div class="px-5 pb-5">
                            <div
                                class="border-t border-gray-100 pt-4 space-y-2"
                            >
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-400">Stok</span>
                                    <span class="font-semibold text-gray-700">
                                        {{ detailProduk.stok }}
                                        {{ detailProduk.satuan }}
                                    </span>
                                </div>
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-400">Satuan</span>
                                    <span class="font-semibold text-gray-700">{{
                                        detailProduk.satuan
                                    }}</span>
                                </div>
                            </div>

                            <!-- Aksi -->
                            <div class="flex gap-3 mt-5">
                                <button
                                    @click="
                                        showDetail = false;
                                        openEdit(detailProduk);
                                    "
                                    class="flex-1 py-2.5 rounded-xl border border-[#F0C6C6] text-[#A31D22] text-sm font-semibold hover:bg-[#FDF2F2] transition"
                                >
                                    Edit
                                </button>
                                <button
                                    @click="
                                        showDetail = false;
                                        confirmDelete(detailProduk);
                                    "
                                    class="flex-1 py-2.5 rounded-xl border border-red-200 text-red-500 text-sm font-semibold hover:bg-red-50 transition"
                                >
                                    Hapus
                                </button>
                            </div>
                        </div>
                    </template>

                    <!-- Layout tanpa deskripsi: seperti sebelumnya -->
                    <template v-else>
                        <div class="relative aspect-square bg-gray-50">
                            <img
                                v-if="detailProduk.foto"
                                :src="`/storage/${detailProduk.foto}`"
                                :alt="detailProduk.nama_produk"
                                class="w-full h-full object-cover"
                            />
                            <div
                                v-else
                                class="w-full h-full flex items-center justify-center"
                            >
                                <svg
                                    class="w-16 h-16 text-gray-200"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="1.5"
                                        d="M4 16l4.586-4.586a2 2 0 0 1 2.828 0L16 16m-2-2 1.586-1.586a2 2 0 0 1 2.828 0L20 14m-6-6h.01M6 20h12a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2H6a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2z"
                                    />
                                </svg>
                            </div>
                            <button
                                @click="showDetail = false"
                                class="absolute top-3 right-3 bg-white/90 rounded-full p-1.5 shadow"
                            >
                                <svg
                                    class="w-4 h-4 text-gray-600"
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
                            <span
                                :class="stokBadge(detailProduk.stok).cls"
                                class="absolute top-3 left-3 text-xs font-semibold px-2.5 py-1 rounded-full"
                            >
                                {{ stokBadge(detailProduk.stok).text }}
                            </span>
                        </div>
                        <div class="p-5">
                            <h3 class="font-bold text-gray-800 text-lg">
                                {{ detailProduk.nama_produk }}
                            </h3>
                            <p class="text-[#A31D22] font-bold text-xl mt-1">
                                {{ formatRupiah(detailProduk.harga) }}
                            </p>
                            <div class="mt-3 space-y-2">
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-400">Stok</span>
                                    <span class="font-semibold text-gray-700">
                                        {{ detailProduk.stok }}
                                        {{ detailProduk.satuan }}
                                    </span>
                                </div>
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-400">Satuan</span>
                                    <span class="font-semibold text-gray-700">{{
                                        detailProduk.satuan
                                    }}</span>
                                </div>
                            </div>
                            <div class="flex gap-3 mt-5">
                                <button
                                    @click="
                                        showDetail = false;
                                        openEdit(detailProduk);
                                    "
                                    class="flex-1 py-2.5 rounded-xl border border-[#F0C6C6] text-[#A31D22] text-sm font-semibold hover:bg-[#FDF2F2] transition"
                                >
                                    Edit
                                </button>
                                <button
                                    @click="
                                        showDetail = false;
                                        confirmDelete(detailProduk);
                                    "
                                    class="flex-1 py-2.5 rounded-xl border border-red-200 text-red-500 text-sm font-semibold hover:bg-red-50 transition"
                                >
                                    Hapus
                                </button>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </Teleport>

        <!-- ══ MODAL HAPUS ═══════════════════════════════════════ -->
        <Teleport to="body">
            <div
                v-if="showHapus"
                class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm"
            >
                <div class="bg-white rounded-2xl shadow-xl p-6 w-72 mx-4">
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
                        Hapus Produk?
                    </h3>
                    <p class="text-center text-sm text-gray-500 mb-5">
                        <span class="font-semibold text-gray-700">{{
                            produkToDelete?.nama_produk
                        }}</span>
                        akan dihapus permanen.
                    </p>
                    <div class="flex gap-3">
                        <button
                            @click="showHapus = false"
                            class="flex-1 py-2 rounded-xl border border-gray-200 text-sm font-semibold text-gray-600 hover:bg-gray-50"
                        >
                            Batal
                        </button>
                        <button
                            @click="doDelete"
                            class="flex-1 py-2 rounded-xl bg-red-500 text-white text-sm font-semibold hover:bg-red-600"
                        >
                            Hapus
                        </button>
                    </div>
                </div>
            </div>
        </Teleport>
    </AuthenticatedLayout>
</template>
