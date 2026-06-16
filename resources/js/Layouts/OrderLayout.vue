<script setup>
import OrderLayoutHeader from "@/js/Components/Admin/Header/OrderLayoutHeader.vue";
import { useLanguageStore } from "@/js/stores/Language";
import { onMounted, ref } from "vue";
import { router } from "@inertiajs/vue3";
const languageStore = useLanguageStore();
import Loader from "@/js/Components/Loader.vue";
const processing = ref(false);

router.on("start", () => (processing.value = true));
router.on("finish", () => (processing.value = false));
onMounted(() => {
    document.documentElement.setAttribute("dir", languageStore.direction);

    document.body.setAttribute(
        "class",
        "relative z-1 bg-whiten  text-base font-normal text-body ",
    );
});
</script>

<template>
    <div class="container-3xl relative">
        <OrderLayoutHeader />
        <main class="px-4 pt-8 lg:pt-12 pb-100">
            <slot></slot>
        </main>
    </div>
    <Loader v-if="processing" />
</template>
