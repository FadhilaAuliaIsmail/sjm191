<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link } from "@inertiajs/vue3";

const props = defineProps({
    transaksi: Object,
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
        month: "long",
        year: "numeric",
        hour: "2-digit",
        minute: "2-digit",
    });
}

function cetakStruk() {
    window.open(route("transaksi.struk", props.transaksi.id), "_blank");
}
</script>

<template>
    <Head title="Detail Transaksi" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-lg md:text-xl font-semibold text-gray-800">
                Detail Transaksi #{{ transaksi.id }}
            </h2>
        </template>

        <div class="p-4 md:p-8">
            <div class="max-w-2xl mx-auto">
                <div
                    class="bg-white rounded-2xl shadow-sm border border-[#EFE7DA] p-4 md:p-6"
                >
                    <div
                        class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-6 pb-4 border-b border-[#EFE7DA]"
                    >
                        <div>
                            <p class="text-sm text-gray-400">
                                Tanggal Transaksi
                            </p>
                            <p class="font-medium text-gray-800">
                                {{ formatTanggal(transaksi.tanggal_transaksi) }}
                            </p>
                        </div>
                        <div class="sm:text-right">
                            <p class="text-sm text-gray-400">Kasir</p>
                            <p class="font-medium text-gray-800">
                                {{ transaksi.user?.name }}
                            </p>
                        </div>
                    </div>

                    <div class="space-y-3 mb-6">
                        <div
                            v-for="detail in transaksi.detail_transaksi"
                            :key="detail.id"
                            class="flex items-start justify-between gap-3 text-sm"
                        >
                            <div class="flex-1 min-w-0">
                                <p class="font-medium text-gray-800 truncate">
                                    {{ detail.produk?.nama_produk }}
                                </p>
                                <p class="text-gray-400">
                                    {{ detail.jumlah }} x
                                    {{ formatRupiah(detail.harga) }}
                                </p>
                            </div>
                            <p class="font-medium text-gray-800 shrink-0">
                                {{ formatRupiah(detail.subtotal) }}
                            </p>
                        </div>
                    </div>

                    <div class="border-t border-[#EFE7DA] pt-4 space-y-2">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500">Total</span>
                            <span class="font-bold text-gray-800">{{
                                formatRupiah(transaksi.total_harga)
                            }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500">Bayar</span>
                            <span class="text-gray-800">{{
                                formatRupiah(transaksi.bayar)
                            }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500">Kembalian</span>
                            <span
                                class="font-semibold"
                                style="color: #1f7a4d"
                                >{{ formatRupiah(transaksi.kembalian) }}</span
                            >
                        </div>
                    </div>

                    <div class="flex flex-col sm:flex-row gap-3 mt-6">
                        <button
                            @click="cetakStruk"
                            class="flex-1 text-white font-semibold py-3 rounded-xl transition-colors hover:opacity-90 order-1 sm:order-none"
                            style="background: #a31d22"
                        >
                            Cetak Struk
                        </button>
                        <Link
                            :href="route('pos.index')"
                            class="px-6 py-3 rounded-xl border border-[#EFE7DA] text-gray-600 hover:bg-[#FBF8F2] font-medium text-center transition-colors"
                        >
                            Kembali ke Kasir
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
