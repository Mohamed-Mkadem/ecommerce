<script setup>
import PageHeader from "@/js/Components/Admin/PageHeader.vue";
import ResultsCounter from "@/js/Components/Admin/ResultsCounter.vue";
import SectionTip from "@/js/Components/Admin/SectionTip.vue";
import SelectInput from "@/js/Components/SelectInput.vue";
import Paginator from "@/js/Components/Paginator.vue";
import NotFound from "@/js/Components/NotFound.vue";
import { useAppearanceStore } from "@/js/stores/appearance";
import { computed, ref } from "vue";
import { Bar } from "vue-chartjs";
import {
    BarElement,
    CategoryScale,
    Chart as ChartJS,
    LinearScale,
    Tooltip,
} from "chart.js";
import { trans } from "laravel-vue-i18n";
import Card from "./Partials/Card.vue";

ChartJS.register(CategoryScale, LinearScale, BarElement, Tooltip);

const props = defineProps({
    statistics: { type: Object },
    bestSelling: { type: Object },
});

const appearanceStore = useAppearanceStore();
const currentStatisticsPeriod = ref("total");
const currentBestSellingPeriod = ref("total");

const bestSellingRows = computed(
    () => props.bestSelling?.[currentBestSellingPeriod.value] ?? [],
);
const productRows = computed(() => props.statistics?.data ?? []);

const bestSellingChartData = computed(() => ({
    labels: bestSellingRows.value.map((product) => product.name),
    datasets: [
        {
            label: trans("Delivered Quantity"),
            data: bestSellingRows.value.map(
                (product) => Number(product.delivered_orders_count) || 0,
            ),
            backgroundColor: "#0f766e",
            hoverBackgroundColor: "#115e59",
            borderRadius: 7,
            barThickness: 18,
        },
    ],
}));

const bestSellingChartOptions = computed(() => ({
    indexAxis: "y",
    responsive: true,
    maintainAspectRatio: false,
    scales: {
        x: {
            beginAtZero: true,
            grid: {
                color: appearanceStore.isDark ? "#404040" : "#e2e8f0",
            },
            ticks: {
                color: appearanceStore.isDark ? "#a3a3a3" : "#64748b",
                precision: 0,
            },
        },
        y: {
            grid: { display: false },
            ticks: {
                color: appearanceStore.isDark ? "#d4d4d4" : "#475569",
                callback(value) {
                    const label = this.getLabelForValue(value);
                    return label.length > 30 ? `${label.slice(0, 27)}…` : label;
                },
            },
        },
    },
    plugins: {
        legend: { display: false },
        tooltip: {
            callbacks: {
                label: (context) =>
                    ` ${trans("Delivered Quantity")}: ${context.parsed.x}`,
            },
        },
    },
}));
</script>

