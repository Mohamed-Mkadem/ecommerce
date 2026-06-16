<script setup>
import { useLanguageStore } from "@/js/stores/Language";
const languageStore = useLanguageStore();
const props = defineProps({
    content: { type: Object, required: true },
    reverse: { type: Boolean, default: false },
});
</script>

<template>
    <section
        class="flex flex-col w-full container-3xl md:max-h-[400px]"
        :class="{
            'md:flex-row': !reverse,
            'md:flex-row-reverse': reverse,
        }"
    >
        <div class="w-full md:w-1/2">
            <img :src="content.img" class="w-full h-full object-cover" />
        </div>

        <div
            class="w-full md:w-1/2 flex flex-col justify-center items-center p-6 md:p-12 bg-gray-100 bg-zinc-100"
        >
            <h2
                class="text-2xl md:text-3xl text-center font-semibold mb-4 text-sky-900"
            >
                {{ content[languageStore.currentLocale].title }}
            </h2>
            <p class="text-lg text-center leading-relaxed text-meta-4">
                {{ content[languageStore.currentLocale].description }}
            </p>
        </div>
    </section>
</template>
