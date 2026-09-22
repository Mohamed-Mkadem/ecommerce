<script setup>
import PageHeader from "@/js/Components/Admin/PageHeader.vue";
import CreateNew from "@/js/Components/CreateNew.vue";
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

const ratesPieChartOptions = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: { position: "bottom" },
        tooltip: {
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
};

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

const deliveryPieChartOptions = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: { position: "bottom" },
        tooltip: {
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
};
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

const chartOptions = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: { display: false },
        tooltip: {
            callbacks: {
                label: (context) =>
                    `${context.parsed.y} ${trans(context.dataset.label)}`,
            },
        },
    },
    scales: {
        x: {
            grid: { display: false },
            ticks: { color: "#475569", font: { size: 12 } },
        },
        y: {
            beginAtZero: true,
            grid: { color: "#e2e8f0" },
            ticks: { color: "#475569", stepSize: 1 },
        },
    },
};
</script>

<template>
    <Head :title="$t('Dashboard')" />
    <PageHeader :page-title="$t('Dashboard')">
        <CreateNew
            :label="$t('New Order')"
            href="orders.create"
            :close-button="false"
        >
        </CreateNew>
    </PageHeader>

    <div
        class="grid xsm:grid-cols-[repeat(auto-fit,minmax(min(200px,100%),1fr))] gap-2 lg:gap-4 items-stretch"
    >
        <div
            class="bg-white p-4 rounded-md shadow-5"
            v-if="$page.props.auth.user.role == 'admin'"
        >
            <p class="text-neutral-700 ltr:-tracking-tighter">
                {{ $t("Earnings") }}
            </p>
            <p class="font-semibold text-2xl mt-3 text-sky-800">
                {{ `${todaysEarnings} ${$t("currency")}` }}
            </p>
            <div class="flex items-end justify-between gap-2">
                <Link
                    class="underline text-sky-900 text-sm capitalize hover:text-sky-800"
                    :href="route('stats.earnings')"
                >
                    {{ $t("Earnings") }}
                </Link>
                <div
                    class="bg-green-500 text-white flex items-center justify-center w-9 h-9 rounded-md"
                >
                    <i class="ri-money-dollar-box-line"></i>
                </div>
            </div>
        </div>
        <div class="bg-white p-4 rounded-md shadow-5">
            <p class="text-neutral-700 ltr:-tracking-tighter">
                {{ $t("Orders") }}
            </p>
            <p class="font-semibold text-2xl mt-3 text-sky-800">
                {{ todaysOrdersCount }}
            </p>
            <div class="flex items-end justify-between gap-2">
                <Link
                    class="underline text-sky-900 text-sm capitalize hover:text-sky-800"
                    :href="route('orders.index')"
                >
                    {{ $t("Orders") }}
                </Link>
                <div
                    class="bg-sky-500 text-white flex items-center justify-center w-9 h-9 rounded-md"
                >
                    <i class="ri-shopping-cart-fill"></i>
                </div>
            </div>
        </div>

        <div
            class="bg-white p-4 rounded-md shadow-5"
            v-if="$page.props.auth.user.role == 'admin'"
        >
            <p class="text-neutral-700 ltr:-tracking-tighter">
                {{ $t("Invoices") }}
            </p>
            <p class="font-semibold text-2xl mt-3 text-sky-800">
                {{ todaysInvoicesCount }}
            </p>
            <div class="flex items-end justify-between gap-2">
                <Link
                    class="underline text-sky-900 text-sm capitalize hover:text-sky-800"
                    :href="route('invoices.index')"
                >
                    {{ $t("Invoices") }}
                </Link>
                <div
                    class="bg-green-500 text-white flex items-center justify-center w-9 h-9 rounded-md"
                >
                    <i class="ri-money-dollar-circle-fill"></i>
                </div>
            </div>
        </div>
        <div
            class="bg-white p-4 rounded-md shadow-5"
             v-if="$page.props.auth.user.role == 'moderator'"
        >
            <p class="text-neutral-700 ltr:-tracking-tighter">
                {{ $t("Clients") }}
            </p>
            <p class="font-semibold text-2xl mt-3 text-sky-800">
                {{ todaysClientsCount }}
            </p>
            <div class="flex items-end justify-between gap-2">
                <Link
                    class="underline text-sky-900 text-sm capitalize hover:text-sky-800"
                    :href="route('clients.index')"
                >
                    {{ $t("Clients") }}
                </Link>
                <div
                    class="bg-indigo-500 text-white flex items-center justify-center w-9 h-9 rounded-md"
                >
                    <i class="ri-group-line"></i>
                </div>
            </div>
        </div>
        <div
            class="bg-white p-4 rounded-md shadow-5"
           
        >
            <p class="text-neutral-700 ltr:-tracking-tighter">
                {{ $t("Notifications") }}
            </p>
            <p class="font-semibold text-2xl mt-3 text-sky-800">
                {{ todaysNotificationsCount }}
            </p>
            <div class="flex items-end justify-between gap-2">
                <Link
                    class="underline text-sky-900 text-sm capitalize hover:text-sky-800"
                    :href="route('notifications.index')"
                >
                    {{ $t("Notifications") }}
                </Link>
                <div
                    class="bg-body text-white flex items-center justify-center w-9 h-9 rounded-md"
                >
                    <i class="ri-notification-line"></i>
                </div>
            </div>
        </div>
    </div>
    <section class="my-12">
        <div
            class="flex items-center justify-between gap-3 flex-wrap bg-white p-3 shadow-1 mb-4"
        >
            <h2 class="text-sky-800 font-semibold text-lg">
                {{ $t("Orders acceptance date") }}
            </h2>
            <ModalLink
                :close-button="false"
                :href="route('shippingSettings.edit', acceptance_dates.id)"
                class="bg-slate-700 text-white text-xl rounded-md px-2 py-1 hover:bg-opacity-90"
            >
                <i class="ri-edit-line"></i>
            </ModalLink>
        </div>
        <div class="grid xmd:grid-cols-2 bg-white shadow-1 rounded-lg p-4">
            <div class="text-black">
                <h3 class="text-lg font-semibold text-sky-900">
                    {{ $t("Greater Tunis") }}
                </h3>
                <p>{{ acceptance_dates.tunis_acceptance_delivery_date }}</p>
            </div>
            <div class="text-black">
                <h3 class="text-lg font-semibold text-sky-900">
                    {{ $t("The states") }}
                </h3>
                <p>{{ acceptance_dates.wilayet_acceptance_delivery_date }}</p>
            </div>
        </div>
    </section>
    <section class="my-12" v-if="weeklyOrders?.length">
        <div
            class="flex items-center justify-between gap-3 flex-wrap bg-white p-3 shadow-1 mb-4"
        >
            <h2 class="text-sky-800 font-semibold text-lg">
                {{ $t("Weekly Orders") }}
            </h2>
            <span class="text-sm text-slate-500">{{ $t("Last 7 days") }}</span>
        </div>
        <div class="bg-white rounded-md shadow-1 p-4">
            <div class="h-80">
                <Bar :data="chartData" :options="chartOptions" />
            </div>
        </div>
    </section>

    <section class="my-12" v-if="weeklyClientOrders?.length">
        <div
            class="flex items-center justify-between gap-3 flex-wrap bg-white p-3 shadow-1 mb-4"
        >
            <h2 class="text-sky-800 font-semibold text-lg">
                {{ $t("Automatic Orders") }}
            </h2>
            <span class="text-sm text-slate-500">{{ $t("Last 7 days") }}</span>
        </div>
        <div class="bg-white rounded-md shadow-1 p-4">
            <div class="h-80">
                <Bar :data="clientChartData" :options="chartOptions" />
            </div>
        </div>
    </section>

