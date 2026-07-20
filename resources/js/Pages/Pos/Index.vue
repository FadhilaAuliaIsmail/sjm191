<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, router } from "@inertiajs/vue3";
import { Minus, Package, Plus, Search, ShoppingCart } from "lucide-vue-next";
import { computed, ref } from "vue";

const props = defineProps({
    produk: Array,
});

const cart = ref([]); // { produk_id, nama_produk, harga, jumlah, foto }
const bayar = ref("");
const processing = ref(false);
const search = ref("");

const filteredProduk = computed(() => {
    if (!search.value) return props.produk;
    return props.produk.filter((p) =>
        p.nama_produk.toLowerCase().includes(search.value.toLowerCase())
    );
});

function addToCart(produk) {
    const existing = cart.value.find((item) => item.produk_id === produk.id);
    if (existing) {
        if (existing.jumlah < produk.stok) existing.jumlah++;
    } else {
        cart.value.push({
            produk_id: produk.id,
            nama_produk: produk.nama_produk,
            harga: produk.harga,
            jumlah: 1,
            foto: produk.foto,
            stok: produk.stok,
        });
    }
}

function incrementItem(item) {
    if (item.jumlah < item.stok) item.jumlah++;
}

function decrementItem(item) {
    item.jumlah--;
    if (item.jumlah <= 0) {
        cart.value = cart.value.filter((i) => i.produk_id !== item.produk_id);
    }
}

const totalHarga = computed(() =>
    cart.value.reduce((sum, item) => sum + item.harga * item.jumlah, 0)
);

const kembalian = computed(() => {
    const b = parseFloat(bayar.value) || 0;
    return b - totalHarga.value;
});

function formatRupiah(angka) {
    return new Intl.NumberFormat("id-ID", {
        style: "currency",
        currency: "IDR",
        minimumFractionDigits: 0,
    }).format(angka);
}

function submitTransaksi() {
    if (cart.value.length === 0) return alert("Keranjang masih kosong.");
    if (parseFloat(bayar.value) < totalHarga.value)
        return alert("Jumlah bayar kurang dari total.");

    processing.value = true;

    router.post(
        route("transaksi.store"),
        {
            items: cart.value.map((item) => ({
                produk_id: item.produk_id,
                jumlah: item.jumlah,
            })),
            bayar: parseFloat(bayar.value),
        },
        {
            onFinish: () => {
                processing.value = false;
            },
            onSuccess: () => {
                cart.value = [];
                bayar.value = "";
            },
            onError: (errors) => {
                alert(
                    Object.values(errors)[0] ??
                        "Terjadi kesalahan saat memproses transaksi."
                );
            },
        }
    );
}
</script>

