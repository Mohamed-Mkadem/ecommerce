<script setup>
import heroImage from "@/assets/images/img.JPEG";
// import heroImage from "@/assets/images/hero-bg2-min-min.JPG";
import PrimaryLink from "../PrimaryLink.vue";
</script>

<template>
    <section class="relative w-full h-screen max-w-screen-3xl mx-auto">
        <div
            class="absolute inset-0 bg-cover bg-center bg-no-repeat"
            :style="{ backgroundImage: `url(${heroImage})` }"
        ></div>

        <div
            class="relative z-10 h-full w-full bg-black/70 text-white flex justify-center items-center"
        >
            <div class="px-8 py-5 rounded-md text-center w-full">
                <h1
                    class="text-3xl xmd:text-5xl font-extrabold mb-4"
                    v-html="$t('Showcase.title')"
                ></h1>
                <p class="xmd:text-xl mb-8 text-gray">
                    {{ $t("Showcase.description") }}
                </p>

                <PrimaryLink
                    href="FE.shop"
                    theme="gold-primary"
                    :label="$t('Showcase.cta')"
                />
            </div>
        </div>
    </section>
</template>