<template>
    <Head :title="$t('Products statistics')" />
    <PageHeader :page-title="$t('Products statistics')" />

    <section
        class="mb-8 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-neutral-700 dark:bg-neutral-800 dark:shadow-black/20"
    >
        <header
            class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-100 px-5 py-4 dark:border-neutral-700"
        >
            <div class="flex items-center gap-3">
                <span
                    class="flex h-10 w-10 items-center justify-center rounded-xl bg-teal-50 text-lg text-teal-700 dark:bg-teal-500/15 dark:text-teal-300"
                    ><i class="ri-trophy-line" aria-hidden="true"></i
                ></span>
                <h2
                    class="text-lg font-semibold text-slate-800 dark:text-neutral-100"
                >
                    {{ $t("Top 10 Best Selling Products") }}
                </h2>
            </div>
            <SelectInput
                variant="admin"
                v-model="currentBestSellingPeriod"
                :aria-label="$t('Period')"
            >
                <option value="total">{{ $t("Total") }}</option>
                <option value="day">{{ $t("Today") }}</option>
                <option value="week">{{ $t("This week") }}</option>
                <option value="month">{{ $t("This month") }}</option>
                <option value="year">{{ $t("This year") }}</option>
            </SelectInput>
        </header>

        <div class="p-5">
            <SectionTip message="Statistics.bestSellingProductsTip" />
            <div
                v-if="bestSellingRows.length"
                class="grid gap-5 2xl:grid-cols-[minmax(0,0.9fr)_minmax(0,1.1fr)]"
            >
                <div
                    class="min-w-0 rounded-xl bg-slate-50 p-4 dark:bg-neutral-900/40"
                >
                    <p
                        class="mb-3 text-sm font-medium text-slate-600 dark:text-neutral-300"
                    >
                        {{ $t("Delivered Quantity") }}
                    </p>
                    <div class="h-[22rem]">
                        <Bar
                            :data="bestSellingChartData"
                            :options="bestSellingChartOptions"
                        />
                    </div>
                </div>
                <div class="min-w-0 overflow-x-auto">
                    <table class="statistics-table">
                        <thead>
                            <tr>
                                <th>{{ $t("Product.product") }}</th>
                                <th>{{ $t("Product.wrapper") }}</th>
                                <th>{{ $t("Product.price") }}</th>
                                <th>{{ $t("Delivered Quantity") }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr
                                v-for="product in bestSellingRows"
                                :key="product.id"
                            >
                                <td class="text-start">
                                    <div
                                        class="flex min-w-64 items-center justify-start gap-3"
                                    >
                                        <img
                                            :src="product.main_image_url"
                                            :alt="product.name"
                                            class="h-12 w-12 shrink-0 rounded-lg object-cover"
                                        />
                                        <Link
                                            :href="
                                                route('products.show', product)
                                            "
                                            class="font-semibold text-slate-800 transition-colors hover:text-teal-700 hover:underline dark:text-neutral-100 dark:hover:text-teal-300"
                                            :title="product.name"
                                        >
                                            {{ product.name }}
                                        </Link>
                                    </div>
                                </td>
                                <td>{{ product.wrapper_title || "—" }}</td>
                                <td class="whitespace-nowrap font-medium">
                                    {{ product.price }}
                                    {{ $t("Product.currency") }}
                                </td>
                                <td>
                                    <span
                                        class="inline-flex items-center justify-center gap-2 font-semibold"
                                    >
                                        <i
                                            class="ri-shopping-cart-line text-slate-500 dark:text-neutral-400"
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
            <NotFound v-else message="NotFound.products" />
        </div>
    </section>

    <section
        class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-neutral-700 dark:bg-neutral-800 dark:shadow-black/20"
    >
        <header
            class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-100 px-5 py-4 dark:border-neutral-700"
        >
            <div class="flex items-center gap-3">
                <span
                    class="flex h-10 w-10 items-center justify-center rounded-xl bg-sky-50 text-lg text-sky-700 dark:bg-sky-500/15 dark:text-sky-300"
                    ><i class="ri-bar-chart-grouped-line" aria-hidden="true"></i
                ></span>
                <h2
                    class="text-lg font-semibold text-slate-800 dark:text-neutral-100"
                >
                    {{ $t("Products By Orders Statuses") }}
                </h2>
            </div>
            <SelectInput
                variant="admin"
                v-model="currentStatisticsPeriod"
                :aria-label="$t('Period')"
            >
                <option value="total">{{ $t("Total") }}</option>
                <option value="day">{{ $t("Today") }}</option>
                <option value="week">{{ $t("This week") }}</option>
                <option value="month">{{ $t("This month") }}</option>
                <option value="year">{{ $t("This year") }}</option>
            </SelectInput>
        </header>

        <div class="p-5">
            <SectionTip message="Statistics.productStatusBreakdownTip" />
            <ResultsCounter :count="statistics.meta.total" />
            <div v-if="productRows.length">
                <div
                    class="grid grid-cols-[repeat(auto-fit,minmax(min(400px,100%),1fr))] gap-4"
                >
                    <Card
                        v-for="product in productRows"
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
                    variant="admin"
                />
            </div>
            <NotFound v-else message="NotFound.products" />
        </div>
    </section>
</template>
