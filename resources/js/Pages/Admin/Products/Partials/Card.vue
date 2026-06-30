<script setup>
import ProductImagePlaceholder from "@/assets/images/product.webp";
const props = defineProps(["product"]);
</script>

<template>
    <div class="shadow-2 p-5 bg-white rounded-md">
        <img :src="ProductImagePlaceholder" class="rounded-sm mx-auto" />

        <div
            class="text-center mt-4 overflow-x-hidden text-ellipsis whitespace-nowrap"
        >
            <Link
                :href="route('products.show', product)"
                class="text-xl text-primary font-semibold hover:text-meta-5 transition-colors"
                :title="product.name"
            >
                {{ product.name }}
            </Link>
            <p class="text-xl font-semibold text-meta-1">
                {{ product.price }} {{ $t("Product.currency") }}
            </p>
        </div>

        <div class="flex justify-around mt-4">
            <div class="text-center">
                <i class="ri-shopping-cart-line text-2xl text-slate-400"></i>
                <p class="text-primary">{{ product.orders_count }}</p>
            </div>
        </div>
    </div>
</template>
