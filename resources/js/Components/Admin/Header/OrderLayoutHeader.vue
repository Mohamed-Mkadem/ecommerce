<script setup>
import DropdownUser from "./DropdownUser.vue";
import AppLogo from "@/js/Components/AppLogo.vue";
import { useAppearanceStore } from "@/js/stores/appearance";

const appearanceStore = useAppearanceStore();
</script>

<template>
    <header
        class="sticky top-0 z-999 flex w-full border-b border-slate-100 bg-white/95 shadow-[0_4px_20px_rgba(15,23,42,0.06)] backdrop-blur-sm dark:border-neutral-700 dark:bg-neutral-800/95 dark:shadow-black/20">
        <div
            class="flex flex-grow items-center justify-between px-4 py-4 md:px-6 2xl:px-11"
        >
            <div>
                <Link class="block" :href="route('dashboard')">
                    <AppLogo color="blue" class="w-[100px]" />
                </Link>
            </div>

            <div class="flex items-center gap-3 2xsm:gap-4">
                <button type="button"
                    class="flex h-9 w-9 items-center justify-center rounded-full border border-slate-200 bg-white text-lg text-slate-600 shadow-sm transition-colors hover:bg-slate-50 hover:text-primary dark:border-neutral-600 dark:bg-neutral-700 dark:text-neutral-200 dark:hover:bg-neutral-600"
                    :aria-pressed="appearanceStore.isDark"
                    :aria-label="appearanceStore.isDark ? 'Switch to light mode' : 'Switch to dark mode'"
                    :title="appearanceStore.isDark ? 'Switch to light mode' : 'Switch to dark mode'"
                    @click="appearanceStore.toggleDarkMode()">
                    <i :class="appearanceStore.isDark ? 'ri-sun-line' : 'ri-moon-line'" aria-hidden="true"></i>
                </button>
                <!-- User Area -->
                <DropdownUser />
                <!-- User Area -->
            </div>
        </div>
    </header>
</template>
