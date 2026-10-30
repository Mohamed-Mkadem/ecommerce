<script setup>
import PageHeader from "@/js/Components/Admin/PageHeader.vue";
import SelectInput from "@/js/Components/SelectInput.vue";
import { useAppearanceStore } from "@/js/stores/appearance";
import { computed, ref } from "vue";
import { Doughnut } from "vue-chartjs";
import {
    ArcElement,
    Chart as ChartJS,
    Legend,
    Tooltip,
} from "chart.js";
import { trans } from "laravel-vue-i18n";

ChartJS.register(ArcElement, Legend, Tooltip);

const props = defineProps({
    earnings: { type: Object },
    expensesCategories: { type: Object },
    revenuesCategories: { type: Object },
});

const appearanceStore = useAppearanceStore();
const currentExpenseCategory = ref("total");
const currentRevenueCategory = ref("total");
const categoryColors = ["#0f766e", "#0891b2", "#f59e0b", "#6366f1", "#e11d48"];

const summaryPeriods = [
    { key: "total", label: "Total", icon: "ri-funds-line" },
    { key: "day", label: "Today", icon: "ri-calendar-event-line" },
    { key: "week", label: "This week", icon: "ri-calendar-check-line" },
    { key: "month", label: "This month", icon: "ri-calendar-2-line" },
    { key: "year", label: "This year", icon: "ri-calendar-line" },
];

const revenueRows = computed(() =>
    Object.values(props.revenuesCategories?.[currentRevenueCategory.value] ?? {}),
);
const expenseRows = computed(() =>
    Object.values(props.expensesCategories?.[currentExpenseCategory.value] ?? {}),
);

function makeChartData(rows) {
    return {
        labels: rows.map((row) => trans(row.category)),
        datasets: [
            {
                data: rows.map((row) => Number.parseFloat(row.amount) || 0),
                backgroundColor: categoryColors,
                borderColor: appearanceStore.isDark ? "#262626" : "#ffffff",
                borderWidth: 3,
                hoverOffset: 6,
            },
        ],
    };
}

const revenueChartData = computed(() => makeChartData(revenueRows.value));
const expenseChartData = computed(() => makeChartData(expenseRows.value));

const chartOptions = computed(() => ({
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
                label(context) {
                    const amount = Number(context.parsed || 0).toFixed(3);
                    return ` ${context.label}: ${amount} ${trans("currency")}`;
                },
            },
        },
    },
}));

function formatPeriodTotal(rows) {
    return rows
        .reduce((sum, row) => sum + (Number.parseFloat(row.amount) || 0), 0)
        .toFixed(3);
}
</script>

