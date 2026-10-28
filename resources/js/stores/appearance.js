import { useStorage } from "@vueuse/core";
import { defineStore } from "pinia";

export const useAppearanceStore = defineStore("appearance", () => {
    const isDark = useStorage("admin-dark-mode", false);

    function toggleDarkMode() {
        isDark.value = !isDark.value;
    }

    return { isDark, toggleDarkMode };
});
