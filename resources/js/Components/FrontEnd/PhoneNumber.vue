<script setup>
import { ref } from "vue";
import { useLanguageStore } from "@/js/stores/Language";
const isHidden = ref(false);
const languageStore = useLanguageStore();

const messages = {
    en: "Need help? Call us!",
    fr: "Besoin d'aide ? Appelez-nous !",
    ar: "هل تحتاج مساعدة؟ اتصل بنا!",
};
const closeBanner = () => {
    isHidden.value = true;
};
</script>

<template>
    <div
        class="fixed bottom-0 left-0 w-full min-h-12 bg-meta-5 text-white p-4 font-semibold flex justify-between items-center xmd:hidden z-99"
        :class="{
            hidden: isHidden,
        }"
    >
        <div>
            <p>
                {{ messages[languageStore.currentLocale] }}
            </p>
            <a href="tel:29144980">
                <i class="ri-customer-service-line me-3 font-normal"></i>
                <span dir="ltr">29 144 980</span>
            </a>
        </div>
        <button
            @click="closeBanner"
            class="text-white bg-transparent text-xl px-2 hover:text-red-500"
        >
            ✖
        </button>
    </div>
</template>
