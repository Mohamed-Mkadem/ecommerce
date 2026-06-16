import { computed, ref } from 'vue'
import { defineStore } from 'pinia'
import { loadLanguageAsync, getActiveLanguage } from 'laravel-vue-i18n'
import { router } from '@inertiajs/vue3'
export const useLanguageStore = defineStore('language', () => {
    const currentLocale = ref(sessionStorage.getItem('locale') ?? getActiveLanguage())

    const direction = computed(() => currentLocale.value == 'ar' ? 'rtl' : 'ltr')

    function changeLocale(newLang) {
        router.get(`/lang/${newLang}`, {}, {
            preserveScroll: true,
            preserveState: true
        });
        sessionStorage.setItem("locale", newLang);
        currentLocale.value = newLang
        loadLanguageAsync(newLang)

    }





    return { changeLocale, direction, currentLocale }
})
