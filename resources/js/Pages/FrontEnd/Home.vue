<script setup>
import WrapperCard from "@/js/Components/FrontEnd/WrapperCard.vue";
import HeroShowcase from "@/js/Components/FrontEnd/HeroShowcase.vue";
import heroImage from "@/assets/images/hero.webp";

const props = defineProps({
    wrappers: Array,
});

</script>

<template>

    <Head :title="`${$t('Nav.home')} `"></Head>

    <HeroShowcase />



    <section id="products" class="mx-auto max-w-screen-3xl bg-lightCream px-5 py-16 sm:px-8 md:px-12 md:py-20">
        <div class="mx-auto max-w-screen-xl">
            <div class="mb-8 flex flex-col gap-5 sm:mb-10 sm:flex-row sm:items-end sm:justify-between">
                <div class="max-w-lg">
                    <p class="mb-2 text-[10px] font-semibold uppercase tracking-[0.2em] text-cOrangeDark">
                        {{ $t("bestsellers.eyebrow") }}
                    </p>
                    <h2 class="font-serif text-3xl leading-tight tracking-tight text-brown sm:text-4xl">
                        {{ $t("bestsellers.title") }}
                    </h2>
                    <p class="mt-3 max-w-md text-xs leading-5 text-brown/75 sm:text-sm sm:leading-6">
                        {{ $t("bestsellers.description") }}
                    </p>
                </div>

                <Link :href="route('FE.shop')"
                    class="inline-flex w-fit items-center gap-2 border-b border-corange/50 pb-1 text-xs font-semibold text-cOrangeDark transition-colors hover:text-brown">
                {{ $t("bestsellers.view_all") }}
                <i class="ri-arrow-right-line rtl:rotate-180" aria-hidden="true"></i>
                </Link>
            </div>

            <div
                class="grid min-[600px]:grid-cols-2 gap-x-4 gap-y-8 sm:gap-x-6 sm:gap-y-10 lg:grid-cols-4 lg:gap-x-5 xl:gap-x-6">
                <WrapperCard v-for="wrapper in props.wrappers" :key="wrapper.id" :wrapper="wrapper" />
            </div>

            <div class="mt-10 flex justify-center sm:mt-12">
                <Link :href="route('FE.shop')"
                    class="inline-flex min-h-11 items-center gap-4 rounded-sm bg-corange px-5 py-3 text-xs font-semibold text-white transition hover:bg-cOrangeDark focus:outline-none focus-visible:ring-2 focus-visible:ring-cOrangeDark focus-visible:ring-offset-2">
                {{ $t("bestsellers.explore_all") }}
                <i class="ri-arrow-right-line rtl:rotate-180" aria-hidden="true"></i>
                </Link>
            </div>
        </div>
    </section>

    <section class="mx-auto max-w-screen-3xl bg-offwhite px-5 py-16 sm:px-8 md:px-12 md:py-24">
        <div class="mx-auto grid max-w-screen-xl items-center gap-10 slg:grid-cols-2 lg:gap-16">
            <div class="relative mx-auto w-full max-w-[560px] p-3 sm:p-4 ">
                <div class="absolute slg:block hidden rounded-lg inset-0 translate-x-3 translate-y-3 border border-corange/40 sm:translate-x-4 sm:translate-y-4"
                    aria-hidden="true"></div>
                <img :src="heroImage" :alt="$t('home_heritage.image_alt')" loading="lazy"
                    class="relative aspect-[1.12/1] rounded-lg  w-full object-cover" />
            </div>

            <div class="max-w-xl py-3 lg:py-8 ">
                <p class="mb-4 text-[10px] font-semibold uppercase tracking-[0.2em] text-cOrangeDark">
                    {{ $t("home_heritage.eyebrow") }}
                </p>
                <h2 class="font-serif text-3xl leading-[1.1] tracking-tight text-brown sm:text-4xl md:text-5xl">
                    {{ $t("home_heritage.title_first") }}<br />
                    {{ $t("home_heritage.title_second") }}
                </h2>
                <p class="mt-5 max-w-lg text-sm leading-7 text-brown/80">
                    {{ $t("home_heritage.description") }}
                </p>

                <ul class="mt-5 space-y-3 text-xs leading-5 text-brown/85 sm:text-sm">
                    <li class="flex items-start gap-3">
                        <i class="ri-check-line mt-0.5 text-base text-cOrangeDark" aria-hidden="true"></i>
                        <span>{{ $t("home_heritage.point_one") }}</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <i class="ri-check-line mt-0.5 text-base text-cOrangeDark" aria-hidden="true"></i>
                        <span>{{ $t("home_heritage.point_two") }}</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <i class="ri-check-line mt-0.5 text-base text-cOrangeDark" aria-hidden="true"></i>
                        <span>{{ $t("home_heritage.point_three") }}</span>
                    </li>
                </ul>

                <Link :href="route('FE.about')"
                    class="mt-7 inline-flex items-center gap-3 border-b border-corange/50 pb-2 text-xs font-semibold text-cOrangeDark transition-colors hover:text-brown">
                {{ $t("home_heritage.cta") }}
                <i class="ri-arrow-right-line rtl:rotate-180" aria-hidden="true"></i>
                </Link>
            </div>
        </div>
    </section>

    <section class="mx-auto max-w-screen-3xl bg-offwhite px-5 pb-16 sm:px-8 md:px-12 md:pb-24">
        <div class="rounded-lg mx-auto grid max-w-screen-xl overflow-hidden bg-[#f3e8da] md:grid-cols-2">
            <div class="min-h-[260px] sm:min-h-[360px] lg:min-h-[460px]">
                <img :src="heroImage" :alt="$t('home_gathering.image_alt')" loading="lazy"
                    class="h-full min-h-[260px] w-full object-cover sm:min-h-[360px] lg:min-h-[460px]" />
            </div>

            <div class="flex flex-col justify-center px-7 py-10 sm:px-12 sm:py-14 lg:px-16">
                <p class="mb-4 text-[10px] font-semibold uppercase tracking-[0.2em] text-cOrangeDark">
                    {{ $t("home_gathering.eyebrow") }}
                </p>
                <h2
                    class="max-w-lg font-serif text-3xl leading-[1.12] tracking-tight text-brown sm:text-4xl md:text-5xl">
                    {{ $t("home_gathering.title_first") }}<br />
                    {{ $t("home_gathering.title_second") }}
                </h2>
                <p class="mt-5 max-w-lg text-sm leading-7 text-brown/80">
                    {{ $t("home_gathering.description") }}
                </p>
                <Link :href="route('FE.shop')"
                    class="mt-7 inline-flex min-h-11 w-fit items-center gap-4 rounded-sm bg-corange px-5 py-3 text-xs font-semibold text-white transition hover:bg-cOrangeDark focus:outline-none focus-visible:ring-2 focus-visible:ring-cOrangeDark focus-visible:ring-offset-2">
                {{ $t("home_gathering.cta") }}
                <i class="ri-arrow-right-line rtl:rotate-180" aria-hidden="true"></i>
                </Link>
            </div>
        </div>
    </section>
</template>
