<script setup>
import { ref } from "vue";
import { onClickOutside } from "@vueuse/core";
import NavContent from "./NavContent";
import AppLogo from "../AppLogo.vue";

const menu = ref(null);
const isNavOpen = ref(false);

onClickOutside(menu, () => {
    isNavOpen.value = false;
});

function closeNav() {
    isNavOpen.value = false;
}
</script>

<template>
    <div ref="menu" class="slg:hidden">
        <button type="button"
            class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full text-brown transition hover:bg-corange/10 hover:text-cOrangeDark focus:outline-none focus-visible:ring-2 focus-visible:ring-corange"
            :aria-label="$t(isNavOpen ? 'Close Menu' : 'Menu')" :aria-expanded="isNavOpen"
            aria-controls="mobile-navigation" @click="isNavOpen = !isNavOpen">
            <i :class="isNavOpen ? 'ri-close-line' : 'ri-menu-line'" class="text-2xl" aria-hidden="true"></i>
        </button>

        <Transition enter-active-class="transition-opacity duration-300"
            leave-active-class="transition-opacity duration-300" enter-from-class="opacity-0"
            leave-to-class="opacity-0">
            <div v-if="isNavOpen" class="fixed inset-0 z-[1000] slg:hidden">
                <button type="button" class="absolute inset-0 h-full w-full bg-brown/45" :aria-label="$t('Close Menu')"
                    @click="closeNav"></button>

                <Transition enter-active-class="transition-transform duration-300 ease-out"
                    leave-active-class="transition-transform duration-300 ease-in"
                    enter-from-class="ltr:-translate-x-full rtl:translate-x-full" enter-to-class="translate-x-0"
                    leave-from-class="translate-x-0" leave-to-class="ltr:-translate-x-full rtl:translate-x-full">
                    <nav v-if="isNavOpen" id="mobile-navigation"
                        class="absolute inset-y-0 start-0 flex w-[min(82vw,340px)] flex-col bg-offwhite shadow-2xl"
                        :aria-label="$t('Navigation')">
                        <div class="flex items-center justify-between border-b border-line px-5 py-5">
                            <Link :href="route('FE.home')" aria-label="Home" class="shrink-0">
                            <AppLogo
                                class="w-[clamp(100px,36vw,150px)] drop-shadow-xl transition-transform duration-300 hover:scale-105" />
                            </Link>
                            <button type="button"
                                class="flex h-9 w-9 items-center justify-center rounded-full text-brown transition hover:bg-corange/10"
                                :aria-label="$t('Close Menu')" @click="closeNav">
                                <i class="ri-close-line text-2xl" aria-hidden="true"></i>
                            </button>
                        </div>

                        <ul class="flex-1 space-y-1 overflow-y-auto px-4 py-5">
                            <li v-for="navLink in NavContent" :key="navLink.component">
                                <Link :href="navLink.route"
                                    class="flex items-center rounded-md px-4 py-3.5 text-base font-medium text-brown transition hover:bg-corange/10 hover:text-cOrangeDark"
                                    :class="{
                                        'bg-corange/10 !text-cOrangeDark': $page.component.startsWith(`FrontEnd/${navLink.component}`),
                                    }" @click="closeNav">
                                {{ $t(navLink.label) }}
                                </Link>
                            </li>
                        </ul>
                    </nav>
                </Transition>
            </div>
        </Transition>
    </div>
</template>
