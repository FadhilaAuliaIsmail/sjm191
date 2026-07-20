<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link } from "@inertiajs/vue3";
import { onMounted, onUnmounted, ref } from "vue";

defineProps({
    pendapatanHariIni: Number,
    transaksiHariIni: Number,
    transaksiTerakhir: Array,
});

function formatRupiah(angka) {
    return new Intl.NumberFormat("id-ID", {
        style: "currency",
        currency: "IDR",
        minimumFractionDigits: 0,
    }).format(angka || 0);
}

function formatJam(tgl) {
    // Laravel mengirim datetime tanpa info timezone (misal "2026-06-30 14:00:00"),
    // browser sering salah mengartikannya sebagai UTC sehingga muncul selisih 7 jam.
    // Parsing manual di sini memperlakukan string itu sebagai waktu lokal (WIB) apa adanya.
    const [, jam, menit] = tgl.match(/(\d{2}):(\d{2}):\d{2}/) || [];
    if (!jam) return "-";
    return `${jam}.${menit}`;
}

// ── Jam & Tanggal Real-time ─────────────────────────────────────
const waktuSekarang = ref(new Date());
let timerId = null;

onMounted(() => {
    timerId = setInterval(() => {
        waktuSekarang.value = new Date();
    }, 1000);
});

onUnmounted(() => {
    if (timerId) clearInterval(timerId);
});

function formatJamSekarang(tgl) {
    return tgl.toLocaleTimeString("id-ID", {
        hour: "2-digit",
        minute: "2-digit",
        second: "2-digit",
        hour12: false,
    });
}

function formatTanggalSekarang(tgl) {
    return tgl.toLocaleDateString("id-ID", {
        weekday: "long",
        day: "numeric",
        month: "long",
        year: "numeric",
    });
}
</script>

<template>
    <Head title="Dashboard" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Dashboard
            </h2>
        </template>

        <div class="space-y-6">
            <!-- Stat Cards -->
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                <div
                    class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-gray-100"
                >
                    <p class="text-sm font-medium text-gray-500">
                        {{ formatTanggalSekarang(waktuSekarang) }}
                    </p>
                    <p
                        class="mt-2 font-mono text-2xl font-bold tabular-nums text-gray-900"
                    >
                        {{ formatJamSekarang(waktuSekarang) }}
                    </p>
                </div>
                <div
                    class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-gray-100"
                >
                    <p class="text-sm font-medium text-gray-500">
                        Pendapatan Hari Ini
                    </p>
                    <p class="mt-2 text-2xl font-bold text-gray-900">
                        {{ formatRupiah(pendapatanHariIni) }}
                    </p>
                </div>
                <div
                    class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-gray-100"
                >
                    <p class="text-sm font-medium text-gray-500">
                        Transaksi Hari Ini
                    </p>
                    <p class="mt-2 text-2xl font-bold text-gray-900">
                        {{ transaksiHariIni }}
                    </p>
                </div>
            </div>

            <!-- Shortcut -->
            <Link
                :href="route('pos.index')"
                class="block rounded-2xl bg-red-600 p-5 text-white shadow-sm hover:bg-red-700 transition"
            >
                <p class="font-semibold">Mulai Transaksi Baru</p>
                <p class="text-sm text-red-100">
                    Buka halaman Kasir untuk melayani pelanggan
                </p>
            </Link>

            <!-- Transaksi Terakhir -->
            <div
                class="rounded-2xl bg-white shadow-sm ring-1 ring-gray-100 overflow-hidden"
            >
                <div class="border-b border-gray-100 px-5 py-3">
                    <h3 class="text-sm font-semibold text-gray-700">
                        Transaksi Terakhir Hari Ini
                    </h3>
                </div>
                <table class="w-full text-sm">
                    <thead
                        class="bg-gray-50 text-left text-xs font-semibold uppercase text-gray-500"
                    >
                        <tr>
                            <th class="px-5 py-3">Jam</th>
                            <th class="px-5 py-3">Total</th>
                            <th class="px-5 py-3">Bayar</th>
                            <th class="px-5 py-3">Kembalian</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <tr
                            v-for="t in transaksiTerakhir"
                            :key="t.id"
                            class="hover:bg-gray-50"
                        >
                            <td class="px-5 py-3 text-gray-600">
                                {{ formatJam(t.tanggal_transaksi) }}
                            </td>
                            <td class="px-5 py-3 font-medium text-gray-800">
                                {{ formatRupiah(t.total_harga) }}
                            </td>
                            <td class="px-5 py-3 text-gray-600">
                                {{ formatRupiah(t.bayar) }}
                            </td>
                            <td class="px-5 py-3 text-gray-600">
                                {{ formatRupiah(t.kembalian) }}
                            </td>
                        </tr>
                        <tr v-if="transaksiTerakhir.length === 0">
                            <td
                                colspan="4"
                                class="px-5 py-10 text-center text-sm text-gray-400"
                            >
                                Belum ada transaksi hari ini.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
