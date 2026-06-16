<script setup>
import ProductCard from "./ProductCard.vue";
import SectionTitle from "./SectionTitle.vue";
const props = defineProps({
    products: {
        type: Array,
        required: true,
    },
    title: {
        type: String,
    },
    ctaLabel: {
        type: String,
    },
    ctaHref: {
        type: String,
    },
});
</script>

<template>
    <section class="container-2xl py-12" v-if="products.length">
        <SectionTitle :text="title" color="blue" />

        <div
            class="grid md:grid-cols-2 lg:grid-cols-4 md:items-stretch gap-4 xmd:gap-6 mt-12 px-6"
        >
            <ProductCard
                v-for="(product, index) in products"
                :product="product"
                :key="index"
            />
        </div>

        <Link
            v-if="ctaHref && ctaLabel"
            :href="route(ctaHref)"
            class="border-2 border-slate-700 rounded bg-transparent mx-auto mt-12 px-4 py-3 block min-w-[200px] max-w-max text-center ltr:tracking-[3px] hover:bg-gold transition-colors"
        >
            {{ ctaLabel }}
        </Link>
    </section>
</template>
