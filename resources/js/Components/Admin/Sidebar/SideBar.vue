<script setup>
import { useSidebarStore } from "@/js/stores/sidebar";
import { onClickOutside } from "@vueuse/core";
import { ref } from "vue";
import SidebarItem from "./SidebarItem.vue";

import { useLanguageStore } from "@/js/stores/Language";
import SideBarContent from "./SideBarContent";
import AppLogo from "@/js/Components/AppLogo.vue";
const languageStore = useLanguageStore();

const target = ref(null);
const sidebarStore = useSidebarStore();

onClickOutside(target, () => {
    sidebarStore.isSidebarOpen = false;
});
</script>

<template>
    <aside
        class="absolute top-0 z-9999 flex h-screen w-72.5 flex-col overflow-y-hidden bg-primary duration-300 ease-linear lg:static"
        :class="{
            'left-0 -translate-x-full lg:translate-x-0':
                !sidebarStore.isSidebarOpen && languageStore.direction == 'ltr',
            'translate-x-0':
                sidebarStore.isSidebarOpen && languageStore.direction == 'ltr',

            // RTL Classes
            'right-0 translate-x-full lg:translate-x-0':
                !sidebarStore.isSidebarOpen && languageStore.direction == 'rtl',
            'translate-x-0':
                sidebarStore.isSidebarOpen && languageStore.direction == 'rtl',
        }"
        ref="target"
    >
        <!-- SIDEBAR HEADER -->
        <div
            class="flex items-center justify-between lg:justify-center gap-2 px-6 py-5.5 lg:py-6.5"
        >
            <Link :href="route('dashboard')">
                <AppLogo class="w-[60px]" color="white" />
            </Link>

            <button
                class="block lg:hidden"
                @click="sidebarStore.isSidebarOpen = false"
            >
                <i class="ri-close-large-line"></i>
            </button>
        </div>
        <!-- SIDEBAR HEADER -->

        <div
            class="no-scrollbar flex flex-col overflow-y-auto duration-300 ease-linear"
        >
            <!-- Sidebar Menu -->
            <nav class="mt-5 py-4 px-4 lg:mt-2 lg:px-6">
                <div>
                    <ul class="mb-6 flex flex-col gap-1.5">
                        <SidebarItem
                            v-for="(menuItem, index) in SideBarContent"
                            :item="menuItem"
                            :key="index"
                            :index="index"
                        />
                    </ul>
                </div>
            </nav>
            <!-- Sidebar Menu -->
        </div>
    </aside>
</template>
