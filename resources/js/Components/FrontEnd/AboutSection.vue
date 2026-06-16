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
    class="flex flex-col w-full gap-10 lg:gap-16 items-center"
    :class="{
      'lg:flex-row': !reverse,
      'lg:flex-row-reverse': reverse,
    }"
  >
    <!-- Image -->
    <div class="w-full lg:w-1/2">
      <img
        :src="content.img"
        class="w-full h-full object-cover rounded-2xl shadow-xl"
      />
    </div>

    <!-- Text -->
    <div class="w-full lg:w-1/2 flex flex-col justify-center">
      <div class="w-12 h-1 rounded-full bg-gold mb-5"></div>
      <h2
        class="text-2xl md:text-3xl font-extrabold text-primary mb-5 tracking-tight"
      >
        {{ content[languageStore.currentLocale].title }}
      </h2>
      <div
        class="text-neutral-600 leading-relaxed about-content"
        v-html="content[languageStore.currentLocale].description"
      ></div>
    </div>
  </section>
</template>

<style scoped>
.about-content :deep(p) {
  margin-bottom: 0.875rem;
}
.about-content :deep(b) {
  color: #161e24;
  font-weight: 700;
}
</style>