<template>
    <Head :title="$t('Earnings statistics')" />
    <PageHeader :page-title="$t('Earnings statistics')" />

    <section class="mb-8 grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5">
        <article
            v-for="period in summaryPeriods"
            :key="period.key"
            class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md dark:border-neutral-700 dark:bg-neutral-800 dark:shadow-black/20"
        >
            <div class="flex items-start justify-between gap-3">
                <div>
                    <p class="text-sm font-medium text-slate-500 dark:text-neutral-400">{{ $t(period.label) }}</p>
                    <p
                        class="mt-2 text-xl font-semibold tracking-tight"
                        :class="{
                            'text-emerald-700 dark:text-emerald-300': Number(earnings[period.key]) > 0,
                            'text-rose-700 dark:text-rose-300': Number(earnings[period.key]) < 0,
                            'text-slate-800 dark:text-neutral-100': Number(earnings[period.key]) === 0,
                        }"
                    >
                        {{ `${earnings[period.key]} ${$t("currency")}` }}
                    </p>
                </div>
                <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-slate-100 text-lg text-slate-600 dark:bg-neutral-700 dark:text-neutral-200">
                    <i :class="period.icon" aria-hidden="true"></i>
                </span>
            </div>
            <div class="mt-4 h-1 rounded-full" :class="Number(earnings[period.key]) >= 0 ? 'bg-emerald-500' : 'bg-rose-500'"></div>
        </article>
    </section>

    <section class="grid gap-6">
        <article class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-neutral-700 dark:bg-neutral-800 dark:shadow-black/20">
            <header class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-100 px-5 py-4 dark:border-neutral-700">
                <div class="flex items-center gap-3">
                    <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-50 text-lg text-emerald-700 dark:bg-emerald-500/15 dark:text-emerald-300"><i class="ri-arrow-down-circle-line" aria-hidden="true"></i></span>
                    <h2 class="text-lg font-semibold text-slate-800 dark:text-neutral-100">{{ $t("Revenues by categories") }}</h2>
                </div>
                <SelectInput variant="admin" v-model="currentRevenueCategory" :aria-label="$t('Period')">
                    <option value="total">{{ $t("Total") }}</option>
                    <option value="day">{{ $t("Today") }}</option>
                    <option value="week">{{ $t("This week") }}</option>
                    <option value="month">{{ $t("This month") }}</option>
                    <option value="year">{{ $t("This year") }}</option>
                </SelectInput>
            </header>
            <div class="grid gap-5 p-5 lg:grid-cols-[minmax(180px,0.8fr)_minmax(0,1.2fr)]">
                <div class="min-w-0 rounded-xl bg-slate-50 p-4 dark:bg-neutral-900/40">
                    <p class="text-xs font-medium text-slate-500 dark:text-neutral-400">{{ $t("Total amount") }}</p>
                    <p class="mt-1 text-lg font-semibold text-slate-800 dark:text-neutral-100">{{ `${formatPeriodTotal(revenueRows)} ${$t("currency")}` }}</p>
                    <div class="mt-3 h-52">
                        <Doughnut :data="revenueChartData" :options="chartOptions" />
                    </div>
                </div>
                <div class="min-w-0 overflow-x-auto">
                    <table class="statistics-table">
                        <thead>
                            <tr>
                                <th>{{ $t("category") }}</th>
                                <th>{{ $t("Number of invoices") }}</th>
                                <th>{{ `${$t("Total amount")} (${$t("currency")})` }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="category in revenueRows" :key="category.category">
                                <td class="text-center font-medium">{{ $t(category.category) }}</td>
                                <td class="text-center">{{ category.count }}</td>
                                <td class="whitespace-nowrap text-center">{{ `${category.amount} ${$t("currency")}` }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </article>

        <article class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-neutral-700 dark:bg-neutral-800 dark:shadow-black/20">
            <header class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-100 px-5 py-4 dark:border-neutral-700">
                <div class="flex items-center gap-3">
                    <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-rose-50 text-lg text-rose-700 dark:bg-rose-500/15 dark:text-rose-300"><i class="ri-arrow-up-circle-line" aria-hidden="true"></i></span>
                    <h2 class="text-lg font-semibold text-slate-800 dark:text-neutral-100">{{ $t("Expenses by categories") }}</h2>
                </div>
                <SelectInput variant="admin" v-model="currentExpenseCategory" :aria-label="$t('Period')">
                    <option value="total">{{ $t("Total") }}</option>
                    <option value="day">{{ $t("Today") }}</option>
                    <option value="week">{{ $t("This week") }}</option>
                    <option value="month">{{ $t("This month") }}</option>
                    <option value="year">{{ $t("This year") }}</option>
                </SelectInput>
            </header>
            <div class="grid gap-5 p-5 lg:grid-cols-[minmax(180px,0.8fr)_minmax(0,1.2fr)]">
                <div class="min-w-0 rounded-xl bg-slate-50 p-4 dark:bg-neutral-900/40">
                    <p class="text-xs font-medium text-slate-500 dark:text-neutral-400">{{ $t("Total amount") }}</p>
                    <p class="mt-1 text-lg font-semibold text-slate-800 dark:text-neutral-100">{{ `${formatPeriodTotal(expenseRows)} ${$t("currency")}` }}</p>
                    <div class="mt-3 h-52">
                        <Doughnut :data="expenseChartData" :options="chartOptions" />
                    </div>
                </div>
                <div class="min-w-0 overflow-x-auto">
                    <table class="statistics-table">
                        <thead>
                            <tr>
                                <th>{{ $t("category") }}</th>
                                <th>{{ $t("Number of invoices") }}</th>
                                <th>{{ `${$t("Total amount")} (${$t("currency")})` }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="category in expenseRows" :key="category.category">
                                <td class="text-center font-medium">{{ $t(category.category) }}</td>
                                <td class="text-center">{{ category.count }}</td>
                                <td class="whitespace-nowrap text-center">{{ `${category.amount} ${$t("currency")}` }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </article>
    </section>
</template>
