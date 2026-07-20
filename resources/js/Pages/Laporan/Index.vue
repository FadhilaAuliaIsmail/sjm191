<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, router } from "@inertiajs/vue3";
import { ref } from "vue";

const props = defineProps({
    transaksi: Array,
    pengeluaran: Array,
    sponsor: Array,
    totalPendapatan: Number,
    totalPengeluaran: Number,
    totalSponsor: Number,
    labaRugi: Number,
    filters: Object,
});

const filterAktif = ref(props.filters.filter ?? "bulan");
const dari = ref(props.filters.dari ?? "");
const sampai = ref(props.filters.sampai ?? "");

function terapkanFilter(jenis) {
    filterAktif.value = jenis;
    if (jenis !== "custom") {
        router.get(
            route("laporan.index"),
            { filter: jenis },
            { preserveState: true, preserveScroll: true }
        );
    }
}

function terapkanFilterCustom() {
    if (!dari.value || !sampai.value) return;
    filterAktif.value = "custom";
    router.get(
        route("laporan.index"),
        { filter: "custom", dari: dari.value, sampai: sampai.value },
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
</script>

<template>
    <Head title="Laporan Keuangan" />
    <AuthenticatedLayout>
        <template #header>
            <h2
                class="text-lg sm:text-xl font-semibold leading-tight text-gray-800"
            >
                Laporan Keuangan
            </h2>
        </template>

        <div class="py-6 sm:py-8">
            <div class="mx-auto max-w-7xl space-y-4 px-3 sm:px-6 lg:px-8">
                <!-- Filter Tanggal -->
                <div
                    class="rounded-2xl bg-white p-4 shadow-sm border border-[#EFE7DA]"
                >
                    <div
                        class="flex flex-col gap-3 lg:flex-row lg:items-center"
                    >
                        <div class="flex flex-wrap gap-2">
                            <button
                                v-for="opsi in [
                                    { key: 'hari', label: 'Hari Ini' },
                                    { key: 'minggu', label: 'Minggu Ini' },
                                    { key: 'bulan', label: 'Bulan Ini' },
                                ]"
                                :key="opsi.key"
                                @click="terapkanFilter(opsi.key)"
                                :class="[
                                    'rounded-lg px-3.5 py-1.5 text-sm font-medium transition',
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
                        </div>
                        <div
                            class="flex flex-wrap items-center gap-2 lg:ml-auto"
                        >
                            <input
                                v-model="dari"
                                type="date"
                                class="min-w-0 flex-1 rounded-lg border-[#EFE7DA] text-sm shadow-sm focus:border-[#a31d22] focus:ring-[#a31d22] sm:flex-none"
                            />
                            <span class="text-sm text-gray-400">s/d</span>
                            <input
                                v-model="sampai"
                                type="date"
                                class="min-w-0 flex-1 rounded-lg border-[#EFE7DA] text-sm shadow-sm focus:border-[#a31d22] focus:ring-[#a31d22] sm:flex-none"
                            />
                            <button
                                @click="terapkanFilterCustom"
                                class="w-full rounded-lg px-3.5 py-1.5 text-sm font-medium text-white hover:opacity-90 transition sm:w-auto"
                                style="background-color: #7c1216"
                            >
                                Terapkan
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Ringkasan Laba Rugi -->
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                    <div
                        class="rounded-2xl bg-white p-5 shadow-sm border border-[#EFE7DA]"
                    >
                        <p class="text-sm font-medium text-gray-500">
                            Total Pendapatan
                        </p>
                        <p
                            class="mt-2 text-xl sm:text-2xl font-bold text-green-600 break-words"
                        >
                            {{ formatRupiah(totalPendapatan) }}
                        </p>
                    </div>
                    <div
                        class="rounded-2xl bg-white p-5 shadow-sm border border-[#EFE7DA]"
                    >
                        <p class="text-sm font-medium text-gray-500">
                            Total Pengeluaran
                        </p>
                        <p
                            class="mt-2 text-xl sm:text-2xl font-bold break-words"
                            style="color: #a31d22"
                        >
                            {{ formatRupiah(totalPengeluaran) }}
                        </p>
                    </div>
                    <div
                        class="rounded-2xl bg-white p-5 shadow-sm border border-[#EFE7DA]"
                    >
                        <p class="text-sm font-medium text-gray-500">
                            Laba / Rugi
                        </p>
                        <p
                            class="mt-2 text-xl sm:text-2xl font-bold break-words"
                            :style="
                                labaRugi >= 0
                                    ? 'color:#16a34a'
                                    : 'color:#a31d22'
                            "
                        >
                            {{ formatRupiah(labaRugi) }}
                        </p>
                    </div>
                </div>

                <!-- Info Sponsor (di luar perhitungan laba rugi) -->
                <div
                    v-if="sponsor.length > 0"
                    class="rounded-2xl p-5 shadow-sm border"
                    style="background-color: #faf6ee; border-color: #efe7da"
                >
                    <p class="text-sm font-medium text-gray-500">
                        Total Nilai Barang dari Sponsor
                    </p>
                    <p
                        class="mt-2 text-lg sm:text-xl font-bold break-words"
                        style="color: #a31d22"
                    >
                        {{ formatRupiah(totalSponsor) }}
                    </p>
                    <p class="mt-1 text-xs text-gray-400">
                        Barang sponsor tidak memengaruhi perhitungan laba/rugi
                        karena bukan pengeluaran kas.
                    </p>
                </div>

                <!-- Export -->
                <div class="flex flex-col gap-2 sm:flex-row sm:justify-end">
                    <a
                        :href="
                            route('laporan.export-excel', {
                                filter: filterAktif,
                                dari,
                                sampai,
                            })
                        "
                        class="inline-flex items-center justify-center rounded-xl px-4 py-2 text-sm font-semibold text-white shadow-sm hover:opacity-90 transition"
                        style="background-color: #1f7a4d"
                    >
                        Export Excel
                    </a>
                    <a
                        :href="
                            route('laporan.export-pdf', {
                                filter: filterAktif,
                                dari,
                                sampai,
                            })
                        "
                        class="inline-flex items-center justify-center rounded-xl px-4 py-2 text-sm font-semibold text-white shadow-sm hover:opacity-90 transition"
                        style="background-color: #a31d22"
                    >
                        Export PDF
                    </a>
                </div>

                <!-- ══ Rincian Pendapatan ══════════════════════════════ -->
                <div
                    class="overflow-hidden rounded-2xl bg-white shadow-sm border border-[#EFE7DA]"
                >
                    <div class="border-b border-[#EFE7DA] px-5 py-3">
                        <h3 class="text-sm font-semibold text-gray-700">
                            Rincian Pendapatan (Transaksi Penjualan)
                        </h3>
                    </div>

                    <!-- Kartu: mobile & tablet kecil -->
                    <div class="divide-y divide-[#F5EDE0] md:hidden">
                        <div
                            v-for="t in transaksi"
                            :key="t.id"
                            class="px-5 py-3"
                        >
                            <div
                                class="flex items-center justify-between gap-3"
                            >
                                <p class="text-xs text-gray-500">
                                    {{ formatTanggal(t.tanggal_transaksi) }}
                                </p>
                                <p class="font-medium text-gray-800">
                                    {{ formatRupiah(t.total_harga) }}
                                </p>
                            </div>
                            <div
                                class="mt-1 flex justify-between text-xs text-gray-500"
                            >
                                <span>Bayar: {{ formatRupiah(t.bayar) }}</span>
                                <span
                                    >Kembalian:
                                    {{ formatRupiah(t.kembalian) }}</span
                                >
                            </div>
                        </div>
                        <div
                            v-if="transaksi.length === 0"
                            class="px-5 py-10 text-center text-sm text-gray-400"
                        >
                            Tidak ada transaksi pada periode ini.
                        </div>
                    </div>

                    <!-- Tabel: desktop -->
                    <div class="hidden overflow-x-auto md:block">
                        <table class="w-full text-sm">
                            <thead
                                class="bg-[#FBF8F2] text-left text-xs font-semibold uppercase text-gray-500"
                            >
                                <tr>
                                    <th class="px-5 py-3">Tanggal</th>
                                    <th class="px-5 py-3">Total Harga</th>
                                    <th class="px-5 py-3">Bayar</th>
                                    <th class="px-5 py-3">Kembalian</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-[#F5EDE0]">
                                <tr
                                    v-for="t in transaksi"
                                    :key="t.id"
                                    class="hover:bg-[#FBF8F2]"
                                >
                                    <td
                                        class="px-5 py-3 text-gray-600 whitespace-nowrap"
                                    >
                                        {{ formatTanggal(t.tanggal_transaksi) }}
                                    </td>
                                    <td
                                        class="px-5 py-3 font-medium text-gray-800 whitespace-nowrap"
                                    >
                                        {{ formatRupiah(t.total_harga) }}
                                    </td>
                                    <td
                                        class="px-5 py-3 text-gray-600 whitespace-nowrap"
                                    >
                                        {{ formatRupiah(t.bayar) }}
                                    </td>
                                    <td
                                        class="px-5 py-3 text-gray-600 whitespace-nowrap"
                                    >
                                        {{ formatRupiah(t.kembalian) }}
                                    </td>
                                </tr>
                                <tr v-if="transaksi.length === 0">
                                    <td
                                        colspan="4"
                                        class="px-5 py-10 text-center text-sm text-gray-400"
                                    >
                                        Tidak ada transaksi pada periode ini.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- ══ Rincian Pengeluaran ═════════════════════════════ -->
                <div
                    class="overflow-hidden rounded-2xl bg-white shadow-sm border border-[#EFE7DA]"
                >
                    <div class="border-b border-[#EFE7DA] px-5 py-3">
                        <h3 class="text-sm font-semibold text-gray-700">
                            Rincian Pengeluaran (Belanja Bahan dari Pasar)
                        </h3>
                    </div>

                    <!-- Kartu: mobile & tablet kecil -->
                    <div class="divide-y divide-[#F5EDE0] md:hidden">
                        <div
                            v-for="p in pengeluaran"
                            :key="p.id"
                            class="px-5 py-3"
                        >
                            <div
                                class="flex items-center justify-between gap-3"
                            >
                                <p class="font-medium text-gray-800">
                                    {{ p.nama_barang }}
                                </p>
                                <p class="font-medium" style="color: #a31d22">
                                    {{
                                        formatRupiah(
                                            (p.harga_beli ?? 0) *
                                                p.jumlah_dibeli
                                        )
                                    }}
                                </p>
                            </div>
                            <div
                                class="mt-1 flex justify-between text-xs text-gray-500"
                            >
                                <span>{{
                                    formatTanggal(p.tanggal_masuk)
                                }}</span>
                                <span
                                    >{{ p.jumlah_dibeli }}
                                    {{ p.satuan }} &times;
                                    {{ formatRupiah(p.harga_beli) }}</span
                                >
                            </div>
                        </div>
                        <div
                            v-if="pengeluaran.length === 0"
                            class="px-5 py-10 text-center text-sm text-gray-400"
                        >
                            Tidak ada pengeluaran pada periode ini.
                        </div>
                    </div>

                    <!-- Tabel: desktop -->
                    <div class="hidden overflow-x-auto md:block">
                        <table class="w-full text-sm">
                            <thead
                                class="bg-[#FBF8F2] text-left text-xs font-semibold uppercase text-gray-500"
                            >
                                <tr>
                                    <th class="px-5 py-3">Tanggal</th>
                                    <th class="px-5 py-3">Nama Barang</th>
                                    <th class="px-5 py-3">Jumlah</th>
                                    <th class="px-5 py-3">Harga Beli</th>
                                    <th class="px-5 py-3">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-[#F5EDE0]">
                                <tr
                                    v-for="p in pengeluaran"
                                    :key="p.id"
                                    class="hover:bg-[#FBF8F2]"
                                >
                                    <td
                                        class="px-5 py-3 text-gray-600 whitespace-nowrap"
                                    >
                                        {{ formatTanggal(p.tanggal_masuk) }}
                                    </td>
                                    <td
                                        class="px-5 py-3 font-medium text-gray-800"
                                    >
                                        {{ p.nama_barang }}
                                    </td>
                                    <td
                                        class="px-5 py-3 text-gray-600 whitespace-nowrap"
                                    >
                                        {{ p.jumlah_dibeli }} {{ p.satuan }}
                                    </td>
                                    <td
                                        class="px-5 py-3 text-gray-600 whitespace-nowrap"
                                    >
                                        {{ formatRupiah(p.harga_beli) }}
                                    </td>
                                    <td
                                        class="px-5 py-3 font-medium whitespace-nowrap"
                                        style="color: #a31d22"
                                    >
                                        {{
                                            formatRupiah(
                                                (p.harga_beli ?? 0) *
                                                    p.jumlah_dibeli
                                            )
                                        }}
                                    </td>
                                </tr>
                                <tr v-if="pengeluaran.length === 0">
                                    <td
                                        colspan="5"
                                        class="px-5 py-10 text-center text-sm text-gray-400"
                                    >
                                        Tidak ada pengeluaran pada periode ini.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- ══ Rincian Barang dari Sponsor ═════════════════════ -->
                <div
                    class="overflow-hidden rounded-2xl bg-white shadow-sm border border-[#EFE7DA]"
                >
                    <div class="border-b border-[#EFE7DA] px-5 py-3">
                        <h3 class="text-sm font-semibold text-gray-700">
                            Rincian Barang dari Sponsor
                        </h3>
                    </div>

                    <!-- Kartu: mobile & tablet kecil -->
                    <div class="divide-y divide-[#F5EDE0] md:hidden">
                        <div v-for="s in sponsor" :key="s.id" class="px-5 py-3">
                            <div
                                class="flex items-center justify-between gap-3"
                            >
                                <p class="font-medium text-gray-800">
                                    {{ s.nama_barang }}
                                </p>
                                <p class="font-medium" style="color: #a31d22">
                                    {{
                                        s.harga_beli
                                            ? formatRupiah(
                                                  s.harga_beli * s.jumlah_dibeli
                                              )
                                            : "-"
                                    }}
                                </p>
                            </div>
                            <div
                                class="mt-1 flex justify-between text-xs text-gray-500"
                            >
                                <span>{{
                                    formatTanggal(s.tanggal_masuk)
                                }}</span>
                                <span
                                    >{{ s.jumlah_dibeli }} {{ s.satuan }}</span
                                >
                            </div>
                            <p
                                v-if="s.keterangan"
                                class="mt-1 text-xs text-gray-400"
                            >
                                {{ s.keterangan }}
                            </p>
                        </div>
                        <div
                            v-if="sponsor.length === 0"
                            class="px-5 py-10 text-center text-sm text-gray-400"
                        >
                            Tidak ada barang sponsor pada periode ini.
                        </div>
                    </div>

                    <!-- Tabel: desktop -->
                    <div class="hidden overflow-x-auto md:block">
                        <table class="w-full text-sm">
                            <thead
                                class="bg-[#FBF8F2] text-left text-xs font-semibold uppercase text-gray-500"
                            >
                                <tr>
                                    <th class="px-5 py-3">Tanggal</th>
                                    <th class="px-5 py-3">Nama Barang</th>
                                    <th class="px-5 py-3">Jumlah</th>
                                    <th class="px-5 py-3">Estimasi Harga</th>
                                    <th class="px-5 py-3">Subtotal</th>
                                    <th class="px-5 py-3">Keterangan</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-[#F5EDE0]">
                                <tr
                                    v-for="s in sponsor"
                                    :key="s.id"
                                    class="hover:bg-[#FBF8F2]"
                                >
                                    <td
                                        class="px-5 py-3 text-gray-600 whitespace-nowrap"
                                    >
                                        {{ formatTanggal(s.tanggal_masuk) }}
                                    </td>
                                    <td
                                        class="px-5 py-3 font-medium text-gray-800"
                                    >
                                        {{ s.nama_barang }}
                                    </td>
                                    <td
                                        class="px-5 py-3 text-gray-600 whitespace-nowrap"
                                    >
                                        {{ s.jumlah_dibeli }} {{ s.satuan }}
                                    </td>
                                    <td
                                        class="px-5 py-3 text-gray-600 whitespace-nowrap"
                                    >
                                        {{
                                            s.harga_beli
                                                ? formatRupiah(s.harga_beli)
                                                : "-"
                                        }}
                                    </td>
                                    <td
                                        class="px-5 py-3 font-medium whitespace-nowrap"
                                        style="color: #a31d22"
                                    >
                                        {{
                                            s.harga_beli
                                                ? formatRupiah(
                                                      s.harga_beli *
                                                          s.jumlah_dibeli
                                                  )
                                                : "-"
                                        }}
                                    </td>
                                    <td class="px-5 py-3 text-gray-500">
                                        {{ s.keterangan || "-" }}
                                    </td>
                                </tr>
                                <tr v-if="sponsor.length === 0">
                                    <td
                                        colspan="6"
                                        class="px-5 py-10 text-center text-sm text-gray-400"
                                    >
                                        Tidak ada barang sponsor pada periode
                                        ini.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
