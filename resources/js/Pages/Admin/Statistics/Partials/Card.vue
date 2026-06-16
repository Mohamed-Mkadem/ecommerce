<script setup>
const props = defineProps(["product", "period"]);
</script>

<template>
    <div class="shadow-2 p-5 bg-white rounded-md">
        <img :src="product.main_image_url" class="rounded-sm mx-auto" />

        <div class="text-center mt-4">
            <Link
                :href="route('products.show', product)"
                class="text-xl text-primary font-semibold hover:text-meta-5 transition-colors text-ellipsis overflow-hidden line-clamp"
                :title="product.name"
            >
                {{ product.name }}
            </Link>
        </div>
        <div class="p-2 border-t-2 border-gray mt-3">
            <div class="text-center text-white bg-sky-800 p-3 rounded-md mb-2">
                <p class="font-semibold">
                    {{ product.counts[period].orders_count }}
                </p>

                <div class="flex items-center gap-2 justify-center">
                    <i class="ri-shopping-cart-fill text-xl"></i>

                    <p>{{ $t("Total") }}</p>
                </div>
            </div>
            <div
                class="grid items-center grid-cols-[repeat(auto-fit,_minmax(min(200px,_100%),_1fr))] gap-2"
            >
                <div
                    class="text-center bg-yellow-200 text-orange-800 p-3 rounded-md"
                >
                    <p class="font-semibold">
                        {{ product.counts[period].pending_orders_count }}
                    </p>

                    <div class="flex items-center gap-2 justify-center">
                        <i class="ri-hourglass-fill text-xl"></i>

                        <p>{{ $t("pending") }}</p>
                    </div>
                </div>
                <div class="text-center bg-red-200 text-red-800 p-3 rounded-md">
                    <p class="font-semibold">
                        {{ product.counts[period].canceled_orders_count }}
                    </p>

                    <div class="flex items-center gap-2 justify-center">
                        <i class="ri-close-circle-fill text-xl"></i>

                        <p>{{ $t("canceled") }}</p>
                    </div>
                </div>

                <div
                    class="text-center bg-green-200 text-green-800 p-3 rounded-md"
                >
                    <p class="font-semibold">
                        {{ product.counts[period].confirmed_orders_count }}
                    </p>

                    <div class="flex items-center gap-2 justify-center">
                        <i class="ri-checkbox-fill text-xl"></i>

                        <p>{{ $t("confirmed") }}</p>
                    </div>
                </div>
                <div
                    class="text-center bg-purple-200 text-purple-800 p-3 rounded-md"
                >
                    <p class="font-semibold">
                        {{ product.counts[period].shipped_orders_count }}
                    </p>

                    <div class="flex items-center gap-2 justify-center">
                        <i class="ri-truck-fill text-xl"></i>

                        <p>{{ $t("shipped") }}</p>
                    </div>
                </div>
                <div
                    class="text-center bg-blue-200 text-blue-800 p-3 rounded-md"
                >
                    <p class="font-semibold">
                        {{ product.counts[period].delivered_orders_count }}
                    </p>

                    <div class="flex items-center gap-2 justify-center">
                        <i class="ri-home-9-fill text-xl"></i>

                        <p>{{ $t("delivered") }}</p>
                    </div>
                </div>
                <div
                    class="text-center bg-zinc-200 text-zinc-700 p-3 rounded-md"
                >
                    <p class="font-semibold">
                        {{ product.counts[period].returned_orders_count }}
                    </p>

                    <div class="flex items-center gap-2 justify-center">
                        <i class="ri-truck-fill text-xl"></i>

                        <p>{{ $t("returned") }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.line-clamp {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    text-overflow: ellipsis;
}
</style>
