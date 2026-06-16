<script setup>
import { ref } from "vue";
import { router } from "@inertiajs/vue3";
import { onClickOutside } from "@vueuse/core";

const target = ref(null);
onClickOutside(target, () => {
    isNavOpen.value = false;
});
const isNavOpen = ref(false);

function toggleNav() {
    isNavOpen.value = !isNavOpen.value;
}
function handleLink(href) {
    router.get(route(`FE.${href}`));
    isNavOpen.value = false;
}
</script>

<template>
    <div class="relative xmd:hidden" ref="target">
        <button
            @click="toggleNav"
            class="p-4 bg-slate-700 text-white w-full flex items-center gap-4 tracking-wider"
        >
            <template v-if="!isNavOpen">
                <i class="ri-menu-line text-xl"></i>
                <span> {{ $t("Menu") }} </span>
            </template>
            <template v-else>
                <i class="ri-menu-unfold-2-fill text-xl"></i>

                <span> {{ $t("Close Menu") }} </span>
            </template>
        </button>
        <ul
            :class="{
                'max-h-0 opacity-0': !isNavOpen,
                'max-h-[500px] opacity-100 z-999': isNavOpen,
            }"
            class="bg-slate-700 border-t border-body absolute start-0 top-[60px] w-full overflow-hidden transition-all duration-300"
        >
            <li>
                <button
                    @click="handleLink('home')"
                    class="text-white tracking-wide px-4 py-3 block w-full text-start"
                >
                    {{ $t("Nav.home") }}
                </button>
            </li>
            <li>
                <button
                    @click="handleLink('shop')"
                    class="text-white tracking-wide px-4 py-3 block w-full text-start"
                >
                    {{ $t("Nav.shop") }}
                </button>
            </li>

            <li>
                <button
                    @click="handleLink('about')"
                    class="text-white tracking-wide px-4 py-3 block w-full text-start"
                >
                    {{ $t("Nav.about") }}
                </button>
            </li>
            <li>
                <button
                    @click="handleLink('contact')"
                    class="text-white tracking-wide px-4 py-3 block w-full text-start"
                >
                    {{ $t("Nav.contact") }}
                </button>
            </li>
        </ul>
    </div>
</template>
