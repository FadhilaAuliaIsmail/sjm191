<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link } from "@inertiajs/vue3";
import Chart from "chart.js/auto";
import {
    AlertTriangle,
    Boxes,
    CalendarDays,
    FileBarChart,
    Inbox,
    Package,
    Receipt,
    Users,
    Wallet,
    Zap,
} from "lucide-vue-next";
import { onMounted, ref } from "vue";

const props = defineProps({
    stats: Object,
    stokMenipis: Array,
    grafikPendapatan: Array,
    produkTerlaris: Array,
});

const chartPendapatanRef = ref(null);
const chartTerlarisRef = ref(null);

function formatRupiah(angka) {
    return new Intl.NumberFormat("id-ID", {
        style: "currency",
        currency: "IDR",
        minimumFractionDigits: 0,
    }).format(angka || 0);
}

onMounted(() => {
    // Grafik pendapatan 7 hari (line chart)
    new Chart(chartPendapatanRef.value, {
        type: "line",
        data: {
            labels: props.grafikPendapatan.map((d) => d.tanggal),
            datasets: [
                {
                    label: "Pendapatan",
                    data: props.grafikPendapatan.map((d) => d.total),
                    borderColor: "#A31D22",
                    backgroundColor: "rgba(163, 29, 34, 0.08)",
                    tension: 0.35,
                    fill: true,
                    pointBackgroundColor: "#A31D22",
                    pointBorderColor: "#FAF6EE",
                    pointBorderWidth: 1.5,
                    pointRadius: 4,
                },
            ],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: "#650E11",
                    padding: 10,
                    titleFont: { size: 12 },
                    bodyFont: { size: 12.5, weight: "600" },
                    callbacks: {
                        label: (ctx) => formatRupiah(ctx.parsed.y),
                    },
                },
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        color: "#A8998C",
                        callback: (val) =>
                            formatRupiah(val).replace("Rp", "").trim(),
                    },
                    grid: { color: "#F1EBDF" },
                },
                x: {
                    ticks: { color: "#A8998C" },
                    grid: { display: false },
                },
            },
        },
    });

    // Grafik produk terlaris (horizontal bar)
    if (props.produkTerlaris.length > 0) {
        new Chart(chartTerlarisRef.value, {
            type: "bar",
            data: {
                labels: props.produkTerlaris.map((p) => p.nama_produk),
                datasets: [
                    {
                        label: "Terjual",
                        data: props.produkTerlaris.map((p) => p.total_terjual),
                        backgroundColor: "#E3A93B",
                        borderRadius: 6,
                    },
                ],
            },
            options: {
                indexAxis: "y",
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    x: {
                        beginAtZero: true,
                        ticks: { color: "#A8998C" },
                        grid: { color: "#F1EBDF" },
                    },
                    y: {
                        ticks: { color: "#5C4A3F" },
                        grid: { display: false },
                    },
                },
            },
        });
    }
});

const statCards = [
    {
        key: "pendapatanHariIni",
        label: "Pendapatan Hari Ini",
        icon: Wallet,
        format: true,
    },
    {
        key: "pendapatanBulanIni",
        label: "Pendapatan Bulan Ini",
        icon: CalendarDays,
        format: true,
    },
    {
        key: "transaksiHariIni",
        label: "Transaksi Hari Ini",
        icon: Receipt,
        format: false,
    },
];

const quickLinks = [
    { label: "Produk", route: "produk.index", icon: Package },
    { label: "Barang Produksi", route: "barang-produksi.index", icon: Boxes },
    { label: "Data Mitra", route: "data-mitra.index", icon: Users },
    { label: "Laporan", route: "laporan.index", icon: FileBarChart },
];
</script>

