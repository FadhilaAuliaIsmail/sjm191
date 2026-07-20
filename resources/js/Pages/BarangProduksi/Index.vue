<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, router, useForm } from "@inertiajs/vue3";
import { computed, ref } from "vue";

const props = defineProps({
    barangProduksi: Array,
    ringkasan: Array,
    totalBelanjaKeseluruhan: Number,
    filters: Object,
});

const sumberAktif = ref(props.filters.sumber ?? "pasar");
const filterAktif = ref(props.filters.filter ?? "semua");
const dari = ref(props.filters.dari ?? "");
const sampai = ref(props.filters.sampai ?? "");

function gantiSumber(sumber) {
    sumberAktif.value = sumber;
    filterAktif.value = "semua";
    router.get(
        route("barang-produksi.index"),
        { sumber, filter: "semua" },
        { preserveState: true, preserveScroll: true }
    );
}

function terapkanFilter(jenis) {
    filterAktif.value = jenis;
    if (jenis !== "custom") {
        router.get(
            route("barang-produksi.index"),
            { sumber: sumberAktif.value, filter: jenis },
            { preserveState: true, preserveScroll: true }
        );
    }
}

function terapkanFilterCustom() {
    if (!dari.value || !sampai.value) return;
    filterAktif.value = "custom";
    router.get(
        route("barang-produksi.index"),
        {
            sumber: sumberAktif.value,
            filter: "custom",
            dari: dari.value,
            sampai: sampai.value,
        },
        { preserveState: true, preserveScroll: true }
    );
}

function formatTanggal(tgl) {
    return new Date(tgl).toLocaleDateString("id-ID", {
        day: "numeric",
        month: "long",
        year: "numeric",
    });
}

function formatRupiah(angka) {
    return new Intl.NumberFormat("id-ID", {
        style: "currency",
        currency: "IDR",
        minimumFractionDigits: 0,
    }).format(angka || 0);
}

const search = ref("");
const dataFiltered = computed(() => {
    if (!search.value) return props.barangProduksi;
    const q = search.value.toLowerCase();
    return props.barangProduksi.filter((d) =>
        d.nama_barang.toLowerCase().includes(q)
    );
});

// ── Modal Tambah/Edit ─────────────────────────────────────────
const showModal = ref(false);
const isEditing = ref(false);
const editingId = ref(null);

const form = useForm({
    nama_barang: "",
    sumber: "pasar",
    tanggal_masuk: new Date().toISOString().slice(0, 10),
    jumlah_dibeli: "",
    berat_per_satuan: "",
    satuan: "kg",
    harga_beli: "",
    keterangan: "",
});

const totalBerat = computed(() => {
    const jd = parseFloat(form.jumlah_dibeli) || 0;
    const bps = parseFloat(form.berat_per_satuan) || 0;
    return jd * bps;
});

const subtotal = computed(() => {
    const jd = parseFloat(form.jumlah_dibeli) || 0;
    const harga = parseFloat(form.harga_beli) || 0;
    return jd * harga;
});

function openCreateModal() {
    isEditing.value = false;
    editingId.value = null;
    form.reset();
    form.sumber = sumberAktif.value;
    form.tanggal_masuk = new Date().toISOString().slice(0, 10);
    form.satuan = "kg";
    form.clearErrors();
    showModal.value = true;
}

function openEditModal(item) {
    isEditing.value = true;
    editingId.value = item.id;
    form.nama_barang = item.nama_barang;
    form.sumber = item.sumber;
    form.tanggal_masuk = item.tanggal_masuk.slice(0, 10);
    form.jumlah_dibeli = item.jumlah_dibeli;
    form.berat_per_satuan = item.berat_per_satuan;
    form.satuan = item.satuan;
    form.harga_beli = item.harga_beli ?? "";
    form.keterangan = item.keterangan ?? "";
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
        form.put(route("barang-produksi.update", editingId.value), {
            onSuccess: () => closeModal(),
        });
    } else {
        form.post(route("barang-produksi.store"), {
            onSuccess: () => closeModal(),
        });
    }
}

// ── Hapus ─────────────────────────────────────────────────────
const showDeleteConfirm = ref(false);
const deleteTarget = ref(null);

function confirmDelete(item) {
    deleteTarget.value = item;
    showDeleteConfirm.value = true;
}

