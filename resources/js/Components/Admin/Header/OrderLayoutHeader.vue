<script setup>
import DropdownNotification from "./DropdownNotification.vue";
import DropdownUser from "./DropdownUser.vue";
import LanguageSwitcher from "./LanguageSwitcher.vue";
import AppLogo from "@/js/Components/AppLogo.vue";
</script>

<template>
    <header class="sticky top-0 z-999 flex w-full bg-white drop-shadow-1">
        <div
            class="flex flex-grow items-center justify-between py-4 px-4 shadow-2 md:px-6 2xl:px-11"
        >
            <div>
                <Link class="block" :href="route('dashboard')">
                    <AppLogo color="blue" class="w-[45px]" />
                </Link>
            </div>

            <div class="flex items-center gap-3 2xsm:gap-7">
                <!-- User Area -->
                <DropdownUser />
                <!-- User Area -->
            </div>
        </div>
    </header>
</template>
