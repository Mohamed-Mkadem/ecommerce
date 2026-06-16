<script setup>
const props = defineProps(["product"]);
</script>

<template>
    <div class="shadow-2 p-5 bg-white rounded-md">
        <div class="flex items-center justify-between flex-wrap mb-3">
            <p
                class="p-2 text-white text-sm rounded-md font-medium bg-blue-500"
            >
                {{ $t(`Product.${product.type}`) }}
            </p>
            <p
                class="p-2 text-white text-sm rounded-md font-medium"
                :class="{
                    'bg-red-500': product.status == 'hidden',
                    'bg-green-600': product.status == 'published',
                }"
            >
                {{ $t(`Product.${product.status}`) }}
            </p>
        </div>

        <img :src="product.main_image_url" class="rounded-sm mx-auto" />

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
        <div
            class="grid items-center p-2 border-t-2 border-gray mt-3"
            :class="{
                'grid-cols-3': product.type == 'pack',
                'grid-cols-2': product.type == 'product',
            }"
        >
            <div class="text-center">
                <i class="ri-star-line text-2xl text-slate-400"></i>
                <p class="text-primary">{{ product.rate ?? "N/A" }}</p>
            </div>
            <div class="text-center">
                <i class="ri-shopping-cart-line text-2xl text-slate-400"></i>
                <p class="text-primary">{{ product.orders_count }}</p>
            </div>
            <div class="text-center" v-if="product.type == 'pack'">
                <i class="ri-calendar-line text-2xl text-slate-400"></i>
                <p class="text-primary">{{ product.ends_at }}</p>
            </div>
        </div>
    </div>
</template>
