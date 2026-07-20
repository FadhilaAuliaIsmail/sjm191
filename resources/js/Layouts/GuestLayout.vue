<script setup>
import Toast from "@/Components/Toast.vue";
</script>

<template>
    <div
        class="guest-bg relative flex min-h-screen items-center justify-center overflow-hidden px-4 py-10"
    >
        <!-- Pola bahan-bahan: jahe, gula merah, tetes susu -->
        <svg
            class="pointer-events-none absolute inset-0 h-full w-full opacity-[0.09]"
            xmlns="http://www.w3.org/2000/svg"
        >
            <defs>
                <pattern
                    id="bahan-alami"
                    width="170"
                    height="170"
                    patternUnits="userSpaceOnUse"
                    patternTransform="rotate(8)"
                >
                    <!-- Akar jahe -->
                    <path
                        d="M15 40 Q10 25 20 15 Q28 22 26 32 Q35 25 42 30 Q38 40 28 42 Q34 50 30 58 Q20 55 18 45 Q10 50 5 42 Q10 38 15 40 Z"
                        fill="none"
                        stroke="white"
                        stroke-width="1.3"
                        stroke-linejoin="round"
                    />

                    <!-- Gula merah (bongkahan gula aren, bentuk silinder pendek) -->
                    <g transform="translate(95 20)">
                        <path
                            d="M0 10 Q0 2 14 2 Q28 2 28 10 L28 26 Q28 34 14 34 Q0 34 0 26 Z"
                            fill="none"
                            stroke="white"
                            stroke-width="1.3"
                        />
                        <path
                            d="M0 10 Q14 18 28 10"
                            fill="none"
                            stroke="white"
                            stroke-width="1.1"
                        />
                    </g>

                    <!-- Tetes susu -->
                    <path
                        d="M120 95 Q128 108 128 116 Q128 126 120 126 Q112 126 112 116 Q112 108 120 95 Z"
                        fill="none"
                        stroke="white"
                        stroke-width="1.3"
                    />

                    <!-- Akar jahe kecil, versi kedua -->
                    <path
                        d="M40 110 Q35 98 44 90 Q50 96 48 104 Q56 99 61 104 Q57 112 49 113 Q54 120 50 127 Q42 124 41 116 Q34 120 30 113 Q35 110 40 110 Z"
                        fill="none"
                        stroke="white"
                        stroke-width="1.1"
                        stroke-linejoin="round"
                    />

                    <!-- Gelas susu kecil -->
                    <g transform="translate(70 130)">
                        <path
                            d="M2 0 L22 0 L19 26 Q19 30 12 30 Q5 30 5 26 Z"
                            fill="none"
                            stroke="white"
                            stroke-width="1.1"
                        />
                        <line
                            x1="4.3"
                            y1="9"
                            x2="19.7"
                            y2="9"
                            stroke="white"
                            stroke-width="1"
                        />
                    </g>
                </pattern>
            </defs>
            <rect width="100%" height="100%" fill="url(#bahan-alami)" />
        </svg>

        <div class="relative z-10 w-full max-w-sm">
            <!-- Logo -->
            <div class="mb-[-2.75rem] flex justify-center">
                <div
                    class="rounded-full bg-white p-1.5 shadow-lg ring-4 ring-white/40"
                >
                    <img
                        src="/Image/Logo.png"
                        alt="Susu Jahe Merah 191"
                        class="h-20 w-20 rounded-full object-cover"
                    />
                </div>
            </div>

            <!-- Card -->
            <div
                class="guest-card rounded-3xl bg-[#fffbf5] px-7 pb-7 pt-14 shadow-2xl"
            >
                <div class="mb-6 text-center">
                    <p
                        class="guest-brand text-lg font-extrabold tracking-tight text-red-700"
                    >
                        Susu Jahe Merah
                    </p>
                    <p
                        class="text-xs font-semibold tracking-[0.2em] text-amber-500"
                    >
                        191
                    </p>
                </div>

                <slot />
            </div>

            <p class="mt-5 text-center text-xs text-white/70">
                Resep Betawi bang Haji Gama — Bekasi, Jawa Barat
            </p>
        </div>

        <Toast />
    </div>
</template>

<style scoped>
@import url("https://fonts.googleapis.com/css2?family=Baloo+2:wght@600;700;800&display=swap");

.guest-bg {
    background: radial-gradient(
        circle at 30% 15%,
        #b91c1c 0%,
        #7f1d1d 55%,
        #5c1414 100%
    );
}
.guest-brand {
    font-family: "Baloo 2", system-ui, sans-serif;
}
.guest-card {
    animation: rise 0.45s ease-out;
}
@keyframes rise {
    from {
        opacity: 0;
        transform: translateY(10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>
