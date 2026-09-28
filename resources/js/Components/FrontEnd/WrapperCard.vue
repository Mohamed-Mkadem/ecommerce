<script setup>
defineProps({
    wrapper: {
        type: Object,
        required: true,
    },
});
</script>

<template>
    <article class="group min-w-0">
        <Link
            :href="route('FE.wrapper', wrapper.slug)"
            class="relative block aspect-square overflow-hidden bg-[#f3eee8]"
            :aria-label="`${$t('bestsellers.explore')} ${wrapper.title}`"
        >
            <img
                :src="wrapper.main_image_url"
                :alt="wrapper.title"
                loading="lazy"
                class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-[1.03]"
            />
        </Link>

        <div class="pt-3">
            <Link
                :href="route('FE.wrapper', wrapper.slug)"
                class="block truncate font-serif text-lg leading-tight text-brown transition-colors group-hover:text-cOrangeDark sm:text-xl"
                :title="wrapper.title"
            >
                {{ wrapper.title }}
            </Link>

            <div class="mt-2 flex flex-wrap items-center gap-x-2 text-sm">
                <span class="font-semibold text-cOrangeDark">
                    {{ wrapper.price }} {{ $t("Product.currency") }}
                </span>
            </div>

            <Link
                :href="route('FE.wrapper', wrapper.slug)"
                class="mt-2 inline-flex items-center gap-2 text-xs font-semibold text-cOrangeDark transition-colors hover:text-brown"
            >
                {{ $t("bestsellers.explore") }}
                <i class="ri-arrow-right-line rtl:rotate-180" aria-hidden="true"></i>
            </Link>
        </div>
    </article>
</template>
