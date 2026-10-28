<script setup>
import PageHeader from "@/js/Components/Admin/PageHeader.vue";
import CreateNew from "@/js/Components/CreateNew.vue";
import { useAppearanceStore } from "@/js/stores/appearance";
import { computed, ref, onMounted, watch } from "vue";
import { trans } from "laravel-vue-i18n";
import DatePicker from "@/js/Components/DatePicker.vue";
import { Bar, Pie } from "vue-chartjs";
import {
    Chart as ChartJS,
    CategoryScale,
    LinearScale,
    BarElement,
    Title,
    Tooltip,
    Legend,
    ArcElement
} from "chart.js";
import axios from "axios";
import { debounce } from "lodash";
ChartJS.register(
    CategoryScale,
    LinearScale,
    BarElement,
    Title,
    Tooltip,
    Legend,
    ArcElement
);

const props = defineProps({
    todaysEarnings: { type: Number },
    todaysInvoicesCount: { type: Number },
    todaysOrdersCount: { type: Number },
    todaysClientsCount: { type: Number },
    todaysNotificationsCount: { type: Number },
    weeklyOrders: { type: Array },
    weeklyClientOrders: { type: Array },
    acceptance_dates: { type: Object },
    defaultRatesStart: String,
    defaultRatesEnd: String,
    defaultDeliveryStart: String,
    defaultDeliveryEnd: String,
});
const appearanceStore = useAppearanceStore();

const orderRates = ref({});
const deliveryRates = ref({});
const loadingRates = ref(false);
const loadingDelivery = ref(false);

const ratesDateFilters = ref({
    start_date: props.defaultRatesStart || "",
    end_date: props.defaultRatesEnd || "",
});

const deliveryDateFilters = ref({
    start_date: props.defaultDeliveryStart || "",
    end_date: props.defaultDeliveryEnd || "",
});

const fetchChartData = async (type = "both") => {
    try {
        if (type === "rates" || type === "both") {
            loadingRates.value = true;
        }
        if (type === "delivery" || type === "both") {
            loadingDelivery.value = true;
        }

        const params = {
            rates_start_date: ratesDateFilters.value.start_date,
            rates_end_date: ratesDateFilters.value.end_date,
            delivery_start_date: deliveryDateFilters.value.start_date,
            delivery_end_date: deliveryDateFilters.value.end_date,
        };

        const response = await axios.get(route("charts.update"), { params });

        if (type === "rates" || type === "both") {
            orderRates.value = response.data.orderRates || {};
        }
        if (type === "delivery" || type === "both") {
            deliveryRates.value = response.data.deliveryRates || {};
        }
    } catch (error) {
        console.error("Error fetching chart data:", error);
    } finally {
        loadingRates.value = false;
        loadingDelivery.value = false;
    }
};

onMounted(() => {
    fetchChartData("both");
});

const debouncedFetch = debounce((type) => fetchChartData(type), 500);

watch(
    ratesDateFilters,
    () => {
        debouncedFetch("rates");
    },
    { deep: true },
);

watch(
    deliveryDateFilters,
    () => {
        debouncedFetch("delivery");
    },
    { deep: true },
);

const ratesPieChartData = computed(() => ({
    labels: [trans("Confirmed"), trans("Canceled"), trans("NRP")],
    datasets: [
        {
            data: [
                orderRates.value?.["order.confirmed"] || 0,
                orderRates.value?.["order.canceled"] || 0,
                orderRates.value?.["order.nrp"] || 0,
            ],
            backgroundColor: ["#bbf7d0", "#fecaca", "#075985"],
            borderColor: ["#86efac", "#fca5a5", "#0c4a6e"],
            borderWidth: 1,
        },
    ],
}));

const ratesPieChartOptions = computed(() => ({
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: {
            position: "bottom",
            labels: { color: appearanceStore.isDark ? "#d4d4d4" : "#475569" },
        },
        tooltip: {
            backgroundColor: appearanceStore.isDark ? "#292a2d" : "#ffffff",
            titleColor: appearanceStore.isDark ? "#f5f5f5" : "#0f172a",
            bodyColor: appearanceStore.isDark ? "#d4d4d4" : "#334155",
            borderColor: appearanceStore.isDark ? "#4b4b4b" : "#e2e8f0",
            borderWidth: 1,
            callbacks: {
                label: function (context) {
                    const value = context.parsed;
                    const total = context.dataset.data.reduce(
                        (acc, v) => acc + v,
                        0,
                    );
                    const percentage =
                        total > 0 ? Math.round((value / total) * 100) : 0;
                    return `${context.label}: ${value} (${percentage}%)`;
                },
            },
        },
    },
}));