<template>
    <Head title="Dashboard" />
    <AuthenticatedLayout>
        <template #header>
            <h2
                class="text-xl font-semibold leading-tight"
                style="font-family: 'Fraunces', serif; color: #5c4a3f"
            >
                Dashboard Pemilik
            </h2>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">
                <!-- Stat Cards -->
                <div
                    class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4"
                >
                    <div
                        v-for="card in statCards"
                        :key="card.key"
                        class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-gray-100"
                    >
                        <div class="flex items-center justify-between">
                            <p class="text-sm font-medium text-gray-500">
                                {{ card.label }}
                            </p>
                            <div
                                class="flex h-8 w-8 items-center justify-center rounded-lg"
                                style="background: #fdf2f2"
                            >
                                <component
                                    :is="card.icon"
                                    class="h-4 w-4"
                                    style="color: #a31d22"
                                />
                            </div>
                        </div>
                        <p class="mt-3 text-2xl font-bold text-gray-900">
                            {{
                                card.format
                                    ? formatRupiah(stats[card.key])
                                    : stats[card.key]
                            }}
                        </p>
                    </div>

                    <div
                        class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-gray-100"
                    >
                        <div class="flex items-center justify-between">
                            <p class="text-sm font-medium text-gray-500">
                                Produk &amp; Mitra
                            </p>
                            <div
                                class="flex h-8 w-8 items-center justify-center rounded-lg"
                                style="background: #fdf2f2"
                            >
                                <Boxes class="h-4 w-4" style="color: #a31d22" />
                            </div>
                        </div>
                        <p class="mt-3 text-2xl font-bold text-gray-900">
                            {{ stats.jumlahProduk }}
                            <span class="text-sm font-normal text-gray-400"
                                >produk</span
                            >
                        </p>
                        <p class="text-sm text-gray-500">
                            {{ stats.jumlahMitra }} mitra/cabang
                        </p>
                    </div>
                </div>

                <!-- Grafik -->
                <div class="grid grid-cols-1 gap-4 lg:grid-cols-3">
                    <div
                        class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-gray-100 lg:col-span-2"
                    >
                        <h3
                            class="mb-4 font-semibold"
                            style="
                                font-family: 'Fraunces', serif;
                                color: #5c4a3f;
                            "
                        >
                            Pendapatan 7 Hari Terakhir
                        </h3>
                        <div class="h-72">
                            <canvas ref="chartPendapatanRef"></canvas>
                        </div>
                    </div>

                    <div
                        class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-gray-100"
                    >
                        <h3
                            class="mb-4 font-semibold"
                            style="
                                font-family: 'Fraunces', serif;
                                color: #5c4a3f;
                            "
                        >
                            Produk Terlaris
                        </h3>
                        <div class="h-72">
                            <canvas
                                v-if="produkTerlaris.length > 0"
                                ref="chartTerlarisRef"
                            ></canvas>
                            <div
                                v-else
                                class="flex h-full flex-col items-center justify-center gap-2 text-center"
                            >
                                <div
                                    class="flex h-10 w-10 items-center justify-center rounded-full"
                                    style="background: #faf6ee"
                                >
                                    <Inbox
                                        class="h-5 w-5"
                                        style="color: #cbb89a"
                                    />
                                </div>
                                <p class="text-sm text-gray-400">
                                    Belum ada data transaksi.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Stok Menipis & Quick Links -->
                <div class="grid grid-cols-1 gap-4 lg:grid-cols-3">
                    <div
                        class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-gray-100 lg:col-span-2"
                    >
                        <h3
                            class="mb-4 flex items-center gap-2 font-semibold"
                            style="
                                font-family: 'Fraunces', serif;
                                color: #5c4a3f;
                            "
                        >
                            <AlertTriangle
                                class="h-4 w-4"
                                style="color: #e3a93b"
                            />
                            Stok Menipis
                        </h3>
                        <div
                            v-if="stokMenipis.length > 0"
                            class="divide-y divide-gray-100"
                        >
                            <div
                                v-for="produk in stokMenipis"
                                :key="produk.id"
                                class="flex items-center justify-between py-3"
                            >
                                <span class="text-sm text-gray-700">{{
                                    produk.nama_produk
                                }}</span>
                                <span
                                    class="rounded-full px-3 py-1 text-xs font-semibold whitespace-nowrap"
                                    style="background: #fdf2e3; color: #b8790f"
                                >
                                    Sisa {{ produk.stok }}
                                    <span
                                        style="opacity: 0.65; font-weight: 500"
                                        >&times; {{ produk.satuan }}</span
                                    >
                                </span>
                            </div>
                        </div>
                        <p v-else class="text-sm text-gray-400">
                            Semua stok produk aman.
                        </p>
                    </div>

                    <div
                        class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-gray-100"
                    >
                        <h3
                            class="mb-4 flex items-center gap-2 font-semibold"
                            style="
                                font-family: 'Fraunces', serif;
                                color: #5c4a3f;
                            "
                        >
                            <Zap class="h-4 w-4" style="color: #a31d22" />
                            Menu Cepat
                        </h3>
                        <div class="grid grid-cols-2 gap-2">
                            <Link
                                v-for="link in quickLinks"
                                :key="link.route"
                                :href="route(link.route)"
                                class="flex flex-col items-center gap-1.5 rounded-xl px-3 py-3 text-center text-sm font-medium transition-colors"
                                style="background: #fdf2f2; color: #a31d22"
                                @mouseenter="
                                    $event.currentTarget.style.background =
                                        '#fbe4e4'
                                "
                                @mouseleave="
                                    $event.currentTarget.style.background =
                                        '#fdf2f2'
                                "
                            >
                                <component :is="link.icon" class="h-4 w-4" />
                                {{ link.label }}
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
