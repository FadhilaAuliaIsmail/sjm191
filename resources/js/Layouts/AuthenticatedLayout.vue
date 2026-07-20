<script setup>
import Toast from "@/Components/Toast.vue";
import { Link, router, usePage } from "@inertiajs/vue3";
import {
    Bell,
    History,
    LayoutDashboard,
    LogOut,
    Menu,
    Package,
    Settings,
    ShoppingCart,
    Truck,
    UserCog,
    Users,
    Wallet,
    X,
} from "lucide-vue-next";
import { computed, onMounted, onUnmounted, ref } from "vue";

const page = usePage();
const user = page.props.auth.user;

const isPemilik = user.peran === "pemilik_usaha";
const isKasir = user.peran === "kasir";

const stokMenipisCount = computed(() => page.props.stokMenipisCount ?? 0);
const stokMenipisList = computed(() => page.props.stokMenipisList ?? []);
const showNotifDropdown = ref(false);
const notifRef = ref(null);
const sidebarOpen = ref(false);

function logout() {
    router.post(route("logout"));
}

function closeSidebar() {
    sidebarOpen.value = false;
}

// Tutup dropdown notifikasi saat klik di luar area-nya
function handleClickOutside(event) {
    if (notifRef.value && !notifRef.value.contains(event.target)) {
        showNotifDropdown.value = false;
    }
}

onMounted(() => {
    document.addEventListener("click", handleClickOutside);
});

onUnmounted(() => {
    document.removeEventListener("click", handleClickOutside);
});
</script>