function executeDelete() {
    form.delete(route("barang-produksi.destroy", deleteTarget.value.id), {
        onSuccess: () => {
            showDeleteConfirm.value = false;
            deleteTarget.value = null;
        },
    });
}
</script>

<template>
    <Head title="Barang Produksi" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Barang Produksi
            </h2>
        </template>

        <div class="p-8">
            <div class="mx-auto max-w-7xl space-y-4">
                <p class="text-sm text-gray-500">
                    Catatan barang/bahan yang masuk. Data ini hanya riwayat,
                    tidak memengaruhi stok produk.
                </p>

                <!-- Tab Sumber -->
                <div class="flex gap-2 border-b border-[#EFE7DA]">
                    <button
                        v-for="tab in [
                            { key: 'pasar', label: 'Beli dari Pasar' },
                            { key: 'sponsor', label: 'Sponsor' },
                        ]"
                        :key="tab.key"
                        @click="gantiSumber(tab.key)"
                        :class="[
                            'border-b-2 px-4 py-2.5 text-sm font-semibold transition',
                        ]"
                        :style="
                            sumberAktif === tab.key
                                ? 'border-color:#a31d22; color:#a31d22'
                                : 'border-color:transparent; color:#a8a29e'
                        "
                    >
                        {{ tab.label }}
                    </button>
                </div>

                <!-- Filter Tanggal -->
                <div
                    class="rounded-2xl bg-white p-4 shadow-sm border border-[#EFE7DA]"
                >
                    <div class="flex flex-wrap items-center gap-2">
                        <button
                            v-for="opsi in [
                                { key: 'semua', label: 'Semua' },
                                { key: 'hari', label: 'Hari Ini' },
                                { key: 'minggu', label: 'Minggu Ini' },
                                { key: 'bulan', label: 'Bulan Ini' },
                            ]"
                            :key="opsi.key"
                            @click="terapkanFilter(opsi.key)"
                            :class="[
                                'rounded-lg px-3 py-1.5 text-sm font-medium transition',
                                filterAktif === opsi.key
                                    ? 'text-white'
                                    : 'bg-[#FBF8F2] text-gray-600 hover:bg-[#F5EDE0]',
                            ]"
                            :style="
                                filterAktif === opsi.key
                                    ? 'background-color:#a31d22'
                                    : ''
                            "
                        >
                            {{ opsi.label }}
                        </button>
                        <div class="ml-auto flex flex-wrap items-center gap-2">
                            <input
                                v-model="dari"
                                type="date"
                                class="rounded-lg border-[#EFE7DA] text-sm shadow-sm focus:border-[#a31d22] focus:ring-[#a31d22]"
                            />
                            <span class="text-sm text-gray-400">s/d</span>
                            <input
                                v-model="sampai"
                                type="date"
                                class="rounded-lg border-[#EFE7DA] text-sm shadow-sm focus:border-[#a31d22] focus:ring-[#a31d22]"
                            />
                            <button
                                @click="terapkanFilterCustom"
                                class="rounded-lg px-3 py-1.5 text-sm font-medium text-white hover:opacity-90"
                                style="background-color: #7c1216"
                            >
                                Terapkan
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Ringkasan -->
                <div
                    v-if="ringkasan.length > 0"
                    class="rounded-2xl bg-white p-5 shadow-sm border border-[#EFE7DA]"
                >
                    <div class="mb-3 flex items-center justify-between">
                        <h3 class="text-sm font-semibold text-gray-700">
                            Ringkasan (sesuai filter)
                        </h3>
                        <p class="text-sm font-bold text-gray-800">
                            Total
                            {{
                                sumberAktif === "pasar"
                                    ? "belanja"
                                    : "nilai barang"
                            }}:
                            {{ formatRupiah(totalBelanjaKeseluruhan) }}
                        </p>
                    </div>
                    <div class="flex flex-wrap gap-3">
                        <div
                            v-for="r in ringkasan"
                            :key="r.nama_barang + r.satuan"
                            class="rounded-xl px-4 py-2.5"
                            style="background-color: #faf6ee"
                        >
                            <p class="text-xs" style="color: #c9836a">
                                {{ r.nama_barang }}
                            </p>
                            <p class="text-lg font-bold" style="color: #a31d22">
                                {{ r.total_jumlah }}
                                <span class="text-xs font-normal">{{
                                    r.satuan
                                }}</span>
                            </p>
                            <p class="text-xs" style="color: #c9836a">
                                {{ formatRupiah(r.total_belanja) }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Toolbar -->
                <div
                    class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between"
                >
                    <input
                        v-model="search"
                        type="text"
                        placeholder="Cari nama barang..."
                        class="w-full rounded-xl border-[#EFE7DA] text-sm shadow-sm focus:border-[#a31d22] focus:ring-[#a31d22] sm:w-72"
                    />
                    <button
                        @click="openCreateModal"
                        class="inline-flex items-center justify-center rounded-xl px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:opacity-90"
                        style="background-color: #a31d22"
                    >
                        + Tambah Pencatatan Barang
                    </button>
                </div>

                <!-- Tabel -->
                <div
                    class="overflow-hidden rounded-2xl bg-white shadow-sm border border-[#EFE7DA]"
                >
                    <table class="w-full text-sm">
                        <thead
                            class="bg-[#FBF8F2] text-left text-xs font-semibold uppercase text-gray-500"
                        >
                            <tr>
                                <th class="px-5 py-3">Tanggal</th>
                                <th class="px-5 py-3">Nama Barang</th>
                                <th class="px-5 py-3">Rincian Beli</th>
                                <th class="px-5 py-3">
                                    {{
                                        sumberAktif === "pasar"
                                            ? "Harga Beli"
                                            : "Estimasi Harga"
                                    }}
                                </th>
                                <th class="px-5 py-3">Subtotal</th>
                                <th class="px-5 py-3">Keterangan</th>
                                <th class="px-5 py-3 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#F5EDE0]">
                            <tr
                                v-for="item in dataFiltered"
                                :key="item.id"
                                class="hover:bg-[#FBF8F2]"
                            >
                                <td class="px-5 py-3 text-gray-600">
                                    {{ formatTanggal(item.tanggal_masuk) }}
                                </td>
                                <td class="px-5 py-3 font-medium text-gray-800">
                                    {{ item.nama_barang }}
                                </td>
                                <td class="px-5 py-3 text-gray-500">
                                    {{ item.jumlah_dibeli }} kuantitas, berat
                                    {{ parseFloat(item.berat_per_satuan) }}
                                    {{ item.satuan }}
                                </td>
                                <td class="px-5 py-3 text-gray-600">
                                    {{
                                        item.harga_beli
                                            ? formatRupiah(item.harga_beli)
                                            : "-"
                                    }}
                                </td>
                                <td
                                    class="px-5 py-3 font-semibold"
                                    style="color: #a31d22"
                                >
                                    {{
                                        item.harga_beli
                                            ? formatRupiah(
                                                  item.jumlah_dibeli *
                                                      item.harga_beli
                                              )
                                            : "-"
                                    }}
                                </td>
                                <td class="px-5 py-3 text-gray-500">
                                    {{ item.keterangan || "-" }}
                                </td>
                                <td class="px-5 py-3 text-right">
                                    <button
                                        @click="openEditModal(item)"
                                        class="mr-3 text-sm font-medium hover:underline"
                                        style="color: #a31d22"
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
                                    :colspan="7"
                                    class="px-5 py-10 text-center text-sm text-gray-400"
                                >
                                    Belum ada data.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- ══ MODAL TAMBAH / EDIT ════════════════════════════════ -->
        <div
            v-if="showModal"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 px-4"
            @click.self="closeModal"
        >
            <div
                class="w-full max-w-md rounded-2xl bg-white p-6 shadow-xl max-h-[90vh] overflow-y-auto"
            >
                <h3 class="mb-4 text-lg font-semibold text-gray-800">
                    {{
                        isEditing
                            ? "Edit Pencatatan Barang"
                            : "Tambah Pencatatan Barang"
                    }}
                </h3>
                <form @submit.prevent="submitForm" class="space-y-4">
                    <!-- Sumber -->
                    <div>
                        <label
                            class="mb-1 block text-sm font-medium text-gray-700"
                            >Sumber</label
                        >
                        <div class="flex gap-2">
                            <button
                                type="button"
                                @click="form.sumber = 'pasar'"
                                class="flex-1 rounded-xl border px-3 py-2 text-sm font-medium"
                                :style="
                                    form.sumber === 'pasar'
                                        ? 'border-color:#a31d22; background-color:#faf6ee; color:#a31d22'
                                        : 'border-color:#EFE7DA; color:#78716c'
                                "
                            >
                                Beli dari Pasar
                            </button>
                            <button
                                type="button"
                                @click="form.sumber = 'sponsor'"
                                class="flex-1 rounded-xl border px-3 py-2 text-sm font-medium"
                                :style="
                                    form.sumber === 'sponsor'
                                        ? 'border-color:#a31d22; background-color:#faf6ee; color:#a31d22'
                                        : 'border-color:#EFE7DA; color:#78716c'
                                "
                            >
                                Sponsor
                            </button>
                        </div>
                    </div>

                    <!-- Nama Barang -->
                    <div>
                        <label
                            class="mb-1 block text-sm font-medium text-gray-700"
                            >Nama Barang</label
                        >
                        <input
                            v-model="form.nama_barang"
                            type="text"
                            placeholder="Isi nama barang..."
                            class="w-full rounded-xl border-[#EFE7DA] text-sm shadow-sm focus:border-[#a31d22] focus:ring-[#a31d22]"
                        />
                        <p
                            v-if="form.errors.nama_barang"
                            class="mt-1 text-xs text-red-500"
                        >
                            {{ form.errors.nama_barang }}
                        </p>
                    </div>

                    <!-- Jumlah -->
                    <div>
                        <label
                            class="mb-1 block text-sm font-medium text-gray-700"
                            >Jumlah</label
                        >
                        <input
                            v-model="form.jumlah_dibeli"
                            type="number"
                            min="1"
                            placeholder="Isi jumlah..."
                            class="w-full rounded-xl border-[#EFE7DA] text-sm shadow-sm focus:border-[#a31d22] focus:ring-[#a31d22]"
                        />
                        <p
                            v-if="form.errors.jumlah_dibeli"
                            class="mt-1 text-xs text-red-500"
                        >
                            {{ form.errors.jumlah_dibeli }}
                        </p>
                    </div>

                    <!-- Berat & Satuan -->
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label
                                class="mb-1 block text-sm font-medium text-gray-700"
                                >Berat per buah</label
                            >
                            <input
                                v-model="form.berat_per_satuan"
                                type="number"
                                min="0.01"
                                step="0.01"
                                placeholder="Isi berat..."
                                class="w-full rounded-xl border-[#EFE7DA] text-sm shadow-sm focus:border-[#a31d22] focus:ring-[#a31d22]"
                            />
                            <p
                                v-if="form.errors.berat_per_satuan"
                                class="mt-1 text-xs text-red-500"
                            >
                                {{ form.errors.berat_per_satuan }}
                            </p>
                        </div>
                        <div>
                            <label
                                class="mb-1 block text-sm font-medium text-gray-700"
                                >Satuan</label
                            >
                            <select
                                v-model="form.satuan"
                                class="w-full rounded-xl border-[#EFE7DA] text-sm shadow-sm focus:border-[#a31d22] focus:ring-[#a31d22]"
                            >
                                <option value="kg">Kilogram (kg)</option>
                                <option value="gram">Gram</option>
                                <option value="liter">Liter</option>
                                <option value="ml">Mililiter (ml)</option>
                                <option value="pcs">Pcs</option>
                                <option value="ikat">Ikat</option>
                                <option value="box">Box</option>
                                <option value="lainnya">Lainnya</option>
                            </select>
                            <p
                                v-if="form.errors.satuan"
                                class="mt-1 text-xs text-red-500"
                            >
                                {{ form.errors.satuan }}
                            </p>
                        </div>
                    </div>

                    <!-- Ringkasan -->
                    <div
                        v-if="form.jumlah_dibeli && form.berat_per_satuan"
                        class="rounded-xl px-4 py-3 text-sm space-y-1.5"
                        style="background-color: #fbf8f2"
                    >
                        <p class="text-gray-600">
                            {{ form.jumlah_dibeli }}
                            <span class="font-semibold text-gray-800">{{
                                form.nama_barang || "barang"
                            }}</span
                            >, masing-masing beratnya
                            <span class="font-semibold text-gray-800"
                                >{{ parseFloat(form.berat_per_satuan) }}
                                {{ form.satuan }}</span
                            >
                        </p>
                        <div
                            v-if="form.harga_beli"
                            class="flex justify-between border-t pt-1.5"
                            style="border-color: #efe7da"
                        >
                            <span class="font-semibold text-gray-700"
                                >Subtotal</span
                            >
                            <span class="font-bold" style="color: #a31d22">{{
                                formatRupiah(subtotal)
                            }}</span>
                        </div>
                    </div>

                    <!-- Harga Beli / Estimasi Harga -->
                    <div>
                        <label
                            class="mb-1 block text-sm font-medium text-gray-700"
                        >
                            {{
                                form.sumber === "pasar"
                                    ? "Harga Beli (per buah)"
                                    : "Estimasi Harga (opsional)"
                            }}
                        </label>
                        <input
                            v-model="form.harga_beli"
                            type="number"
                            min="0"
                            placeholder="Misal: 15000"
                            class="w-full rounded-xl border-[#EFE7DA] text-sm shadow-sm focus:border-[#a31d22] focus:ring-[#a31d22]"
                        />
                        <p
                            v-if="form.errors.harga_beli"
                            class="mt-1 text-xs text-red-500"
                        >
                            {{ form.errors.harga_beli }}
                        </p>
                    </div>

                    <!-- Tanggal -->
                    <div>
                        <label
                            class="mb-1 block text-sm font-medium text-gray-700"
                            >Tanggal Masuk</label
                        >
                        <input
                            v-model="form.tanggal_masuk"
                            type="date"
                            class="w-full rounded-xl border-[#EFE7DA] text-sm shadow-sm focus:border-[#a31d22] focus:ring-[#a31d22]"
                        />
                        <p
                            v-if="form.errors.tanggal_masuk"
                            class="mt-1 text-xs text-red-500"
                        >
                            {{ form.errors.tanggal_masuk }}
                        </p>
                    </div>

                    <!-- Keterangan -->
                    <div>
                        <label
                            class="mb-1 block text-sm font-medium text-gray-700"
                            >Keterangan (opsional)</label
                        >
                        <textarea
                            v-model="form.keterangan"
                            rows="2"
                            :placeholder="
                                form.sumber === 'pasar'
                                    ? 'Misal: beli dari Pasar Tanah Abang'
                                    : 'Misal: sponsor dari Foyu'
                            "
                            class="w-full rounded-xl border-[#EFE7DA] text-sm shadow-sm focus:border-[#a31d22] focus:ring-[#a31d22]"
                        />
                    </div>

                    <!-- Tombol -->
                    <div class="flex justify-end gap-2 pt-2">
                        <button
                            type="button"
                            @click="closeModal"
                            class="rounded-xl px-4 py-2 text-sm font-medium text-gray-600 hover:bg-gray-100"
                        >
                            Batal
                        </button>
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="rounded-xl px-4 py-2 text-sm font-semibold text-white hover:opacity-90 disabled:opacity-50"
                            style="background-color: #a31d22"
                        >
                            {{ isEditing ? "Simpan" : "Tambah" }}
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- ══ MODAL HAPUS ════════════════════════════════════════ -->
        <div
            v-if="showDeleteConfirm"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 px-4"
            @click.self="showDeleteConfirm = false"
        >
            <div class="w-full max-w-sm rounded-2xl bg-white p-6 shadow-xl">
                <h3 class="text-lg font-semibold text-gray-800">
                    Hapus Barang Produksi?
                </h3>
                <p class="mt-2 text-sm text-gray-500">
                    Catatan barang "<span class="font-semibold">{{
                        deleteTarget?.nama_barang
                    }}</span
                    >" akan dihapus permanen.
                </p>
                <div class="mt-5 flex justify-end gap-2">
                    <button
                        @click="showDeleteConfirm = false"
                        class="rounded-xl px-4 py-2 text-sm font-medium text-gray-600 hover:bg-gray-100"
                    >
                        Batal
                    </button>
                    <button
                        @click="executeDelete"
                        class="rounded-xl bg-red-500 px-4 py-2 text-sm font-semibold text-white hover:bg-red-600"
                    >
                        Hapus
                    </button>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
