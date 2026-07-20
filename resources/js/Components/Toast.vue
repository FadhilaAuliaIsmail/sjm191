<script setup>
import { usePage } from "@inertiajs/vue3";
import { onMounted, ref, watch } from "vue";

const page = usePage();
const toasts = ref([]);
let idCounter = 0;

function pushToast(type, message) {
    const id = ++idCounter;
    toasts.value.push({ id, type, message });
    setTimeout(() => {
        toasts.value = toasts.value.filter((t) => t.id !== id);
    }, 4000);
}

function dismiss(id) {
    toasts.value = toasts.value.filter((t) => t.id !== id);
}

function checkFlash(flash) {
    if (!flash) return;
    if (flash.success) pushToast("success", flash.success);
    if (flash.error) pushToast("error", flash.error);
}

// Cek begitu komponen tampil (perlu, karena tiap pindah halaman Toast
// ini di-mount ulang dari awal, jadi watch() saja tidak akan sempat
// mendeteksi flash yang sudah ada sejak awal render).
onMounted(() => {
    checkFlash(page.props.flash);
});

// Tetap watch juga, untuk kasus tetap di halaman yang sama
// (misal filter tanggal pakai preserveState).
watch(
    () => page.props.flash,
    (flash) => checkFlash(flash),
    { deep: true }
);
</script>

<template>
    <Teleport to="body">
        <div
            class="pointer-events-none fixed right-4 top-4 z-[100] flex w-full max-w-sm flex-col gap-2"
        >
            <TransitionGroup name="toast">
                <div
                    v-for="t in toasts"
                    :key="t.id"
                    class="pointer-events-auto flex items-start gap-3 rounded-xl px-4 py-3 shadow-lg ring-1"
                    :class="
                        t.type === 'success'
                            ? 'bg-white ring-green-100'
                            : 'bg-white ring-red-100'
                    "
                >
                    <div
                        class="mt-0.5 flex h-5 w-5 shrink-0 items-center justify-center rounded-full text-white"
                        :class="
                            t.type === 'success' ? 'bg-green-500' : 'bg-red-500'
                        "
                    >
                        <svg
                            v-if="t.type === 'success'"
                            class="h-3 w-3"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="3"
                                d="M5 13l4 4L19 7"
                            />
                        </svg>
                        <svg
                            v-else
                            class="h-3 w-3"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="3"
                                d="M6 18L18 6M6 6l12 12"
                            />
                        </svg>
                    </div>
                    <p class="flex-1 text-sm text-gray-700">{{ t.message }}</p>
                    <button
                        @click="dismiss(t.id)"
                        class="text-gray-300 hover:text-gray-500"
                    >
                        <svg
                            class="h-4 w-4"
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
            </TransitionGroup>
        </div>
    </Teleport>
</template>

<style scoped>
.toast-enter-active,
.toast-leave-active {
    transition: all 0.25s ease;
}
.toast-enter-from {
    opacity: 0;
    transform: translateX(20px);
}
.toast-leave-to {
    opacity: 0;
    transform: translateX(20px);
}
</style>
