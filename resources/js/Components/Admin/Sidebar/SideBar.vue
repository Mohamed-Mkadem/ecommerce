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
        class="absolute top-0 z-9999 flex h-screen w-72.5 flex-col overflow-visible bg-primary duration-300 ease-linear dark:bg-[#303030] lg:static"
        :class="{
            'lg:w-[4.5rem]': sidebarStore.isCollapsed,
            'lg:w-72.5': !sidebarStore.isCollapsed,
            'left-0 -translate-x-full lg:translate-x-0':
                !sidebarStore.isSidebarOpen && languageStore.direction == 'ltr',
            'translate-x-0':
                sidebarStore.isSidebarOpen && languageStore.direction == 'ltr',

            // RTL Classes
            'right-0 translate-x-full lg:translate-x-0':
                !sidebarStore.isSidebarOpen && languageStore.direction == 'rtl',
            'translate-x-0':
                sidebarStore.isSidebarOpen && languageStore.direction == 'rtl',
        }" ref="target">
        <!-- SIDEBAR HEADER -->
        <div class="flex items-center justify-between gap-2 px-4 py-5.5 lg:py-6.5" :class="sidebarStore.isCollapsed ? 'lg:justify-center lg:px-2' : 'lg:justify-between lg:px-6'">
            <Link :href="route('dashboard')" class="min-w-0 overflow-hidden" :class="sidebarStore.isCollapsed ? 'lg:hidden' : ''">
            <AppLogo class="w-[150px]" color="white" />
            </Link>

            <button
                type="button"
                class="hidden h-9 w-9 shrink-0 items-center justify-center rounded-md text-white/80 hover:bg-white/10 hover:text-white lg:flex"
                :aria-label="sidebarStore.isCollapsed ? 'Expand sidebar' : 'Collapse sidebar'"
                :title="sidebarStore.isCollapsed ? 'Expand sidebar' : 'Collapse sidebar'"
                @click="sidebarStore.toggleCollapsed()"
            >
                <i :class="sidebarStore.isCollapsed ? 'ri-menu-unfold-line' : 'ri-menu-fold-line'" class="text-xl"></i>
            </button>

            <button class="block lg:hidden" @click="sidebarStore.isSidebarOpen = false">
                <i class="ri-close-large-line"></i>
            </button>
        </div>
        <!-- SIDEBAR HEADER -->

        <div class="no-scrollbar flex flex-col overflow-y-auto duration-300 ease-linear">
            <!-- Sidebar Menu -->
            <nav class="mt-5 py-4 px-4 lg:mt-2" :class="sidebarStore.isCollapsed ? 'lg:px-2' : 'lg:px-6'">
                <div>
                    <ul class="mb-6 flex flex-col gap-1.5">
                        <SidebarItem v-for="(menuItem, index) in SideBarContent" :item="menuItem" :key="index"
                            :collapsed="sidebarStore.isCollapsed"
                            :index="index" />
                    </ul>
                </div>
            </nav>
            <!-- Sidebar Menu -->
        </div>
    </aside>
</template>