<template>
    <Head title="Kasir" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold text-gray-800">Kasir</h2>
        </template>

        <div class="flex flex-col lg:flex-row gap-5 lg:h-full p-4 lg:p-8">
            <!-- Grid Produk -->
            <div class="flex-1 min-w-0">
                <div class="relative mb-4">
                    <Search
                        class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400"
                    />
                    <input
                        v-model="search"
                        type="text"
                        placeholder="Cari produk..."
                        class="w-full pl-11 pr-4 py-3 rounded-2xl border border-[#EFE7DA] bg-white text-sm shadow-sm focus:outline-none focus:ring-2 focus:border-[#a31d22]"
                        style="--tw-ring-color: rgba(163, 29, 34, 0.25)"
                    />
                </div>

                <div
                    class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-3 lg:gap-4 pb-6"
                >
                    <button
                        v-for="p in filteredProduk"
                        :key="p.id"
                        @click="addToCart(p)"
                        class="bg-white rounded-2xl overflow-hidden shadow-sm hover:shadow-md transition-all text-left border border-[#EFE7DA] hover:border-[#e3c9c9] group"
                    >
                        <div class="aspect-square bg-[#FBF8F2] overflow-hidden">
                            <img
                                v-if="p.foto"
                                :src="
                                    p.foto.startsWith('http')
                                        ? p.foto
                                        : `/storage/${p.foto}`
                                "
                                :alt="p.nama_produk"
                                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                            />
                            <div
                                v-else
                                class="w-full h-full flex flex-col items-center justify-center gap-1.5 text-gray-300"
                            >
                                <Package class="w-8 h-8" stroke-width="1.5" />
                                <span class="text-xs">Tanpa Foto</span>
                            </div>
                        </div>
                        <div class="p-3">
                            <p
                                class="font-medium text-gray-800 text-sm truncate"
                            >
                                {{ p.nama_produk }}
                            </p>
                            <p
                                class="font-semibold text-sm mt-1"
                                style="color: #a31d22"
                            >
                                {{ formatRupiah(p.harga) }}
                            </p>
                            <p class="text-xs text-gray-400 mt-0.5">
                                Stok: {{ p.stok }} {{ p.satuan }}
                            </p>
                        </div>
                    </button>
                </div>

                <div
                    v-if="filteredProduk.length === 0"
                    class="flex flex-col items-center justify-center py-24 text-center"
                >
                    <div
                        class="w-16 h-16 rounded-full flex items-center justify-center mb-3"
                        style="background: #faf6ee"
                    >
                        <Package
                            class="w-7 h-7"
                            style="color: #e3a93b"
                            stroke-width="1.5"
                        />
                    </div>
                    <p class="text-gray-400 text-sm">
                        Tidak ada produk ditemukan.
                    </p>
                </div>
            </div>

            <!-- Keranjang -->
            <div
                class="w-full lg:w-96 lg:shrink-0 bg-white rounded-2xl shadow-sm border border-[#EFE7DA] flex flex-col p-4 lg:p-5"
            >
                <div class="flex items-center gap-2 mb-4">
                    <ShoppingCart class="w-4.5 h-4.5" style="color: #a31d22" />
                    <h3 class="font-semibold text-gray-800">Keranjang</h3>
                    <span
                        v-if="cart.length > 0"
                        class="ml-auto text-xs font-semibold text-white rounded-full px-2 py-0.5"
                        style="background: #a31d22"
                    >
                        {{ cart.length }}
                    </span>
                </div>

                <div class="flex-1 overflow-auto space-y-1 -mx-1">
                    <div
                        v-if="cart.length === 0"
                        class="text-gray-400 text-sm text-center py-16"
                    >
                        Belum ada item dipilih.
                    </div>

                    <div
                        v-for="item in cart"
                        :key="item.produk_id"
                        class="flex items-center gap-3 px-1 py-2.5 rounded-xl hover:bg-[#FBF8F2] transition-colors"
                    >
                        <div
                            class="w-11 h-11 rounded-lg bg-[#FBF8F2] overflow-hidden flex-shrink-0 flex items-center justify-center"
                        >
                            <img
                                v-if="item.foto"
                                :src="
                                    item.foto.startsWith('http')
                                        ? item.foto
                                        : `/storage/${item.foto}`
                                "
                                class="w-full h-full object-cover"
                            />
                            <Package v-else class="w-4 h-4 text-gray-300" />
                        </div>
                        <div class="flex-1 min-w-0">
                            <p
                                class="text-sm font-medium text-gray-800 truncate"
                            >
                                {{ item.nama_produk }}
                            </p>
                            <p class="text-xs text-gray-400">
                                {{ formatRupiah(item.harga) }}
                            </p>
                        </div>
                        <div class="flex items-center gap-1.5 shrink-0">
                            <button
                                @click="decrementItem(item)"
                                class="w-6 h-6 rounded-full flex items-center justify-center transition-colors"
                                style="background: #fbf8f2; color: #a31d22"
                                onmouseover="this.style.background='#F5EDE0'"
                                onmouseout="this.style.background='#FBF8F2'"
                            >
                                <Minus class="w-3 h-3" stroke-width="2.5" />
                            </button>
                            <span
                                class="text-sm w-4 text-center font-medium text-gray-700"
                                >{{ item.jumlah }}</span
                            >
                            <button
                                @click="incrementItem(item)"
                                class="w-6 h-6 rounded-full flex items-center justify-center transition-colors"
                                style="background: #fbf8f2; color: #a31d22"
                                onmouseover="this.style.background='#F5EDE0'"
                                onmouseout="this.style.background='#FBF8F2'"
                            >
                                <Plus class="w-3 h-3" stroke-width="2.5" />
                            </button>
                        </div>
                    </div>
                </div>

                <div class="border-t border-[#EFE7DA] pt-4 mt-2 space-y-3">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500">Total</span>
                        <span class="font-bold text-gray-800">{{
                            formatRupiah(totalHarga)
                        }}</span>
                    </div>

                    <input
                        v-model="bayar"
                        type="number"
                        placeholder="Jumlah bayar"
                        class="w-full px-4 py-2.5 rounded-xl border border-[#EFE7DA] text-sm focus:outline-none focus:ring-2 focus:border-[#a31d22]"
                        style="--tw-ring-color: rgba(163, 29, 34, 0.25)"
                    />

                    <div v-if="bayar" class="flex justify-between text-sm">
                        <span class="text-gray-500">Kembalian</span>
                        <span
                            :style="
                                kembalian < 0
                                    ? 'color:#a31d22'
                                    : 'color:#1f7a4d'
                            "
                            class="font-semibold"
                        >
                            {{ formatRupiah(kembalian) }}
                        </span>
                    </div>

                    <button
                        @click="submitTransaksi"
                        :disabled="processing || cart.length === 0"
                        class="w-full text-white font-semibold py-3 rounded-xl transition-colors disabled:opacity-40"
                        style="background: #a31d22"
                        onmouseover="if(!this.disabled) this.style.background='#8a171c'"
                        onmouseout="if(!this.disabled) this.style.background='#a31d22'"
                    >
                        {{ processing ? "Memproses..." : "Bayar" }}
                    </button>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
