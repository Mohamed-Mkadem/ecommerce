<script setup>
import { useLanguageStore } from "@/js/stores/Language";
import info from "./data/privacy";

const languageStore = useLanguageStore();
</script>

<template>
    <Head :title="$t('Privacy Policy')" />

    <!-- Page banner -->
    <div
        class="bg-gradient-to-br from-sky-900 via-sky-800 to-primary text-white py-20 px-6 text-center max-w-screen-3xl mx-auto"
    >
        <h1 class="text-4xl font-extrabold tracking-tight">
            {{ $t("Privacy Policy") }}
        </h1>
    </div>

    <!-- Content -->
    <div class="py-16 px-4 md:px-8 max-w-3xl mx-auto">
        <div
            v-for="(value, key) in info[languageStore.currentLocale]"
            :key="key"
            class="mb-10 pb-10 border-b border-neutral-100 last:border-0 last:pb-0 last:mb-0"
        >
            <h2
                class="text-lg font-bold text-primary border-s-4 border-gold ps-4 mb-4 leading-snug"
            >
                {{ key }}
            </h2>
            <div
                class="text-neutral-600 leading-relaxed privacy-content"
                v-html="value"
            ></div>
        </div>
    </div>
</template>

<style scoped>
.privacy-content :deep(p) {
    margin-bottom: 0.5rem;
}
.privacy-content :deep(ul) {
    list-style: inside disc;
    margin-top: 0.5rem;
    margin-bottom: 0.5rem;
}
.privacy-content :deep(li) {
    margin-bottom: 0.375rem;
}
.privacy-content :deep(li span) {
    font-weight: 600;
    color: #161e24;
}
</style>
