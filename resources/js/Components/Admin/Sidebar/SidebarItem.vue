<script setup>
import { useSidebarStore } from "@/js/stores/sidebar";
import { useLanguageStore } from "@/js/stores/Language";
import { useAppearanceStore } from "@/js/stores/appearance";
import { nextTick, ref } from "vue";

import SidebarDropdown from "./SidebarDropdown.vue";
import { usePage, router } from "@inertiajs/vue3";
const sidebarStore = useSidebarStore();
const languageStore = useLanguageStore();
const appearanceStore = useAppearanceStore();

const props = defineProps(["item", "index", "active", "collapsed"]);
const currentPage = usePage().component;
const flyout = ref(null);
const flyoutOpen = ref(false);
const flyoutStyle = ref({});
let closeFlyoutTimeout;

const openFlyout = async (event) => {
    if (!props.collapsed || !props.item.children) return;

    clearTimeout(closeFlyoutTimeout);
    const bounds = event.currentTarget.getBoundingClientRect();
    flyoutStyle.value = languageStore.direction === "rtl"
        ? { top: `${bounds.top}px`, right: `${window.innerWidth - bounds.left}px` }
        : { top: `${bounds.top}px`, left: `${bounds.right}px` };
    flyoutOpen.value = true;

    await nextTick();
    if (flyout.value) {
        const panelBounds = flyout.value.getBoundingClientRect();
        if (panelBounds.bottom > window.innerHeight - 8) {
            flyoutStyle.value.top = `${Math.max(8, window.innerHeight - panelBounds.height - 8)}px`;
        }
    }
};

const scheduleFlyoutClose = () => {
    closeFlyoutTimeout = setTimeout(() => {
        flyoutOpen.value = false;
    }, 150);
};

const keepFlyoutOpen = () => clearTimeout(closeFlyoutTimeout);

const toggleDropDown = (e) => {
    let dropDown = e.currentTarget.nextElementSibling;
    dropDown.classList.toggle("hidden");
};
</script>

<template>
    <li
        class="group relative"
        @mouseenter="openFlyout"
        @mouseleave="scheduleFlyoutClose"
        @focusin="openFlyout"
        @focusout="scheduleFlyoutClose"
        v-if="
            !item.onlyAdmin ||
            (item.onlyAdmin && $page.props.auth.user.role == 'admin')
        "
    >
        <Link
            v-if="!item.children"
            :href="item.route"
            class="group relative flex items-center gap-2.5 rounded-sm py-2 px-4 font-medium text-bodydark1 duration-300 ease-in-out hover:bg-white/20"
            :title="collapsed ? $t(item.label) : undefined"
            :aria-label="collapsed ? $t(item.label) : undefined"
            :class="[collapsed ? 'lg:justify-center lg:px-0' : '', {
                'bg-white/20 ': $page.component.startsWith(
                    `Admin/${item.component}`,
                ),
            }]"
        >
            <span class="shrink-0 text-lg" v-html="item.icon"></span>

            <span :class="collapsed ? 'lg:hidden' : ''">{{ $t(item.label) }}</span>
            <span v-if="collapsed" class="pointer-events-none absolute top-1/2 z-50 hidden -translate-y-1/2 whitespace-nowrap rounded-md bg-primary px-3 py-2 text-sm text-white shadow-lg lg:block lg:opacity-0 lg:group-hover:opacity-100 lg:group-focus-visible:opacity-100" :class="languageStore.direction === 'rtl' ? 'right-full mr-3' : 'left-full ml-3'">
                {{ $t(item.label) }}
            </span>
        </Link>
        <button
            v-else
            class="group relative flex w-full items-center gap-2.5 rounded-sm py-2 px-4 font-medium text-bodydark1 duration-300 ease-in-out hover:bg-white/20"
            :aria-label="collapsed ? $t(item.label) : undefined"
            :class="[collapsed ? 'lg:justify-center lg:px-0' : '', {
                'bg-white/20 ': $page.component.startsWith(
                    `Admin/${item.component}`,
                ),
            }]"
            @click="toggleDropDown"
        >
            <span class="shrink-0 text-lg" v-html="item.icon"></span>

            <span :class="collapsed ? 'lg:hidden' : ''">{{ $t(item.label) }}</span>

            <svg
                v-if="item.children"
                class="absolute rtl:left-4 ltr:right-4 top-1/2 -translate-y-1/2 fill-current pointer-events-none"
                :class="[
                    collapsed ? 'lg:hidden' : '',
                    { 'rotate-180': sidebarStore.page === item.label },
                ]"
                width="20"
                height="20"
                viewBox="0 0 20 20"
                fill="none"
                xmlns="http://www.w3.org/2000/svg"
            >
                <path
                    fill-rule="evenodd"
                    clip-rule="evenodd"
                    d="M4.41107 6.9107C4.73651 6.58527 5.26414 6.58527 5.58958 6.9107L10.0003 11.3214L14.4111 6.91071C14.7365 6.58527 15.2641 6.58527 15.5896 6.91071C15.915 7.23614 15.915 7.76378 15.5896 8.08922L10.5896 13.0892C10.2641 13.4147 9.73651 13.4147 9.41107 13.0892L4.41107 8.08922C4.08563 7.76378 4.08563 7.23614 4.41107 6.9107Z"
                    fill=""
                />
            </svg>
        </button>
        <!-- Dropdown Menu Start -->
        <div class="translate transform overflow-hidden hidden" :class="collapsed ? 'lg:hidden' : ''">
            <SidebarDropdown
                v-if="item.children"
                :items="item.children"
                :currentPage="currentPage"
                :page="item.label"
            />
            <!-- Dropdown Menu End -->
        </div>

        <Teleport to="body">
            <div
                v-if="item.children && collapsed && flyoutOpen"
                ref="flyout"
                class="fixed z-999999 hidden max-h-[calc(100vh-1rem)] min-w-52 overflow-y-auto rounded-md p-3 shadow-xl lg:block"
                :class="appearanceStore.isDark ? 'dark bg-neutral-700' : 'bg-primary'"
                :style="flyoutStyle"
                @mouseenter="keepFlyoutOpen"
                @mouseleave="scheduleFlyoutClose"
                @focusin="keepFlyoutOpen"
                @focusout="scheduleFlyoutClose"
            >
                <p class="mb-2 px-3 text-xs font-semibold uppercase tracking-wide text-bodydark2">{{ $t(item.label) }}</p>
                <SidebarDropdown
                    :items="item.children"
                    :currentPage="currentPage"
                    :page="item.label"
                />
            </div>
        </Teleport>
    </li>
</template>
