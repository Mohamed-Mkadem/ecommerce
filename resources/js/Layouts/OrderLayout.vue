<script setup>
import OrderLayoutHeader from "@/js/Components/Admin/Header/OrderLayoutHeader.vue";
import { useLanguageStore } from "@/js/stores/Language";
import { onMounted, ref, watch } from "vue";
import { router } from "@inertiajs/vue3";
import { useAppearanceStore } from "@/js/stores/appearance";
const languageStore = useLanguageStore();
const appearanceStore = useAppearanceStore();
import Loader from "@/js/Components/Loader.vue";
const processing = ref(false);

function syncBodyAppearance() {
    document.body.classList.toggle("bg-whiten", !appearanceStore.isDark);
    document.body.classList.toggle("bg-[#202124]", appearanceStore.isDark);
    document.body.classList.toggle("text-body", !appearanceStore.isDark);
    document.body.classList.toggle("text-neutral-200", appearanceStore.isDark);
}

router.on("start", () => (processing.value = true));
router.on("finish", () => (processing.value = false));
onMounted(() => {
    document.documentElement.setAttribute("dir", languageStore.direction);

    document.body.setAttribute(
        "class",
        "relative z-1 bg-whiten  text-base font-normal text-body ",
    );
    syncBodyAppearance();
});

watch(() => appearanceStore.isDark, syncBodyAppearance);
</script>

<template>
    <div
        class="order-layout container-3xl relative min-h-screen bg-whiten text-body transition-colors dark:bg-[#202124] dark:text-neutral-200"
        :class="{ dark: appearanceStore.isDark }">
        <OrderLayoutHeader />
        <main class="px-4 pb-12 pt-8 lg:pt-12">
            <slot></slot>
        </main>
    </div>
    <Loader v-if="processing" />
</template>

<style>
.order-layout.dark {
    background-color: #202124;
    color: #e5e7eb;
}

.order-layout.dark .order-panel {
    border-color: #404040;
    background-color: #262626;
    box-shadow: 0 8px 30px rgba(0, 0, 0, 0.18);
    color: #d4d4d4;
}

.order-layout.dark .order-panel h2,
.order-layout.dark .order-panel h3,
.order-layout.dark .order-panel h4,
.order-layout.dark .order-panel .text-sky-900,
.order-layout.dark .order-panel .text-sky-700,
.order-layout.dark .order-panel .text-neutral-900,
.order-layout.dark .order-panel .text-neutral-700,
.order-layout.dark .order-panel .text-neutral-500,
.order-layout.dark .order-panel .text-slate-700,
.order-layout.dark .order-panel .text-slate-800,
.order-layout.dark .order-panel .text-primary {
    color: #e5e7eb !important;
}

.order-layout.dark .order-panel input:not([type="checkbox"]):not([type="submit" i]),
.order-layout.dark .order-panel select,
.order-layout.dark .order-panel textarea {
    border-color: #525252;
    background-color: #303030;
    color: #f5f5f5;
}

.order-layout.dark .order-panel input::placeholder,
.order-layout.dark .order-panel textarea::placeholder {
    color: #a3a3a3;
}

.order-layout.dark .order-panel .border-neutral-200,
.order-layout.dark .order-panel .border-zinc-200 {
    border-color: #525252;
}

.order-layout.dark .order-panel button.w-10:hover {
    background-color: #404040;
}

.order-form input:not([type="checkbox"]):not([type="submit" i]),
.order-form select,
.order-form textarea {
    min-height: 2.75rem;
    border: 1px solid #dbe3ec;
    border-radius: 0.75rem;
    background-color: #f8fafc;
    padding: 0.625rem 0.875rem;
    color: #334155;
    box-shadow: inset 0 1px 2px rgba(15, 23, 42, 0.03);
    transition: border-color 160ms ease, box-shadow 160ms ease, background-color 160ms ease;
}

.order-form select {
    appearance: none;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%239ca3af' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='m6 9 6 6 6-6'/%3E%3C/svg%3E");
    background-position: right 0.9rem center;
    background-repeat: no-repeat;
    background-size: 1rem;
    padding-right: 2.75rem;
}

[dir="rtl"] .order-form select {
    background-position: left 0.9rem center;
    padding-right: 0.875rem;
    padding-left: 2.75rem;
}

.order-form input:not([type="checkbox"]):not([type="submit" i]):focus,
.order-form select:focus,
.order-form textarea:focus {
    border-color: #0f766e;
    background-color: #fff;
    outline: none;
    box-shadow: 0 0 0 3px rgba(15, 118, 110, 0.14);
}

.order-form input::placeholder,
.order-form textarea::placeholder {
    color: #94a3b8;
}

.order-form label {
    color: #475569;
    font-weight: 600;
}

.order-form input[type="checkbox"] {
    accent-color: #0f766e;
}

.order-form input[type="submit"] {
    min-height: 2.875rem;
    border: 0;
    border-radius: 0.75rem;
    background: #0f766e;
    transition: background-color 160ms ease, transform 160ms ease;
}

.order-form input[type="submit"]:hover:not(:disabled) {
    background: #0d9488;
    transform: translateY(-1px);
}

.order-layout.dark .order-form input:not([type="checkbox"]):not([type="submit" i]),
.order-layout.dark .order-form select,
.order-layout.dark .order-form textarea {
    border-color: #525252;
    background-color: #303030;
    color: #f5f5f5;
    box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.15);
}

.order-layout.dark .order-form input:not([type="checkbox"]):not([type="submit" i]):focus,
.order-layout.dark .order-form select:focus,
.order-layout.dark .order-form textarea:focus {
    border-color: #2dd4bf;
    background-color: #303030;
    box-shadow: 0 0 0 3px rgba(45, 212, 191, 0.16);
}

.order-layout.dark .order-form input::placeholder,
.order-layout.dark .order-form textarea::placeholder {
    color: #a3a3a3;
}

.order-layout.dark .order-form label {
    color: #d4d4d4;
}

.order-layout.dark .order-form input[type="checkbox"] {
    accent-color: #2dd4bf;
}

.order-layout.dark .order-form select {
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%23a3a3a3' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='m6 9 6 6 6-6'/%3E%3C/svg%3E");
}

.order-layout.dark .order-form .datepicker + div {
    color: #a3a3a3 !important;
}

.order-products > div {
    overflow: hidden;
    border: 1px solid #e2e8f0;
    border-radius: 0.875rem;
    background: #fff;
    padding: 0.5rem;
}

.order-products .text-meta-5 {
    color: #0f766e;
}

.order-products button {
    border-radius: 0.75rem;
    background: #0f766e !important;
    transition: background-color 160ms ease, transform 160ms ease;
}

.order-products button:hover {
    background: #0d9488 !important;
    transform: translateY(-1px);
}

.order-layout.dark .order-products > div {
    border-color: #404040;
    background: #2b2b2b;
}

.order-layout.dark .order-products .text-meta-5 {
    color: #5eead4;
}
</style>