<template>
    <div class="h-screen bg-[#FBF8F2] flex overflow-hidden relative">
        <!-- Overlay (mobile/tablet only, saat sidebar terbuka) -->
        <div
            v-if="sidebarOpen"
            @click="closeSidebar"
            class="fixed inset-0 bg-black/50 z-30 xl:hidden"
        ></div>

        <!-- Sidebar -->
        <aside
            class="w-64 flex flex-col shrink-0 fixed xl:static inset-y-0 left-0 z-40 transition-transform duration-300 ease-in-out"
            :class="
                sidebarOpen
                    ? 'translate-x-0'
                    : '-translate-x-full xl:translate-x-0'
            "
            style="
                background: radial-gradient(
                    130% 90% at 20% 0%,
                    #a31d22 0%,
                    #7c1216 55%,
                    #650e11 100%
                );
            "
        >
            <!-- Logo -->
            <div
                class="h-20 flex items-center gap-3 px-5"
                style="border-bottom: 1px solid rgba(250, 246, 238, 0.14)"
            >
                <div
                    class="w-11 h-11 rounded-full flex items-center justify-center shrink-0"
                    style="
                        background: #faf6ee;
                        box-shadow: 0 0 0 2px rgba(250, 246, 238, 0.35);
                    "
                >
                    <img
                        src="/image/Logo.png"
                        alt="Susu Jahe Merah 191"
                        class="w-9 h-9 rounded-full object-cover"
                    />
                </div>
                <div class="leading-tight min-w-0 flex-1">
                    <p
                        class="text-sm font-bold truncate"
                        style="
                            font-family: 'Fraunces', serif;
                            color: #faf6ee;
                            letter-spacing: 0.2px;
                        "
                    >
                        Susu Jahe Merah
                    </p>
                    <p
                        class="text-xs font-semibold"
                        style="color: #e3a93b; letter-spacing: 2px"
                    >
                        191
                    </p>
                </div>
                <!-- Tombol tutup, cuma muncul di mobile/tablet -->
                <button
                    type="button"
                    @click="closeSidebar"
                    class="xl:hidden shrink-0 text-white/70 hover:text-white"
                >
                    <X class="w-5 h-5" />
                </button>
            </div>

            <!-- Nav -->
            <nav class="flex-1 px-3 py-4 space-y-5 overflow-y-auto">
                <!-- ── KASIR ── -->
                <template v-if="isKasir">
                    <div>
                        <p class="nav-category">Operasional</p>
                        <div class="space-y-0.5">
                            <Link
                                :href="route('dashboard')"
                                class="nav-link"
                                :class="{
                                    'nav-link-active':
                                        route().current('dashboard'),
                                }"
                                @click="closeSidebar"
                            >
                                <LayoutDashboard class="nav-icon" />
                                <span>Dashboard</span>
                            </Link>
                            <Link
                                :href="route('pos.index')"
                                class="nav-link"
                                :class="{
                                    'nav-link-active':
                                        route().current('pos.index'),
                                }"
                                @click="closeSidebar"
                            >
                                <ShoppingCart class="nav-icon" />
                                <span>Kasir</span>
                            </Link>
                            <Link
                                :href="route('transaksi.index')"
                                class="nav-link"
                                :class="{
                                    'nav-link-active':
                                        route().current('transaksi.index') ||
                                        route().current('transaksi.show'),
                                }"
                                @click="closeSidebar"
                            >
                                <History class="nav-icon" />
                                <span>Riwayat Transaksi</span>
                            </Link>
                        </div>
                    </div>

                    <div>
                        <p class="nav-category">Manajemen</p>
                        <div class="space-y-0.5">
                            <Link
                                :href="route('produk.index')"
                                class="nav-link"
                                :class="{
                                    'nav-link-active':
                                        route().current('produk.*'),
                                }"
                                @click="closeSidebar"
                            >
                                <Package class="nav-icon" />
                                <span>Kelola Produk</span>
                            </Link>
                        </div>
                    </div>
                </template>

                <!-- ── PEMILIK USAHA ── -->
                <template v-if="isPemilik">
                    <div>
                        <p class="nav-category">Utama</p>
                        <div class="space-y-0.5">
                            <Link
                                :href="route('dashboard')"
                                class="nav-link"
                                :class="{
                                    'nav-link-active':
                                        route().current('dashboard'),
                                }"
                                @click="closeSidebar"
                            >
                                <LayoutDashboard class="nav-icon" />
                                <span>Dashboard</span>
                            </Link>
                        </div>
                    </div>

                    <div>
                        <p class="nav-category">Master Data</p>
                        <div class="space-y-0.5">
                            <Link
                                :href="route('produk.index')"
                                class="nav-link"
                                :class="{
                                    'nav-link-active':
                                        route().current('produk.*'),
                                }"
                                @click="closeSidebar"
                            >
                                <Package class="nav-icon" />
                                <span>Produk</span>
                            </Link>
                            <Link
                                :href="route('data-mitra.index')"
                                class="nav-link"
                                :class="{
                                    'nav-link-active':
                                        route().current('data-mitra.*'),
                                }"
                                @click="closeSidebar"
                            >
                                <Users class="nav-icon" />
                                <span>Data Mitra</span>
                            </Link>
                            <Link
                                :href="route('pengguna.index')"
                                class="nav-link"
                                :class="{
                                    'nav-link-active':
                                        route().current('pengguna.*'),
                                }"
                                @click="closeSidebar"
                            >
                                <UserCog class="nav-icon" />
                                <span>Pengguna</span>
                            </Link>
                        </div>
                    </div>

                    <div>
                        <p class="nav-category">Pengadaan</p>
                        <div class="space-y-0.5">
                            <Link
                                :href="route('barang-produksi.index')"
                                class="nav-link"
                                :class="{
                                    'nav-link-active':
                                        route().current('barang-produksi.*'),
                                }"
                                @click="closeSidebar"
                            >
                                <Truck class="nav-icon" />
                                <span>Barang Produksi</span>
                            </Link>
                        </div>
                    </div>

                    <div>
                        <p class="nav-category">Laporan</p>
                        <div class="space-y-0.5">
                            <Link
                                :href="route('laporan.index')"
                                class="nav-link"
                                :class="{
                                    'nav-link-active':
                                        route().current('laporan.index'),
                                }"
                                @click="closeSidebar"
                            >
                                <Wallet class="nav-icon" />
                                <span>Laporan Keuangan</span>
                            </Link>
                        </div>
                    </div>
                </template>
            </nav>

            <!-- Pengaturan & Logout -->
            <div
                class="p-3 space-y-0.5"
                style="border-top: 1px solid rgba(250, 246, 238, 0.14)"
            >
                <Link
                    :href="route('profile.edit')"
                    class="nav-link"
                    :class="{
                        'nav-link-active': route().current('profile.edit'),
                    }"
                    @click="closeSidebar"
                >
                    <Settings class="nav-icon" />
                    <span>Pengaturan</span>
                </Link>
                <button
                    type="button"
                    @click="logout"
                    class="nav-link w-full text-left nav-link-danger"
                >
                    <LogOut class="nav-icon" />
                    <span>Logout</span>
                </button>
            </div>
        </aside>

        <!-- Main -->
        <div class="flex-1 flex flex-col min-w-0">
            <!-- Topbar -->
            <header
                class="h-16 bg-white border-b border-gray-100 flex items-center justify-between px-4 md:px-8 gap-3"
            >
                <div class="flex items-center gap-3 min-w-0">
                    <!-- Tombol hamburger, cuma muncul di mobile/tablet -->
                    <button
                        type="button"
                        @click="sidebarOpen = true"
                        class="xl:hidden shrink-0 text-gray-500 hover:text-gray-700"
                    >
                        <Menu class="w-6 h-6" />
                    </button>
                    <div class="min-w-0">
                        <slot name="header" />
                    </div>
                </div>

                <!-- Notifikasi + Profil -->
                <div class="flex items-center gap-2 md:gap-4 shrink-0">
                    <div v-if="isPemilik" ref="notifRef" class="relative">
                        <button
                            type="button"
                            @click="showNotifDropdown = !showNotifDropdown"
                            class="relative flex h-9 w-9 items-center justify-center rounded-full text-gray-400 hover:bg-gray-50 hover:text-gray-600"
                            title="Stok menipis"
                        >
                            <Bell class="h-5 w-5" />
                            <span
                                v-if="stokMenipisCount > 0"
                                class="absolute -right-0.5 -top-0.5 flex h-4 min-w-[16px] items-center justify-center rounded-full bg-red-600 px-1 text-[10px] font-bold text-white"
                            >
                                {{ stokMenipisCount }}
                            </span>
                        </button>

                        <div
                            v-if="showNotifDropdown"
                            class="absolute right-0 top-full mt-2 w-72 max-w-[90vw] rounded-xl bg-white shadow-lg ring-1 ring-gray-100 z-50"
                        >
                            <div class="border-b border-gray-100 px-4 py-2.5">
                                <p class="text-sm font-semibold text-gray-700">
                                    Stok Menipis
                                </p>
                            </div>
                            <div class="max-h-72 overflow-y-auto">
                                <div
                                    v-for="p in stokMenipisList"
                                    :key="p.id"
                                    class="flex items-center justify-between gap-3 px-4 py-2.5 hover:bg-gray-50"
                                >
                                    <span
                                        class="text-sm text-gray-700 truncate"
                                        >{{ p.nama_produk }}</span
                                    >
                                    <span
                                        class="shrink-0 rounded-full bg-red-50 px-2 py-0.5 text-xs font-semibold text-red-600 whitespace-nowrap"
                                    >
                                        Sisa {{ p.stok }} {{ p.satuan }}
                                    </span>
                                </div>
                                <p
                                    v-if="stokMenipisList.length === 0"
                                    class="px-4 py-6 text-center text-sm text-gray-400"
                                >
                                    Semua stok aman.
                                </p>
                            </div>
                            <Link
                                :href="route('produk.index')"
                                @click="showNotifDropdown = false"
                                class="block border-t border-gray-100 px-4 py-2.5 text-center text-sm font-medium text-red-600 hover:bg-red-50"
                            >
                                Kelola Produk
                            </Link>
                        </div>
                    </div>

                    <div class="flex items-center gap-2.5">
                        <div
                            class="w-8 h-8 rounded-full bg-red-600 text-white flex items-center justify-center text-sm font-bold shrink-0"
                        >
                            {{ user.name.charAt(0).toUpperCase() }}
                        </div>
                        <div class="text-left leading-tight hidden sm:block">
                            <p class="text-sm font-semibold text-gray-800">
                                {{ user.name }}
                            </p>
                            <p class="text-xs text-gray-400">
                                {{ isPemilik ? "Pemilik Usaha" : "Kasir" }}
                            </p>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Content -->
            <main class="flex-1 p-4 md:p-8 overflow-auto">
                <slot />
            </main>
        </div>

        <Toast />
    </div>
</template>

<style scoped>
.nav-category {
    padding: 0 0.75rem;
    margin-bottom: 0.4rem;
    font-size: 0.6875rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.09em;
    color: rgba(250, 246, 238, 0.45);
}
.nav-link {
    display: flex;
    align-items: center;
    gap: 0.625rem;
    padding: 0.575rem 0.75rem;
    border-radius: 0.625rem;
    font-size: 0.875rem;
    font-weight: 500;
    color: rgba(250, 246, 238, 0.72);
    transition: background-color 0.15s, color 0.15s;
    position: relative;
}
.nav-link:hover {
    background-color: rgba(250, 246, 238, 0.08);
    color: #faf6ee;
}
.nav-link-active {
    background-color: #faf6ee;
    color: #a31d22;
    font-weight: 600;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.18);
}
.nav-link-danger {
    color: rgba(250, 246, 238, 0.6);
}
.nav-link-danger:hover {
    background-color: rgba(250, 246, 238, 0.08);
    color: #f2c9c0;
}
.nav-icon {
    width: 18px;
    height: 18px;
    flex-shrink: 0;
    color: currentColor;
}
</style>
