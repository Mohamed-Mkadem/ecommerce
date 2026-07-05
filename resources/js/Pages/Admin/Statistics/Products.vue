<script setup>
import PageHeader from "@/js/Components/Admin/PageHeader.vue";
import { ref } from "vue";
import Card from "./Partials/Card.vue";
import ProductCard from "@/js/Pages/Admin/Products/Partials/Card.vue";
import Paginator from "@/js/Components/Paginator.vue";
import NotFound from "@/js/Components/NotFound.vue";
const props = defineProps({
    statistics: { type: Object },
    bestSelling: { type: Object },
});

const currentStatisticsPeriod = ref("total");
const currentBestSellingPeriod = ref("total");
</script>

<template>
    <Head :title="$t('Products statistics')" />
    <PageHeader :page-title="$t('Products statistics')"> </PageHeader>

    <!-- <div
        class="grid xsm:grid-cols-[repeat(auto-fit,minmax(min(150px,100%),1fr))] gap-2 lg:gap-4"
    >
        <div class="bg-white p-4 rounded-md shadow-5">
            <p class="text-neutral-700 ltr:-tracking-tighter">
                {{ $t("Total") }}
            </p>

            <div class="flex justify-between items-end">
                <p class="font-semibold text-lg text-sky-800">
                    {{ productsStatusCount["total"] }}
                </p>
                <div
                    class="text-white flex items-center justify-center w-9 h-9 rounded-md bg-sky-700"
                >
                    <i class="ri-cake-3-line"></i>
                </div>
            </div>
        </div>

        <div class="bg-white p-4 rounded-md shadow-5">
            <p class="text-neutral-700 ltr:-tracking-tighter">
                {{ $t("Product.published") }}
            </p>

            <div class="flex justify-between items-end">
                <p class="font-semibold text-lg text-sky-800">
                    {{ productsStatusCount["published"] }}
                </p>
                <div
                    class="text-white flex items-center justify-center w-9 h-9 rounded-md bg-green-600"
                >
                    <i class="ri-check-double-line"></i>
                </div>
            </div>
        </div>
        <div class="bg-white p-4 rounded-md shadow-5">
            <p class="text-neutral-700 ltr:-tracking-tighter">
                {{ $t("Product.hidden") }}
            </p>

            <div class="flex justify-between items-end">
                <p class="font-semibold text-lg text-sky-800">
                    {{ productsStatusCount["hidden"] }}
                </p>
                <div
                    class="text-white flex items-center justify-center w-9 h-9 rounded-md bg-red-600"
                >
                    <i class="ri-eye-off-line"></i>
                </div>
            </div>
        </div>
    </div> -->

    <section class="my-12">
        <div
            class="bg-white rounded-lg p-4 shadow-1 md:flex md:justify-between md:items-center md:gap-3 md:flex-wrap mb-4"
        >
            <h2 class="text-sky-800 font-semibold text-lg">
                {{ $t("Top 10 Best Selling Products") }}
            </h2>

            <select
                v-model="currentBestSellingPeriod"
                class="w-full md:w-max min-w-20 mt-4 md:mt-0 rounded-md focus:ring-primary focus:border-primary border-editor text-primary"
            >
                <option value="total">{{ $t("Total") }}</option>
                <option value="day">{{ $t("Today") }}</option>
                <option value="week">{{ $t("This week") }}</option>
                <option value="month">{{ $t("This month") }}</option>
                <option value="year">{{ $t("This year") }}</option>
            </select>
        </div>

        <div v-if="bestSelling[currentBestSellingPeriod].length">
            <div
                class="grid grid-cols-[repeat(auto-fit,_minmax(min(400px,_100%),_1fr))] gap-4"
            >
                <div
                    class="shadow-2 p-5 bg-white rounded-md"
                    v-for="product in bestSelling[currentBestSellingPeriod]"
                    :key="product.id"
                >
                    <div class="flex items-center flex-wrap mb-3">
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

                    <img
                        :src="product.main_image_url"
                        class="rounded-sm mx-auto"
                    />

                    <div class="text-center mt-4">
                        <Link
                            :href="route('products.show', product)"
                            class="text-xl text-primary font-semibold hover:text-meta-5 transition-colors line-clamp"
                            :title="product.name"
                        >
                            {{ product.name }}
                        </Link>
                        <p class="text-xl font-semibold text-meta-1">
                            {{ product.price }} {{ $t("Product.currency") }}
                        </p>
                    </div>
                    <div
                        class="grid items-center p-2 border-t-2 border-gray mt-3 grid-cols-2"
                    >
                        <div class="text-center">
                            <i class="ri-star-line text-2xl text-slate-400"></i>
                            <p class="text-primary">
                                {{ product.rate ?? "N/A" }}
                            </p>
                        </div>
                        <div class="text-center">
                            <i
                                class="ri-shopping-cart-line text-2xl text-slate-400"
                            ></i>
                            <p class="text-primary">
                                {{ product.delivered_orders_count }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="my-12">
        <div
            class="bg-white rounded-lg p-4 shadow-1 md:flex md:justify-between md:items-center md:gap-3 md:flex-wrap mb-4"
        >
            <h2 class="text-sky-800 font-semibold text-lg">
                {{ $t("Products By Orders Statuses") }}
            </h2>

            <select
                v-model="currentStatisticsPeriod"
                class="w-full md:w-max min-w-20 mt-4 md:mt-0 rounded-md focus:ring-primary focus:border-primary border-editor text-primary"
            >
                <option value="total">{{ $t("Total") }}</option>
                <option value="day">{{ $t("Today") }}</option>
                <option value="week">{{ $t("This week") }}</option>
                <option value="month">{{ $t("This month") }}</option>
                <option value="year">{{ $t("This year") }}</option>
            </select>
        </div>

        <div v-if="statistics.data.length">
            <div
                class="grid grid-cols-[repeat(auto-fit,_minmax(min(400px,_100%),_1fr))] gap-4"
            >
                <Card
                    v-for="product in props.statistics.data"
                    :key="product.id"
                    :product="product"
                    :period="currentStatisticsPeriod"
                />
            </div>
            <Paginator
                :links="statistics.meta.links"
                :previous="statistics.links.prev"
                :next="statistics.links.next"
                class="mt-4"
            />
        </div>
        <div v-else>
            <NotFound message="NotFound.products" />
        </div>
    </section>
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