<section class="my-12">
        <div class="flex flex-col gap-6">
            <div class="bg-white rounded-lg shadow-1 p-6">
                <div
                    class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6"
                >
                    <div>
                        <h2 class="text-xl font-bold text-slate-800">
                            <i class="ri-pie-chart-2-line mr-2"></i
                            >{{ $t("Rates Overview") }}
                        </h2>
                        <p class="text-slate-500 text-sm mt-1">
                            {{
                                $t(
                                    "A visual breakdown of confirmation, cancellation, and NRP rates.",
                                )
                            }}
                        </p>
                    </div>
                    <div
                        class="grid sm:grid-cols-[1fr,20px,1fr] items-center gap-2"
                    >
                        <div class="w-full">
                            <DatePicker v-model="ratesDateFilters.start_date" />
                        </div>
                        <span class="text-slate-500 text-center">{{
                            $t("to")
                        }}</span>
                        <div class="w-full">
                            <DatePicker v-model="ratesDateFilters.end_date" />
                        </div>
                    </div>
                </div>
                <div class="max-w-sm mx-auto h-72 relative">
                    <div
                        v-if="loadingRates"
                        class="absolute inset-0 flex items-center justify-center bg-white bg-opacity-75 z-10"
                    >
                        <i
                            class="ri-loader-4-line animate-spin text-3xl text-sky-600"
                        ></i>
                    </div>
                    <Pie
                        :data="ratesPieChartData"
                        :options="ratesPieChartOptions"
                    />
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-1 p-6">
                <div
                    class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6"
                >
                    <div>
                        <h2 class="text-xl font-bold text-slate-800">
                            <i class="ri-pie-chart-line mr-2"></i
                            >{{ $t("Delivery & Return Rates") }}
                        </h2>
                        <p class="text-slate-500 text-sm mt-1">
                            {{
                                $t(
                                    "A visual breakdown of delivered vs returned orders.",
                                )
                            }}
                        </p>
                    </div>
                    <div
                        class="grid sm:grid-cols-[1fr,20px,1fr] items-center gap-2"
                    >
                        <div class="w-full">
                            <DatePicker
                                v-model="deliveryDateFilters.start_date"
                            />
                        </div>
                        <span class="text-slate-500 text-center">{{
                            $t("to")
                        }}</span>
                        <div class="w-full">
                            <DatePicker
                                v-model="deliveryDateFilters.end_date"
                            />
                        </div>
                    </div>
                </div>
                <div class="max-w-sm mx-auto h-72 relative">
                    <div
                        v-if="loadingDelivery"
                        class="absolute inset-0 flex items-center justify-center bg-white bg-opacity-75 z-10"
                    >
                        <i
                            class="ri-loader-4-line animate-spin text-3xl text-sky-600"
                        ></i>
                    </div>
                    <Pie
                        :data="deliveryPieChartData"
                        :options="deliveryPieChartOptions"
                    />
                </div>
            </div>
        </div>
    </section>

  
</template>
