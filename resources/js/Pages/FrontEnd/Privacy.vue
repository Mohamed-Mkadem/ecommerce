<script setup>
import { useLanguageStore } from "@/js/stores/Language";
import info from "./data/privacy";

const languageStore = useLanguageStore();
</script>

<template>
    <Head :title="$t('Privacy Policy')" />

    <main class="min-h-screen bg-lightCream text-brown">
        <header class="border-b border-line bg-offwhite px-5 py-12 text-center sm:px-8 md:py-16">
            <p class="mb-3 text-[10px] font-semibold uppercase tracking-[0.2em] text-cOrangeDark">Sweetia</p>
            <h1 class="font-serif text-4xl leading-tight text-brown sm:text-5xl">{{ $t("Privacy Policy") }}</h1>
        </header>

        <div class="mx-auto max-w-4xl px-5 py-10 sm:px-8 md:py-16">
            <article
                v-for="(value, key) in info[languageStore.currentLocale]"
                :key="key"
                class="mb-5 border border-line bg-white p-5 last:mb-0 sm:p-7 md:p-8"
            >
                <h2 class="mb-4 border-s-2 border-corange ps-4 font-serif text-xl leading-snug text-brown sm:text-2xl">{{ key }}</h2>
                <div class="privacy-content text-sm leading-7 text-brown/75 sm:text-base" v-html="value"></div>
            </article>
        </div>
    </main>
</template>

<style scoped>
.privacy-content :deep(p) { margin-bottom: 0.5rem; }
.privacy-content :deep(ul) { list-style: inside disc; margin: 0.75rem 0; }
.privacy-content :deep(li) { margin-bottom: 0.5rem; }
.privacy-content :deep(li span) { font-weight: 600; color: #50372d; }
</style>
