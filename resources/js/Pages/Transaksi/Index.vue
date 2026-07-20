<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, router } from "@inertiajs/vue3";
import { computed, ref } from "vue";

const props = defineProps({
    transaksis: Array,
    totalPendapatan: Number,
    jumlahTransaksi: Number,
    filters: Object,
});

const tanggalAwal = ref(props.filters.tanggal_awal);
const tanggalAkhir = ref(props.filters.tanggal_akhir);

function toDateString(date) {
    return date.toISOString().split("T")[0];
}

function terapkanFilter() {
    router.get(
        route("transaksi.index"),
        { tanggal_awal: tanggalAwal.value, tanggal_akhir: tanggalAkhir.value },
        { preserveState: true }
    );
}

function setHariIni() {
    const hariIni = toDateString(new Date());
    tanggalAwal.value = hariIni;
    tanggalAkhir.value = hariIni;
    terapkanFilter();
}

function setMingguIni() {
    const now = new Date();
    const hari = now.getDay() === 0 ? 6 : now.getDay() - 1;
    const awal = new Date(now);
    awal.setDate(now.getDate() - hari);
    tanggalAwal.value = toDateString(awal);
    tanggalAkhir.value = toDateString(now);
    terapkanFilter();
}

function setBulanIni() {
    const now = new Date();
    const awal = new Date(now.getFullYear(), now.getMonth(), 1);
    tanggalAwal.value = toDateString(awal);
    tanggalAkhir.value = toDateString(now);
    terapkanFilter();
}

const filterAktif = computed(() => {
    const hariIni = toDateString(new Date());
    if (tanggalAwal.value === hariIni && tanggalAkhir.value === hariIni)
        return "hari_ini";

    const now = new Date();
    const hari = now.getDay() === 0 ? 6 : now.getDay() - 1;
    const awalMinggu = new Date(now);
    awalMinggu.setDate(now.getDate() - hari);
    if (
        tanggalAwal.value === toDateString(awalMinggu) &&
        tanggalAkhir.value === hariIni
    )
        return "minggu_ini";

    const awalBulan = toDateString(
        new Date(now.getFullYear(), now.getMonth(), 1)
    );
    if (tanggalAwal.value === awalBulan && tanggalAkhir.value === hariIni)
        return "bulan_ini";

    return null;
});

function formatRupiah(angka) {
    return new Intl.NumberFormat("id-ID", {
        style: "currency",
        currency: "IDR",
        minimumFractionDigits: 0,
    }).format(angka);
}

function formatTanggal(tanggal) {
    return new Date(tanggal).toLocaleString("id-ID", {
        day: "2-digit",
        month: "short",
        year: "numeric",
        hour: "2-digit",
        minute: "2-digit",
    });
}
</script>

