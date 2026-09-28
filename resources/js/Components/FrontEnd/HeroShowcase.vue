<script setup>
import heroImage from "@/assets/images/hero.webp";
</script>

<template>
    <section class="mx-auto w-full max-w-screen-3xl bg-offwhite text-brown">
        <div
            class="mx-auto grid max-w-screen-xl items-center justify-center gap-12 px-5 py-14 sm:px-8 sm:py-16 lg:min-h-[650px] lg:grid-cols-2 lg:gap-16 lg:px-10 lg:py-20">
            <div class="max-w-xl text-center lg:text-start">
                <p
                    class="mb-5 flex items-center justify-center gap-3 text-xs before:w-6 before:h-[1px] before:bg-cOrangeDark uppercase tracking-[0.2em] text-cOrangeDark lg:justify-start">
                    {{ $t("showcase.pretext") }}
                </p>

                <h1
                    class="font-serif text-4xl leading-[1.08] tracking-[-0.045em]  sm:text-5xl md:text-6xl lg:text-[4.5rem]">
                    {{ $t("showcase.title_start") }}
                    <span class=" text-corange italic">{{ $t("showcase.title_highlight") }}</span>
                </h1>

                <p class="mx-auto mt-6 max-w-md text-sm leading-7 text-brown/80 sm:text-base lg:mx-0">
                    {{ $t("showcase.description") }}
                </p>

                <div class="mt-7 flex justify-center lg:justify-start">
                    <Link :href="route('FE.shop')"
                        class="inline-flex min-h-12 items-center gap-4 rounded-sm bg-corange px-5 py-3 text-xs font-semibold text-white transition hover:bg-cOrangeDark focus:outline-none focus-visible:ring-2 focus-visible:ring-cOrangeDark focus-visible:ring-offset-2">
                    {{ $t("showcase.cta") }}
                    <i class="ri-arrow-right-line text-base rtl:rotate-180" aria-hidden="true"></i>
                    </Link>
                </div>

                <div class="mt-7 flex items-center justify-center gap-3 text-[11px] text-cOrangeDark lg:justify-start">
                    <span class="flex h-7 w-7 items-center justify-center rounded-full border border-corange/40"
                        aria-hidden="true">
                        <i class="ri-heart-line text-sm"></i>
                    </span>
                    <span>{{ $t("showcase.note") }}</span>
                </div>
            </div>

            <div class="relative mx-auto w-full max-w-[610px] px-3 pb-3 sm:px-5 sm:pb-5">
                <div class="absolute inset-0 rounded-t-full border border-corange/40" aria-hidden="true"></div>
                <div class="relative mx-3 mt-3 aspect-[4/3.7] overflow-hidden rounded-t-full sm:mx-4 sm:mt-4">
                    <img :src="heroImage" :alt="$t('showcase.image_alt')" class="h-full w-full object-cover" />
                </div>

                <div
                    class="relative z-10 -mt-16 ms-auto w-[min(100%,255px)] border border-line bg-white px-4 py-3 shadow-sm sm:-me-3 sm:-mt-24 sm:w-[min(100%,280px)] sm:px-5 sm:py-4">
                    <p class="text-[9px] font-semibold uppercase tracking-[0.16em] text-cOrangeDark">
                        {{ $t("showcase.card_eyebrow") }}
                    </p>
                    <p class="mt-1 font-serif text-base leading-tight text-brown sm:text-lg">
                        {{ $t("showcase.card_title") }}
                    </p>
                    <p class="mt-1.5 text-[10px] leading-4 text-brown/75">
                        {{ $t("showcase.card_description") }}
                    </p>
                </div>
            </div>
        </div>

        <div class="border-y border-line bg-[#f8f1e9] px-5 py-5 sm:py-6">
            <div
                class="mx-auto flex max-w-3xl flex-col items-center justify-center gap-4 text-center sm:flex-row sm:gap-10 lg:gap-16">
                <div class="flex items-center gap-2.5 text-[9px] font-semibold uppercase tracking-[0.1em] text-brown">
                    <i class="ri-truck-line text-base text-corange" aria-hidden="true"></i>
                    {{ $t("showcase.feature_delivery") }}
                </div>
                <div class="flex items-center gap-2.5 text-[9px] font-semibold uppercase tracking-[0.1em] text-brown">
                    <i class="ri-shield-check-line text-base text-corange" aria-hidden="true"></i>
                    {{ $t("showcase.feature_care") }}
                </div>
                <div class="flex items-center gap-2.5 text-[9px] font-semibold uppercase tracking-[0.1em] text-brown">
                    <i class="ri-home-heart-line text-base text-corange" aria-hidden="true"></i>
                    {{ $t("showcase.feature_tradition") }}
                </div>
            </div>
        </div>
    </section>
</template>