const deliveryPieChartData = computed(() => ({
    labels: [trans("Delivered"), trans("Returned")],
    datasets: [
        {
            data: [
                deliveryRates.value?.["order.delivered"] || 0,
                deliveryRates.value?.["order.returned"] || 0,
            ],
            backgroundColor: ["#bfdbfe", "#e4e4e7"],
            borderColor: ["#93c5fd", "#d4d4d8"],
            borderWidth: 1,
        },
    ],
}));

const deliveryPieChartOptions = computed(() => ({
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: {
            position: "bottom",
            labels: { color: appearanceStore.isDark ? "#d4d4d4" : "#475569" },
        },
        tooltip: {
            backgroundColor: appearanceStore.isDark ? "#292a2d" : "#ffffff",
            titleColor: appearanceStore.isDark ? "#f5f5f5" : "#0f172a",
            bodyColor: appearanceStore.isDark ? "#d4d4d4" : "#334155",
            borderColor: appearanceStore.isDark ? "#4b4b4b" : "#e2e8f0",
            borderWidth: 1,
            callbacks: {
                label: function (context) {
                    const value = context.parsed;
                    const total = context.dataset.data.reduce(
                        (acc, v) => acc + v,
                        0,
                    );
                    const percentage =
                        total > 0 ? Math.round((value / total) * 100) : 0;
                    return `${context.label}: ${value} (${percentage}%)`;
                },
            },
        },
    },
}));
const chartData = computed(() => ({
    labels: props.weeklyOrders?.map((day) => {
        const parts = day.label.split(" ");
        return parts.length === 2 ? `${trans(parts[0])} ${parts[1]}` : trans(day.label);
    }) ?? [],
    datasets: [
        {
            label: "Orders",
            data: props.weeklyOrders?.map((day) => day.count) ?? [],
            backgroundColor: "#0ea5e9",
            borderRadius: 12,
            borderSkipped: false,
            maxBarThickness: 40,
        },
    ],
}));

const clientChartData = computed(() => ({
    labels: props.weeklyClientOrders?.map((day) => {
        const parts = day.label.split(" ");
        return parts.length === 2 ? `${trans(parts[0])} ${parts[1]}` : trans(day.label);
    }) ?? [],
    datasets: [
        {
            label: "Client Orders",
            data: props.weeklyClientOrders?.map((day) => day.count) ?? [],
            backgroundColor: "#14b8a6",
            borderRadius: 12,
            borderSkipped: false,
            maxBarThickness: 40,
        },
    ],
}));

const chartOptions = computed(() => ({
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: { display: false },
        tooltip: {
            backgroundColor: appearanceStore.isDark ? "#292a2d" : "#ffffff",
            titleColor: appearanceStore.isDark ? "#f5f5f5" : "#0f172a",
            bodyColor: appearanceStore.isDark ? "#d4d4d4" : "#334155",
            borderColor: appearanceStore.isDark ? "#4b4b4b" : "#e2e8f0",
            borderWidth: 1,
            callbacks: {
                label: (context) =>
                    `${context.parsed.y} ${trans(context.dataset.label)}`,
            },
        },
    },
    scales: {
        x: {
            grid: { display: false },
            ticks: {
                color: appearanceStore.isDark ? "#d4d4d4" : "#475569",
                font: { size: 12 },
            },
        },
        y: {
            beginAtZero: true,
            grid: {
                color: appearanceStore.isDark ? "#454545" : "#e2e8f0",
            },
            ticks: {
                color: appearanceStore.isDark ? "#d4d4d4" : "#475569",
                stepSize: 1,
            },
        },
    },
}));
</script>

