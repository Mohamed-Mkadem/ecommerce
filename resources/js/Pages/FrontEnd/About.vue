<script setup>
import traditionImage from "@/assets/images/about-tradition.webp";
import sharingImage from "@/assets/images/about-sharing.webp";
</script>

<template>

    <Head :title="$t('About us')" />

    <main class="bg-lightCream text-brown">
        <section class="mx-auto max-w-screen-3xl px-5 py-12 sm:px-8 md:px-12 md:py-20">
            <div class="mx-auto grid max-w-screen-xl items-center gap-10 lg:grid-cols-2 lg:gap-16">
                <div class="relative mx-auto w-full max-w-[570px] p-3 sm:p-4">
                    <div class="absolute inset-0 translate-x-3 translate-y-3 border border-corange/40 sm:translate-x-4 sm:translate-y-4"
                        aria-hidden="true"></div>
                    <img :src="traditionImage" :alt="$t('about.hero_image_alt')"
                        class="relative aspect-[1.08/1] w-full rounded-sm object-cover" />
                    <div
                        class="absolute bottom-0 end-0 max-w-[200px] border border-line bg-white px-4 py-3 sm:-end-2 sm:-bottom-2">
                        <p class="font-serif text-sm leading-snug text-brown">{{ $t("about.image_note") }}</p>
                    </div>
                </div>

                <div class="max-w-xl py-4 lg:py-8">
                    <p class="mb-4 text-[10px] font-semibold uppercase tracking-[0.2em] text-cOrangeDark">
                        {{ $t("about.eyebrow") }}
                    </p>
                    <h1 class="font-serif text-4xl leading-[1.08] tracking-tight text-brown sm:text-5xl md:text-6xl">
                        {{ $t("about.title_first") }}<br />
                        <span class="text-cOrangeDark italic">{{ $t("about.title_second") }}</span>
                    </h1>
                    <p class="mt-6 text-sm leading-7 text-brown/75 sm:text-base">
                        {{ $t("about.intro") }}
                    </p>
                    <Link :href="route('FE.shop')"
                        class="mt-7 inline-flex min-h-11 items-center gap-3 rounded-sm bg-corange px-5 py-3 text-xs font-semibold text-white transition-colors hover:bg-cOrangeDark focus:outline-none focus-visible:ring-2 focus-visible:ring-cOrangeDark focus-visible:ring-offset-2">
                    {{ $t("about.hero_cta") }}
                    <i class="ri-arrow-right-line rtl:rotate-180" aria-hidden="true"></i>
                    </Link>
                </div>
            </div>
        </section>

        <section class="border-y border-line bg-offwhite px-5 py-12 sm:px-8 md:px-12 md:py-16">
            <div class="mx-auto max-w-screen-xl">
                <div class="mx-auto mb-9 max-w-2xl text-center">
                    <p class="mb-2 text-[10px] font-semibold uppercase tracking-[0.2em] text-cOrangeDark">
                        {{ $t("about.values_eyebrow") }}
                    </p>
                    <h2 class="font-serif text-3xl leading-tight text-brown sm:text-4xl">
                        {{ $t("about.values_title") }}
                    </h2>
                </div>

                <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    <article class="border border-line bg-white p-5 sm:p-6">
                        <span
                            class="mb-4 flex h-10 w-10 items-center justify-center rounded-full border border-corange/30 text-cOrangeDark">
                            <i class="ri-restaurant-2-line text-lg" aria-hidden="true"></i>
                        </span>
                        <h3 class="font-serif text-xl text-brown">{{ $t("about.value_craft_title") }}</h3>
                        <p class="mt-2 text-sm leading-6 text-brown/70">{{ $t("about.value_craft_description") }}</p>
                    </article>

                    <article class="border border-line bg-white p-5 sm:p-6">
                        <span
                            class="mb-4 flex h-10 w-10 items-center justify-center rounded-full border border-corange/30 text-cOrangeDark">
                            <i class="ri-heart-3-line text-lg" aria-hidden="true"></i>
                        </span>
                        <h3 class="font-serif text-xl text-brown">{{ $t("about.value_care_title") }}</h3>
                        <p class="mt-2 text-sm leading-6 text-brown/70">{{ $t("about.value_care_description") }}</p>
                    </article>

                    <article class="border border-line bg-white p-5 sm:p-6 sm:col-span-2 lg:col-span-1">
                        <span
                            class="mb-4 flex h-10 w-10 items-center justify-center rounded-full border border-corange/30 text-cOrangeDark">
                            <i class="ri-gift-2-line text-lg" aria-hidden="true"></i>
                        </span>
                        <h3 class="font-serif text-xl text-brown">{{ $t("about.value_sharing_title") }}</h3>
                        <p class="mt-2 text-sm leading-6 text-brown/70">{{ $t("about.value_sharing_description") }}</p>
                    </article>
                </div>
            </div>
        </section>

        <section class="mx-auto max-w-screen-3xl px-5 py-14 sm:px-8 md:px-12 md:py-20">
            <div class="mx-auto grid max-w-screen-xl items-center gap-9 md:grid-cols-2 md:gap-x-16 md:gap-y-12">
                <div class="overflow-hidden rounded-sm border xmd:min-h-[500px] border-line bg-white p-2 sm:p-3">
                    <img :src="sharingImage" :alt="$t('about.sharing_image_alt')" loading="lazy"
                        class="aspect-[1.2/1] w-full min-h-[500px]  rounded-sm object-cover" />
                </div>
                <div class="max-w-xl md:ms-4">
                    <p class="mb-4 text-[10px] font-semibold uppercase tracking-[0.2em] text-cOrangeDark">
                        {{ $t("about.sharing_eyebrow") }}
                    </p>
                    <h2 class="font-serif text-3xl leading-[1.12] tracking-tight text-brown sm:text-4xl md:text-5xl">
                        {{ $t("about.sharing_title_first") }}<br />
                        {{ $t("about.sharing_title_second") }}
                    </h2>
                    <p class="mt-5 text-sm leading-7 text-brown/75 sm:text-base">
                        {{ $t("about.sharing_description") }}
                    </p>
                    <ul class="mt-5 space-y-3 text-sm text-brown/80">
                        <li class="flex items-start gap-3">
                            <i class="ri-check-line mt-0.5 text-base text-cOrangeDark" aria-hidden="true"></i>
                            <span>{{ $t("about.sharing_point_one") }}</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <i class="ri-check-line mt-0.5 text-base text-cOrangeDark" aria-hidden="true"></i>
                            <span>{{ $t("about.sharing_point_two") }}</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <i class="ri-check-line mt-0.5 text-base text-cOrangeDark" aria-hidden="true"></i>
                            <span>{{ $t("about.sharing_point_three") }}</span>
                        </li>
                    </ul>
                </div>
            </div>
        </section>

        <section class="px-5 pb-14 sm:px-8 md:px-12 md:pb-20">
            <div class="mx-auto max-w-screen-xl rounded-sm bg-[#f3e8da] px-6 py-10 text-center sm:px-10 sm:py-14">
                <p class="mb-3 text-[10px] font-semibold uppercase tracking-[0.2em] text-cOrangeDark">
                    {{ $t("about.closing_eyebrow") }}
                </p>
                <h2 class="font-serif text-3xl leading-tight text-brown sm:text-4xl">
                    {{ $t("about.closing_title") }}
                </h2>
                <p class="mx-auto mt-3 max-w-xl text-sm leading-6 text-brown/70">
                    {{ $t("about.closing_description") }}
                </p>
                <Link :href="route('FE.shop')"
                    class="mt-6 inline-flex min-h-11 items-center gap-3 rounded-sm bg-corange px-5 py-3 text-xs font-semibold text-white transition-colors hover:bg-cOrangeDark focus:outline-none focus-visible:ring-2 focus-visible:ring-cOrangeDark focus-visible:ring-offset-2">
                {{ $t("about.closing_cta") }}
                <i class="ri-arrow-right-line rtl:rotate-180" aria-hidden="true"></i>
                </Link>
            </div>
        </section>
    </main>
</template>