<template>
    <Head title="Riwayat Transaksi" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold text-gray-800">
                Riwayat Transaksi
            </h2>
        </template>

        <div class="p-8">
            <!-- Filter -->
            <div
                class="bg-white rounded-2xl shadow-sm border border-[#EFE7DA] p-5 mb-5"
            >
                <div class="flex flex-wrap items-end justify-between gap-4">
                    <div class="flex flex-wrap items-end gap-4">
                        <div>
                            <label class="block text-sm text-gray-500 mb-1"
                                >Dari Tanggal</label
                            >
                            <input
                                v-model="tanggalAwal"
                                type="date"
                                class="px-4 py-2 rounded-xl border border-[#EFE7DA] focus:outline-none focus:ring-2"
                                style="--tw-ring-color: rgba(163, 29, 34, 0.25)"
                            />
                        </div>
                        <div>
                            <label class="block text-sm text-gray-500 mb-1"
                                >Sampai Tanggal</label
                            >
                            <input
                                v-model="tanggalAkhir"
                                type="date"
                                class="px-4 py-2 rounded-xl border border-[#EFE7DA] focus:outline-none focus:ring-2"
                                style="--tw-ring-color: rgba(163, 29, 34, 0.25)"
                            />
                        </div>
                        <button
                            @click="terapkanFilter"
                            class="text-white font-medium px-5 py-2.5 rounded-xl transition-colors hover:opacity-90"
                            style="background: #a31d22"
                        >
                            Terapkan
                        </button>
                    </div>
                    <div class="flex flex-wrap gap-2">
                        <button
                            @click="setHariIni"
                            :class="
                                filterAktif === 'hari_ini'
                                    ? 'text-white border-transparent'
                                    : 'bg-white border-[#EFE7DA] text-gray-600 hover:bg-[#FBF8F2]'
                            "
                            :style="
                                filterAktif === 'hari_ini'
                                    ? 'background:#a31d22'
                                    : ''
                            "
                            class="px-4 py-2 rounded-xl border text-sm font-medium transition-colors"
                        >
                            Hari Ini
                        </button>
                        <button
                            @click="setMingguIni"
                            :class="
                                filterAktif === 'minggu_ini'
                                    ? 'text-white border-transparent'
                                    : 'bg-white border-[#EFE7DA] text-gray-600 hover:bg-[#FBF8F2]'
                            "
                            :style="
                                filterAktif === 'minggu_ini'
                                    ? 'background:#a31d22'
                                    : ''
                            "
                            class="px-4 py-2 rounded-xl border text-sm font-medium transition-colors"
                        >
                            Minggu Ini
                        </button>
                        <button
                            @click="setBulanIni"
                            :class="
                                filterAktif === 'bulan_ini'
                                    ? 'text-white border-transparent'
                                    : 'bg-white border-[#EFE7DA] text-gray-600 hover:bg-[#FBF8F2]'
                            "
                            :style="
                                filterAktif === 'bulan_ini'
                                    ? 'background:#a31d22'
                                    : ''
                            "
                            class="px-4 py-2 rounded-xl border text-sm font-medium transition-colors"
                        >
                            Bulan Ini
                        </button>
                    </div>
                </div>
            </div>

            <!-- Ringkasan -->
            <div class="grid grid-cols-2 gap-4 mb-5">
                <div
                    class="bg-white rounded-2xl shadow-sm border border-[#EFE7DA] p-5"
                >
                    <p class="text-sm text-gray-400">Total Pendapatan</p>
                    <p class="text-2xl font-bold text-gray-800 mt-1">
                        {{ formatRupiah(totalPendapatan) }}
                    </p>
                </div>
                <div
                    class="bg-white rounded-2xl shadow-sm border border-[#EFE7DA] p-5"
                >
                    <p class="text-sm text-gray-400">Jumlah Transaksi</p>
                    <p class="text-2xl font-bold text-gray-800 mt-1">
                        {{ jumlahTransaksi }}
                    </p>
                </div>
            </div>

            <!-- Daftar Transaksi -->
            <div
                class="bg-white rounded-2xl shadow-sm border border-[#EFE7DA] overflow-hidden"
            >
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-[#FBF8F2] text-gray-500 text-left">
                            <th class="px-5 py-3 font-medium">No. Transaksi</th>
                            <th class="px-5 py-3 font-medium">Tanggal</th>
                            <th class="px-5 py-3 font-medium">Kasir</th>
                            <th class="px-5 py-3 font-medium text-right">
                                Total
                            </th>
                            <th class="px-5 py-3 font-medium text-center">
                                Aksi
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#F5EDE0]">
                        <tr
                            v-for="transaksi in transaksis"
                            :key="transaksi.id"
                            class="hover:bg-[#FBF8F2] transition-colors"
                        >
                            <td class="px-5 py-3 font-medium text-gray-800">
                                #{{ transaksi.id }}
                            </td>
                            <td class="px-5 py-3 text-gray-600">
                                {{ formatTanggal(transaksi.tanggal_transaksi) }}
                            </td>
                            <td class="px-5 py-3 text-gray-600">
                                {{ transaksi.user?.name }}
                            </td>
                            <td
                                class="px-5 py-3 text-right font-medium text-gray-800"
                            >
                                {{ formatRupiah(transaksi.total_harga) }}
                            </td>
                            <td class="px-5 py-3 text-center">
                                <Link
                                    :href="
                                        route('transaksi.show', transaksi.id)
                                    "
                                    class="font-medium hover:underline"
                                    style="color: #a31d22"
                                >
                                    Lihat Detail
                                </Link>
                            </td>
                        </tr>
                        <tr v-if="transaksis.length === 0">
                            <td
                                colspan="5"
                                class="px-5 py-12 text-center text-gray-400"
                            >
                                Tidak ada transaksi pada rentang tanggal ini.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