<template>

    <Head :title="$t('Dashboard')" />
    <PageHeader :page-title="$t('Dashboard')">
        <CreateNew :label="$t('New Order')" href="orders.create" :close-button="false">
        </CreateNew>
    </PageHeader>

    <div class="grid xsm:grid-cols-[repeat(auto-fit,minmax(min(200px,100%),1fr))] gap-2 lg:gap-4 items-stretch">
        <div class="rounded-2xl border border-slate-200/80 bg-white p-5 shadow-[0_8px_30px_rgba(15,23,42,0.06)] transition duration-200 hover:-translate-y-0.5 hover:shadow-[0_14px_34px_rgba(15,23,42,0.10)] dark:border-neutral-700 dark:bg-neutral-800 dark:shadow-black/20"
            v-if="$page.props.auth.user.role == 'admin'">
            <p class="text-sm font-medium text-slate-500 dark:text-neutral-400">
                {{ $t("Earnings") }}
            </p>
            <p class="mt-3 text-2xl font-semibold tracking-tight text-slate-900 dark:text-neutral-50">
                {{ `${todaysEarnings} ${$t("currency")}` }}
            </p>
            <div class="flex items-end justify-between gap-2">
                <Link class="text-sm font-medium capitalize text-primary underline decoration-slate-300 underline-offset-4 transition-colors hover:text-sky-700 hover:decoration-primary dark:text-neutral-200 dark:decoration-neutral-500 dark:hover:text-white"
                    :href="route('stats.earnings')">
                {{ $t("Earnings") }}
                <i class="ri-arrow-right-up-line ms-1" aria-hidden="true"></i>
                </Link>
                <div
                    class="flex h-11 w-11 items-center justify-center rounded-xl bg-emerald-50 text-lg text-emerald-600 dark:bg-emerald-950/60 dark:text-emerald-300">
                    <i class="ri-money-dollar-box-line"></i>
                </div>
            </div>
        </div>
        <div
            class="rounded-2xl border border-slate-200/80 bg-white p-5 shadow-[0_8px_30px_rgba(15,23,42,0.06)] transition duration-200 hover:-translate-y-0.5 hover:shadow-[0_14px_34px_rgba(15,23,42,0.10)] dark:border-neutral-700 dark:bg-neutral-800 dark:shadow-black/20">
            <p class="text-sm font-medium text-slate-500 dark:text-neutral-400">
                {{ $t("Orders") }}
            </p>
            <p class="mt-3 text-2xl font-semibold tracking-tight text-slate-900 dark:text-neutral-50">
                {{ todaysOrdersCount }}
            </p>
            <div class="flex items-end justify-between gap-2">
                <Link class="text-sm font-medium capitalize text-primary underline decoration-slate-300 underline-offset-4 transition-colors hover:text-sky-700 hover:decoration-primary dark:text-neutral-200 dark:decoration-neutral-500 dark:hover:text-white"
                    :href="route('orders.index')">
                {{ $t("Orders") }}
                <i class="ri-arrow-right-up-line ms-1" aria-hidden="true"></i>
                </Link>
                <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-sky-50 text-lg text-sky-600 dark:bg-sky-950/60 dark:text-sky-300">
                    <i class="ri-shopping-cart-fill"></i>
                </div>
            </div>
        </div>

        <div class="rounded-2xl border border-slate-200/80 bg-white p-5 shadow-[0_8px_30px_rgba(15,23,42,0.06)] transition duration-200 hover:-translate-y-0.5 hover:shadow-[0_14px_34px_rgba(15,23,42,0.10)] dark:border-neutral-700 dark:bg-neutral-800 dark:shadow-black/20"
            v-if="$page.props.auth.user.role == 'admin'">
            <p class="text-sm font-medium text-slate-500 dark:text-neutral-400">
                {{ $t("Invoices") }}
            </p>
            <p class="mt-3 text-2xl font-semibold tracking-tight text-slate-900 dark:text-neutral-50">
                {{ todaysInvoicesCount }}
            </p>
            <div class="flex items-end justify-between gap-2">
                <Link class="text-sm font-medium capitalize text-primary underline decoration-slate-300 underline-offset-4 transition-colors hover:text-sky-700 hover:decoration-primary dark:text-neutral-200 dark:decoration-neutral-500 dark:hover:text-white"
                    :href="route('invoices.index')">
                {{ $t("Invoices") }}
                <i class="ri-arrow-right-up-line ms-1" aria-hidden="true"></i>
                </Link>
                <div
                    class="flex h-11 w-11 items-center justify-center rounded-xl bg-emerald-50 text-lg text-emerald-600 dark:bg-violet-950/60 dark:text-violet-300">
                    <i class="ri-money-dollar-circle-fill"></i>
                </div>
            </div>
        </div>
        <div class="rounded-2xl border border-slate-200/80 bg-white p-5 shadow-[0_8px_30px_rgba(15,23,42,0.06)] transition duration-200 hover:-translate-y-0.5 hover:shadow-[0_14px_34px_rgba(15,23,42,0.10)] dark:border-neutral-700 dark:bg-neutral-800 dark:shadow-black/20"
            v-if="$page.props.auth.user.role == 'moderator'">
            <p class="text-sm font-medium text-slate-500 dark:text-neutral-400">
                {{ $t("Clients") }}
            </p>
            <p class="mt-3 text-2xl font-semibold tracking-tight text-slate-900 dark:text-neutral-50">
                {{ todaysClientsCount }}
            </p>
            <div class="flex items-end justify-between gap-2">
                <Link class="text-sm font-medium capitalize text-primary underline decoration-slate-300 underline-offset-4 transition-colors hover:text-sky-700 hover:decoration-primary dark:text-neutral-200 dark:decoration-neutral-500 dark:hover:text-white"
                    :href="route('clients.index')">
                {{ $t("Clients") }}
                <i class="ri-arrow-right-up-line ms-1" aria-hidden="true"></i>
                </Link>
                <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-indigo-50 text-lg text-indigo-600 dark:bg-indigo-950/60 dark:text-indigo-300">
                    <i class="ri-group-line"></i>
                </div>
            </div>
        </div>
        <div
            class="rounded-2xl border border-slate-200/80 bg-white p-5 shadow-[0_8px_30px_rgba(15,23,42,0.06)] transition duration-200 hover:-translate-y-0.5 hover:shadow-[0_14px_34px_rgba(15,23,42,0.10)] dark:border-neutral-700 dark:bg-neutral-800 dark:shadow-black/20">
            <p class="text-sm font-medium text-slate-500 dark:text-neutral-400">
                {{ $t("Notifications") }}
            </p>
            <p class="mt-3 text-2xl font-semibold tracking-tight text-slate-900 dark:text-neutral-50">
                {{ todaysNotificationsCount }}
            </p>
            <div class="flex items-end justify-between gap-2">
                <Link class="text-sm font-medium capitalize text-primary underline decoration-slate-300 underline-offset-4 transition-colors hover:text-sky-700 hover:decoration-primary dark:text-neutral-200 dark:decoration-neutral-500 dark:hover:text-white"
                    :href="route('notifications.index')">
                {{ $t("Notifications") }}
                <i class="ri-arrow-right-up-line ms-1" aria-hidden="true"></i>
                </Link>
                <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-slate-100 text-lg text-slate-600 dark:bg-rose-950/60 dark:text-rose-300">
                    <i class="ri-notification-line"></i>
                </div>
            </div>
        </div>
    </div>
    <section class="my-12">
        <div
            class="flex flex-wrap items-center justify-between gap-3 rounded-t-2xl border border-slate-200/80 bg-white px-5 py-4 dark:border-neutral-700 dark:bg-neutral-800">
            <h2 class="text-lg font-semibold text-slate-800 dark:text-neutral-100">
                {{ $t("Orders acceptance date") }}
            </h2>
            <ModalLink :close-button="false" :href="route('shippingSettings.edit', acceptance_dates.id)"
                class="rounded-xl bg-primary/5 px-3 py-2 text-lg text-primary transition-colors hover:bg-primary/10 dark:bg-neutral-700 dark:text-neutral-200 dark:hover:bg-neutral-600">
                <i class="ri-edit-line"></i>
            </ModalLink>
        </div>
        <div
            class="grid gap-4 rounded-b-2xl border border-t-0 border-slate-200/80 bg-white p-5 shadow-sm xmd:grid-cols-2 dark:border-neutral-700 dark:bg-neutral-800">
            <div class="rounded-xl bg-slate-50/80 p-4 dark:bg-neutral-700/70">
                <h3 class="text-sm font-semibold text-slate-500 dark:text-neutral-400">
                    {{ $t("Greater Tunis") }}
                </h3>
                <p class="mt-1 text-base font-semibold text-slate-800 dark:text-neutral-100">{{
                    acceptance_dates.tunis_acceptance_delivery_date }}</p>
            </div>
            <div class="rounded-xl bg-slate-50/80 p-4 dark:bg-neutral-700/70">
                <h3 class="text-sm font-semibold text-slate-500 dark:text-neutral-400">
                    {{ $t("The states") }}
                </h3>
                <p class="mt-1 text-base font-semibold text-slate-800 dark:text-neutral-100">{{
                    acceptance_dates.wilayet_acceptance_delivery_date }}</p>
            </div>
        </div>
    </section>
    <section class="my-12" v-if="weeklyOrders?.length">
        <div
            class="flex flex-wrap items-center justify-between gap-3 rounded-t-2xl border border-slate-200/80 bg-white px-5 py-4 dark:border-neutral-700 dark:bg-neutral-800">
            <h2 class="text-lg font-semibold text-slate-800 dark:text-neutral-100">
                {{ $t("Orders (Last 15 Days)") }}
            </h2>
            <span class="text-sm text-slate-500 dark:text-neutral-400">{{ $t("Last 15 days") }}</span>
        </div>
        <div class="rounded-b-2xl border border-t-0 border-slate-200/80 bg-white p-5 shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
            <div class="h-80">
                <Bar :data="chartData" :options="chartOptions" />
            </div>
        </div>
    </section>

    <section class="my-12" v-if="weeklyClientOrders?.length">
        <div
            class="flex flex-wrap items-center justify-between gap-3 rounded-t-2xl border border-slate-200/80 bg-white px-5 py-4 dark:border-neutral-700 dark:bg-neutral-800">
            <h2 class="text-lg font-semibold text-slate-800 dark:text-neutral-100">
                {{ $t("Storefront Orders (Last 15 Days)") }}
            </h2>
            <span class="text-sm text-slate-500 dark:text-neutral-400">{{ $t("Last 15 days") }}</span>
        </div>
        <div class="rounded-b-2xl border border-t-0 border-slate-200/80 bg-white p-5 shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
            <div class="h-80">
                <Bar :data="clientChartData" :options="chartOptions" />
            </div>
        </div>
    </section>

    <section class="my-12">
        <div class="flex flex-col gap-6">
            <div
                class="rounded-2xl border border-slate-200/80 bg-white p-5 shadow-[0_8px_30px_rgba(15,23,42,0.06)] sm:p-6 dark:border-neutral-700 dark:bg-neutral-800 dark:shadow-black/20">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
                    <div>
                        <h2 class="text-xl font-semibold tracking-tight text-slate-800 dark:text-neutral-100">
                            <i class="ri-pie-chart-2-line me-2"></i>{{ $t("Rates Overview") }}
                        </h2>
                        <p class="mt-1 text-sm text-slate-500 dark:text-neutral-400">
                            {{
                                $t(
                                    "A visual breakdown of confirmation, cancellation, and NRP rates.",
                                )
                            }}
                        </p>
                    </div>
                    <div class="grid sm:grid-cols-[1fr,20px,1fr] items-center gap-2">
                        <div class="w-full">
                            <DatePicker v-model="ratesDateFilters.start_date" />
                        </div>
                        <span class="text-center text-slate-500 dark:text-neutral-400">{{
                            $t("to")
                        }}</span>
                        <div class="w-full">
                            <DatePicker v-model="ratesDateFilters.end_date" />
                        </div>
                    </div>
                </div>
                <div class="max-w-sm mx-auto h-72 relative">
                    <div v-if="loadingRates"
                        class="absolute inset-0 z-10 flex items-center justify-center rounded-xl bg-white/75 backdrop-blur-[2px] dark:bg-neutral-800/75">
                        <i class="ri-loader-4-line animate-spin text-3xl text-sky-600"></i>
                    </div>
                    <Pie :data="ratesPieChartData" :options="ratesPieChartOptions" />
                </div>
            </div>

            <div
                class="rounded-2xl border border-slate-200/80 bg-white p-5 shadow-[0_8px_30px_rgba(15,23,42,0.06)] sm:p-6 dark:border-neutral-700 dark:bg-neutral-800 dark:shadow-black/20">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
                    <div>
                        <h2 class="text-xl font-semibold tracking-tight text-slate-800 dark:text-neutral-100">
                            <i class="ri-pie-chart-line me-2"></i>{{ $t("Delivery & Return Rates") }}
                        </h2>
                        <p class="mt-1 text-sm text-slate-500 dark:text-neutral-400">
                            {{
                                $t(
                                    "A visual breakdown of delivered vs returned orders.",
                                )
                            }}
                        </p>
                    </div>
                    <div class="grid sm:grid-cols-[1fr,20px,1fr] items-center gap-2">
                        <div class="w-full">
                            <DatePicker v-model="deliveryDateFilters.start_date" />
                        </div>
                        <span class="text-center text-slate-500 dark:text-neutral-400">{{
                            $t("to")
                        }}</span>
                        <div class="w-full">
                            <DatePicker v-model="deliveryDateFilters.end_date" />
                        </div>
                    </div>
                </div>
                <div class="max-w-sm mx-auto h-72 relative">
                    <div v-if="loadingDelivery"
                        class="absolute inset-0 z-10 flex items-center justify-center rounded-xl bg-white/75 backdrop-blur-[2px] dark:bg-neutral-800/75">
                        <i class="ri-loader-4-line animate-spin text-3xl text-sky-600"></i>
                    </div>
                    <Pie :data="deliveryPieChartData" :options="deliveryPieChartOptions" />
                </div>
            </div>
        </div>
    </section>


</template>
