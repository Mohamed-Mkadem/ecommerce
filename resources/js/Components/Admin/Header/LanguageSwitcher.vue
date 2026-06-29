<script setup>
import { onClickOutside } from "@vueuse/core";
import { reactive, ref, computed } from "vue";
import usFlag from "@/assets/images/flags/us.svg";
import frFlag from "@/assets/images/flags/fr.svg";
import tnFlag from "@/assets/images/flags/tn.svg";
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
   

    return locales[languageStore.currentLocale] ;
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
        <button
            class="flex items-center justify-between gap-2"
            :class="{
                'bg-white border-stroke ': !isInFrontEnd,
                'text-slate-700 border border-slate-200 hover:border-sky-300 hover:bg-sky-50 hover:text-sky-800 px-3 py-2 rounded transition-all duration-300 shadow-sm':
                    isInFrontEnd,
            }"
            @click="dropdownOpen = !dropdownOpen"
        >
            <img
                class="w-5 rounded-sm"
                :src="currentLocaleDisplay.icon"
                :alt="currentLocaleDisplay.label"
            />
            {{ currentLocaleDisplay.label }}
        </button>

        <!-- Dropdown Start -->
        <div
            v-show="dropdownOpen"
            :class="{
                'bg-white border-stroke ': !isInFrontEnd,
                'bg-white border-slate-200 shadow-lg': isInFrontEnd,
            }"
            class="absolute ltr:right-0 rtl:left-0 mt-2 w-33 flex-col rounded-sm border shadow-default z-99"
        >
            <ul class="  ">
                <li
                    @click.prevent="changeLang(locale.value)"
                    v-for="(locale, index) in locales"
                    :key="locale.value"
                    class="p-2 flex items-center justify-between gap-2 cursor-pointer"
                    :class="{
                        'text-slate-700 hover:bg-sky-50 hover:text-sky-800 transition-colors': isInFrontEnd,
                        'text-primary hover:bg-slate-200': !isInFrontEnd,
                    }"
                >
                    <img
                        class="w-5 rounded-sm"
                        :src="locale.icon"
                        :alt="locale.label"
                    />
                    {{ locale.label }}
                </li>
            </ul>
        </div>
        <!-- Dropdown End -->
    </div>
</template>
