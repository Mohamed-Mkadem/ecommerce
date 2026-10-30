<script setup>
import PageHeader from "@/js/Components/Admin/PageHeader.vue";
import SelectInput from "@/js/Components/SelectInput.vue";
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

ChartJS.register(CategoryScale, LinearScale, BarElement, Tooltip);

const props = defineProps({
    clients: { type: Object },
    states: { type: Object },
});

const appearanceStore = useAppearanceStore();
const currentStatesPeriod = ref("total");

const summaryPeriods = [
    { key: "total", label: "Total", icon: "ri-group-line" },
    { key: "day", label: "Today", icon: "ri-user-add-line" },
    { key: "week", label: "This week", icon: "ri-calendar-check-line" },
    { key: "month", label: "This month", icon: "ri-calendar-2-line" },
    { key: "year", label: "This year", icon: "ri-calendar-line" },
];

const stateRows = computed(() =>
    Object.values(props.states?.[currentStatesPeriod.value] ?? {}),
);
const topStateRows = computed(() => stateRows.value.slice(0, 10));

const stateChartData = computed(() => ({
    labels: topStateRows.value.map((state) => state.state),
    datasets: [
        {
            label: "Clients",
            data: topStateRows.value.map((state) => Number(state.count) || 0),
            backgroundColor: "#0f766e",
            hoverBackgroundColor: "#115e59",
            borderRadius: 7,
            barThickness: 18,
        },
    ],
}));

const stateChartOptions = computed(() => ({
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
                    return label.length > 22 ? `${label.slice(0, 19)}…` : label;
                },
            },
        },
    },
    plugins: {
        legend: { display: false },
        tooltip: {
            callbacks: {
                label: (context) => ` ${context.parsed.x}`,
            },
        },
    },
}));
</script>

<template>
    <Head :title="$t('Clients statistics')" />
    <PageHeader :page-title="$t('Clients statistics')" />

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
                        {{ clients[period.key] }}
                    </p>
                </div>
                <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-slate-100 text-lg text-slate-600 dark:bg-neutral-700 dark:text-neutral-200">
                    <i :class="period.icon" aria-hidden="true"></i>
                </span>
            </div>
            <div class="mt-4 h-1 rounded-full bg-teal-700"></div>
        </article>
    </section>

    <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-neutral-700 dark:bg-neutral-800 dark:shadow-black/20">
        <header class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-100 px-5 py-4 dark:border-neutral-700">
            <div class="flex items-center gap-3">
                <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-teal-50 text-lg text-teal-700 dark:bg-teal-500/15 dark:text-teal-300"><i class="ri-map-pin-2-line" aria-hidden="true"></i></span>
                <h2 class="text-lg font-semibold text-slate-800 dark:text-neutral-100">{{ $t("Clients By States") }}</h2>
            </div>
            <SelectInput variant="admin" v-model="currentStatesPeriod" :aria-label="$t('Period')">
                <option value="total">{{ $t("Total") }}</option>
                <option value="day">{{ $t("Today") }}</option>
                <option value="week">{{ $t("This week") }}</option>
                <option value="month">{{ $t("This month") }}</option>
                <option value="year">{{ $t("This year") }}</option>
            </SelectInput>
        </header>

        <div class="grid gap-5 p-5 2xl:grid-cols-[minmax(0,0.8fr)_minmax(0,1.2fr)]">
            <div class="min-w-0 rounded-xl bg-slate-50 p-4 dark:bg-neutral-900/40">
                <p class="mb-3 text-sm font-medium text-slate-600 dark:text-neutral-300">{{ $t("Top 10 states") }}</p>
                <div v-if="topStateRows.length" class="h-[22rem]">
                    <Bar :data="stateChartData" :options="stateChartOptions" />
                </div>
                <p v-else class="py-8 text-center text-sm text-slate-500 dark:text-neutral-400">{{ $t("No client data available") }}</p>
            </div>

            <div class="min-w-0 overflow-x-auto">
                <table class="statistics-table">
                    <thead>
                        <tr>
                            <th>{{ $t("State") }}</th>
                            <th>{{ $t("Number of clients") }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(state, index) in stateRows" :key="`${state.state}-${index}`">
                            <td class="font-medium">{{ state.state }}</td>
                            <td>{{ state.count }}</td>
                        </tr>
                        <tr v-if="!stateRows.length">
                            <td colspan="2" class="text-center text-slate-500 dark:text-neutral-400">{{ $t("No client data available") }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </section>
</template>
