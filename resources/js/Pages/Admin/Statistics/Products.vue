<script setup>
import PageHeader from "@/js/Components/Admin/PageHeader.vue";
import SectionTip from "@/js/Components/Admin/SectionTip.vue";
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

    <section class="my-12">
        <SectionTip message="Statistics.bestSellingProductsTip" />
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
            <div class="overflow-x-auto rounded-lg shadow-1">
                <table class="min-w-full bg-white text-slate-800">
                    <thead class="bg-sky-800 text-white">
                        <tr>
                            <th scope="col" class="px-4 py-3 text-start">
                                {{ $t("Product.product") }}
                            </th>
                            <th scope="col" class="px-4 py-3 text-center">
                                {{ $t("Product.wrapper") }}
                            </th>
                            <th scope="col" class="px-4 py-3 text-center">
                                {{ $t("Product.price") }}
                            </th>
                            <th scope="col" class="px-4 py-3 text-center">
                                {{ $t("Delivered Quantity") }}
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            v-for="product in bestSelling[
                                currentBestSellingPeriod
                            ]"
                            :key="product.id"
                            class="border-t border-slate-200 odd:bg-white even:bg-slate-50"
                        >
                            <td class="px-4 py-3">
                                <div class="flex min-w-64 items-center gap-3">
                                    <img
                                        :src="product.main_image_url"
                                        :alt="product.name"
                                        class="h-14 w-14 shrink-0 rounded object-cover"
                                    />
                                    <Link
                                        :href="route('products.show', product)"
                                        class="font-semibold text-primary transition-colors hover:text-meta-5"
                                        :title="product.name"
                                    >
                                        {{ product.name }}
                                    </Link>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-center">
                                {{ product.wrapper_title || "—" }}
                            </td>
                            <td
                                class="whitespace-nowrap px-4 py-3 text-center font-medium"
                            >
                                {{ product.price }} {{ $t("Product.currency") }}
                            </td>
                            <td class="px-4 py-3 text-center">
                                <span
                                    class="inline-flex items-center justify-center gap-1"
                                >
                                    <i
                                        class="ri-shopping-cart-line text-slate-400"
                                        aria-hidden="true"
                                    ></i>
                                    {{ product.delivered_orders_count }}
                                </span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    <section class="my-12">
        <SectionTip message="Statistics.productStatusBreakdownTip" />
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
