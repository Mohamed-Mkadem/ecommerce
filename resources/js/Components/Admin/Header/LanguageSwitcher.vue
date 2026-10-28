<script setup>
import { onClickOutside } from "@vueuse/core";
import { reactive, ref, computed } from "vue";
import usFlag from "@/assets/images/flags/us.webp";
import frFlag from "@/assets/images/flags/fr.webp";
import tnFlag from "@/assets/images/flags/tn.webp";
import { useLanguageStore } from "@/js/stores/Language";
const languageStore = useLanguageStore();
const props = defineProps({
    isInFrontEnd: { type: Boolean },
});
const locales = reactive({
    en: { icon: usFlag, label: "English", value: "en" },
    fr: { icon: frFlag, label: "Français", value: "fr" },
    ar: { icon: tnFlag, label: "العربية", value: "ar" },
});



const currentLocaleDisplay = computed(() => {


    return locales[languageStore.currentLocale];
});

async function changeLang(newLang) {
    dropdownOpen.value = false;
    languageStore.changeLocale(newLang);

    if (languageStore.currentLocale == "ar") {
        document.documentElement.setAttribute("dir", "rtl");
    } else {
        document.documentElement.setAttribute("dir", "ltr");
    }
}
const target = ref(null);
const dropdownOpen = ref(false);

onClickOutside(target, () => {
    dropdownOpen.value = false;
});
</script>

<template>
    <div class="relative" ref="target">
        <button class="flex items-center justify-between gap-2 font-normal" :class="{
            'rounded-full border border-slate-200 bg-white px-3 py-2 text-sm text-slate-600 shadow-sm transition duration-200 hover:border-slate-300 hover:bg-slate-50': !isInFrontEnd,
            'text-brown border border-brown hover:border-sky-800 hover:bg-sky-50/10   px-3 py-2 rounded transition-all duration-300 shadow-sm':
                isInFrontEnd,
        }" @click="dropdownOpen = !dropdownOpen">
            <img class="w-5 rounded-sm" :src="currentLocaleDisplay.icon" :alt="currentLocaleDisplay.label" />
            {{ currentLocaleDisplay.label }}
        </button>

        <!-- Dropdown Start -->
        <div v-show="dropdownOpen" :class="{
            'bg-white border-slate-200 shadow-[0_16px_40px_rgba(15,23,42,0.14)] ring-1 ring-black/5': !isInFrontEnd,
            'bg-white border-slate-200 shadow-lg': isInFrontEnd,
        }" class="absolute ltr:right-0 rtl:left-0 z-99 mt-3 w-36 flex-col rounded-xl border shadow-xl">
            <ul class="py-1">
                <li @click.prevent="changeLang(locale.value)" v-for="(locale, index) in locales" :key="locale.value"
                    class="mx-1 flex cursor-pointer items-center justify-between gap-2 rounded-lg px-3 py-2.5 text-sm font-normal" :class="{
                        'text-slate-700 hover:bg-sky-50 hover:text-sky-800 transition-colors': isInFrontEnd,
                        'text-slate-700 transition-colors hover:bg-slate-50 hover:text-primary': !isInFrontEnd,
                    }">
                    <img class="w-5 rounded-sm" :src="locale.icon" :alt="locale.label" />
                    {{ locale.label }}
                </li>
            </ul>
        </div>
        <!-- Dropdown End -->
    </div>
</template>
