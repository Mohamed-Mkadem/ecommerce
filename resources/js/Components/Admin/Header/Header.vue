<script setup>
import { useSidebarStore } from "@/js/stores/sidebar";
import DropdownNotification from "./DropdownNotification.vue";
import DropdownUser from "./DropdownUser.vue";
import LanguageSwitcher from "./LanguageSwitcher.vue";
import AppLogo from "@/js/Components/AppLogo.vue";
const { toggleSidebar } = useSidebarStore();
const sidebarStore = useSidebarStore();
defineProps({
    notifications: Array,
    notifying: Number,
});
defineEmits(["handle-link"]);
</script>

<template>
    <header class="sticky top-0 z-999 flex w-full border-b border-slate-100 bg-white/95 shadow-[0_4px_20px_rgba(15,23,42,0.06)] backdrop-blur-sm">
        <div class="flex flex-grow items-center justify-between py-4 px-4 lg:justify-end md:px-6 2xl:px-11">
            <div class="flex items-center gap-2 sm:gap-4 lg:hidden">
                <!-- Hamburger Toggle BTN -->
                <button class="z-99999 block rounded-sm border border-stroke bg-white p-1.5 shadow-sm lg:hidden" @click="
                    () => {
                        toggleSidebar();
                    }
                ">
                    <span class="relative block h-5.5 w-5.5 cursor-pointer">
                        <span class="block absolute right-0 h-full w-full">
                            <span
                                class="relative top-0 left-0 my-1 block h-0.5 w-0 rounded-sm bg-black delay-[0] duration-200 ease-in-out"
                                :class="{
                                    '!w-full delay-300':
                                        !sidebarStore.isSidebarOpen,
                                }"></span>
                            <span
                                class="relative top-0 left-0 my-1 block h-0.5 w-0 rounded-sm bg-black delay-150 duration-200 ease-in-out"
                                :class="{
                                    '!w-full delay-400':
                                        !sidebarStore.isSidebarOpen,
                                }"></span>
                            <span
                                class="relative top-0 left-0 my-1 block h-0.5 w-0 rounded-sm bg-black delay-200 duration-200 ease-in-out"
                                :class="{
                                    '!w-full delay-500':
                                        !sidebarStore.isSidebarOpen,
                                }"></span>
                        </span>
                        <span class="block absolute right-0 h-full w-full rotate-45">
                            <span
                                class="absolute left-2.5 top-0 block h-full w-0.5 rounded-sm bg-black delay-300 duration-200 ease-in-out"
                                :class="{
                                    '!h-0 delay-[0]':
                                        !sidebarStore.isSidebarOpen,
                                }"></span>
                            <span
                                class="delay-400 absolute left-0 top-2.5 block h-0.5 w-full rounded-sm bg-black duration-200 ease-in-out"
                                :class="{
                                    '!h-0 delay-200':
                                        !sidebarStore.isSidebarOpen,
                                }"></span>
                        </span>
                    </span>
                </button>
                <!-- Hamburger Toggle BTN -->
                <Link class="block flex-shrink-0 lg:hidden" :href="route('dashboard')">
                <AppLogo color="blue" class="xsm:w-[100px] w-[60px]" />
                </Link>
            </div>

            <div class="flex items-center gap-3 2xsm:gap-7">
                <!-- Language Switcher Start -->
                <LanguageSwitcher></LanguageSwitcher>
                <!-- Language Switcher End -->

                <ul class="flex items-center gap-2 2xsm:gap-4">
                    <!-- Notification Menu Area -->
                    <DropdownNotification :notifications="notifications" :notifying="notifying"
                        @handle-link="$emit('handle-link', $event)" />
                    <!-- Notification Menu Area -->
                </ul>

                <!-- User Area -->
                <DropdownUser />
                <!-- User Area -->
            </div>
        </div>
    </header>
</template>
