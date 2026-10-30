<script setup>
const props = defineProps(["product"]);
</script>

<template>
    <article class="group overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm transition duration-200 hover:-translate-y-0.5 hover:shadow-md dark:border-neutral-700 dark:bg-neutral-800 dark:shadow-black/30">
        <div class="overflow-hidden bg-slate-50 dark:bg-neutral-900/50">
            <img
                :src="product.main_image_url"
                :alt="product.name"
                class="h-56 w-full object-cover transition duration-300 group-hover:scale-[1.02]"
            />
        </div>

        <div class="p-4">
            <div class="min-w-0 text-center">
                <Link
                    :href="route('products.show', product)"
                    class="block truncate text-lg font-semibold text-slate-800 transition-colors hover:text-primary hover:underline dark:text-neutral-100 dark:hover:text-white"
                    :title="product.name"
                >
                    {{ product.name }}
                </Link>
                <p class="mt-1 text-lg font-bold text-slate-700 dark:text-neutral-200">
                    {{ product.price }} {{ $t("Product.currency") }}
                </p>
            </div>

            <div class="mt-4 flex items-center justify-center gap-2 rounded-xl bg-slate-50 px-3 py-2.5 text-sm dark:bg-neutral-700/60">
                <i class="ri-shopping-cart-line text-base text-slate-500 dark:text-neutral-400" aria-hidden="true"></i>
                <span class="text-slate-500 dark:text-neutral-400">{{ $t("Orders") }}</span>
                <span class="font-semibold text-slate-800 dark:text-neutral-100">{{ product.orders_count }}</span>
            </div>
        </div>
    </article>
</template>
