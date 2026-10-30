<script setup>
import PageHeader from "@/js/Components/Admin/PageHeader.vue";
import SelectInput from "@/js/Components/SelectInput.vue";
import { useAppearanceStore } from "@/js/stores/appearance";
import { computed, ref } from "vue";
import { Doughnut } from "vue-chartjs";
import { ArcElement, Chart as ChartJS, Legend, Tooltip } from "chart.js";
import { trans } from "laravel-vue-i18n";

ChartJS.register(ArcElement, Legend, Tooltip);

const props = defineProps({
    orders: { type: Object },
    states: { type: Object },
});

const appearanceStore = useAppearanceStore();
const currentOrdersPeriod = ref("total");
const currentStatesPeriod = ref("total");

const summaryPeriods = [
    { key: "total", label: "Total", icon: "ri-shopping-bag-3-line" },
    { key: "day", label: "Today", icon: "ri-calendar-event-line" },
    { key: "week", label: "This week", icon: "ri-calendar-check-line" },
    { key: "month", label: "This month", icon: "ri-calendar-2-line" },
    { key: "year", label: "This year", icon: "ri-calendar-line" },
];

const statusColors = {
    pending: "#f59e0b",
    confirmed: "#0f766e",
    shipped: "#0284c7",
    canceled: "#e11d48",
    returned: "#f97316",
    delivered: "#16a34a",
    abandoned: "#64748b",
};

const statusRows = computed(() =>
    Object.entries(props.orders?.[currentOrdersPeriod.value] ?? {})
        .filter(([status]) => status !== "total")
        .map(([status, count]) => ({ status, count })),
);

const stateRows = computed(() =>
    Object.values(props.states?.[currentStatesPeriod.value] ?? {}),
);

const statusChartData = computed(() => ({
    labels: statusRows.value.map(({ status }) => trans(status)),
    datasets: [
        {
            data: statusRows.value.map(({ count }) => Number(count) || 0),
            backgroundColor: statusRows.value.map(
                ({ status }) => statusColors[status] ?? "#64748b",
            ),
            borderColor: appearanceStore.isDark ? "#262626" : "#ffffff",
            borderWidth: 3,
            hoverOffset: 6,
        },
    ],
}));

const statusChartOptions = computed(() => ({
    responsive: true,
    maintainAspectRatio: false,
    cutout: "66%",
    plugins: {
        legend: {
            position: "bottom",
            labels: {
                color: appearanceStore.isDark ? "#d4d4d4" : "#64748b",
                boxWidth: 10,
                boxHeight: 10,
                padding: 14,
                usePointStyle: true,
            },
        },
        tooltip: {
            callbacks: {
                label: (context) => ` ${context.label}: ${context.parsed}`,
            },
        },
    },
}));
</script>

<template>
    <Head :title="$t('Orders statistics')" />
    <PageHeader :page-title="$t('Orders statistics')" />

    <section class="mb-8 grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5">
        <article
            v-for="period in summaryPeriods"
            :key="period.key"
            class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md dark:border-neutral-700 dark:bg-neutral-800 dark:shadow-black/20"
        >
            <div class="flex items-start justify-between gap-3">
                <div>
                    <p class="text-sm font-medium text-slate-500 dark:text-neutral-400">{{ $t(period.label) }}</p>
                    <p class="mt-2 text-xl font-semibold tracking-tight text-slate-800 dark:text-neutral-100">
                        {{ orders[period.key].total }}
                    </p>
                </div>
                <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-slate-100 text-lg text-slate-600 dark:bg-neutral-700 dark:text-neutral-200">
                    <i :class="period.icon" aria-hidden="true"></i>
                </span>
            </div>
            <div class="mt-4 h-1 rounded-full bg-teal-700"></div>
        </article>
    </section>

    <section class="mb-6 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-neutral-700 dark:bg-neutral-800 dark:shadow-black/20">
        <header class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-100 px-5 py-4 dark:border-neutral-700">
            <div class="flex items-center gap-3">
                <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-teal-50 text-lg text-teal-700 dark:bg-teal-500/15 dark:text-teal-300"><i class="ri-pie-chart-2-line" aria-hidden="true"></i></span>
                <h2 class="text-lg font-semibold text-slate-800 dark:text-neutral-100">{{ $t("Orders By Status") }}</h2>
            </div>
            <SelectInput variant="admin" v-model="currentOrdersPeriod" :aria-label="$t('Period')">
                <option value="total">{{ $t("Total") }}</option>
                <option value="day">{{ $t("Today") }}</option>
                <option value="week">{{ $t("This week") }}</option>
                <option value="month">{{ $t("This month") }}</option>
                <option value="year">{{ $t("This year") }}</option>
            </SelectInput>
        </header>
        <div class="grid gap-5 p-5 lg:grid-cols-[minmax(0,0.8fr)_minmax(0,1.2fr)]">
            <div class="min-w-0 rounded-xl bg-slate-50 p-4 dark:bg-neutral-900/40">
                <p class="text-xs font-medium text-slate-500 dark:text-neutral-400">{{ $t("Orders") }}</p>
                <p class="mt-1 text-lg font-semibold text-slate-800 dark:text-neutral-100">{{ orders[currentOrdersPeriod].total }}</p>
                <div class="mt-3 h-56">
                    <Doughnut :data="statusChartData" :options="statusChartOptions" />
                </div>
            </div>
            <div class="min-w-0 overflow-x-auto">
                <table class="statistics-table">
                    <thead>
                        <tr>
                            <th>{{ $t("Status") }}</th>
                            <th>{{ $t("Number of orders") }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="row in statusRows" :key="row.status">
                            <td class="font-medium">{{ $t(row.status) }}</td>
                            <td>{{ row.count }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-neutral-700 dark:bg-neutral-800 dark:shadow-black/20">
        <header class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-100 px-5 py-4 dark:border-neutral-700">
            <div class="flex items-center gap-3">
                <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-sky-50 text-lg text-sky-700 dark:bg-sky-500/15 dark:text-sky-300"><i class="ri-map-pin-line" aria-hidden="true"></i></span>
                <h2 class="text-lg font-semibold text-slate-800 dark:text-neutral-100">{{ $t("Orders By States") }}</h2>
            </div>
            <SelectInput variant="admin" v-model="currentStatesPeriod" :aria-label="$t('Period')">
                <option value="total">{{ $t("Total") }}</option>
                <option value="day">{{ $t("Today") }}</option>
                <option value="week">{{ $t("This week") }}</option>
                <option value="month">{{ $t("This month") }}</option>
                <option value="year">{{ $t("This year") }}</option>
            </SelectInput>
        </header>
        <div class="p-5">
            <div class="overflow-x-auto">
                <table class="statistics-table">
                    <thead>
                        <tr>
                            <th>{{ $t("State") }}</th>
                            <th>{{ $t("Number of orders") }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(state, index) in stateRows" :key="`${state.state}-${index}`">
                            <td class="font-medium">{{ state.state }}</td>
                            <td>{{ state.count }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </section>
</template>
