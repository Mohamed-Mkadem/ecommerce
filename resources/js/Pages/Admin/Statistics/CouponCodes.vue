<script setup>
import PageHeader from "@/js/Components/Admin/PageHeader.vue";
import ResultsCounter from "@/js/Components/Admin/ResultsCounter.vue";
import SelectInput from "@/js/Components/SelectInput.vue";
import Paginator from "@/js/Components/Paginator.vue";
import NotFound from "@/js/Components/NotFound.vue";
import { computed, ref } from "vue";

const props = defineProps({
    count: { type: Object },
    usageStatistics: { type: Object },
    statistics: { type: Object },
});

const currentStatisticsPeriod = ref("total");
const expandedCouponIds = ref(new Set());

const summaryPeriods = [
    { key: "total", label: "Total", icon: "ri-coupon-3-line" },
    { key: "day", label: "Today", icon: "ri-calendar-event-line" },
    { key: "week", label: "This week", icon: "ri-calendar-check-line" },
    { key: "month", label: "This month", icon: "ri-calendar-2-line" },
    { key: "year", label: "This year", icon: "ri-calendar-line" },
];

const statusSummary = [
    {
        key: "active",
        label: "active",
        icon: "ri-checkbox-circle-line",
        color: "bg-emerald-50 text-emerald-800 dark:bg-emerald-500/15 dark:text-emerald-200",
    },
    {
        key: "inactive",
        label: "inactive",
        icon: "ri-eye-off-line",
        color: "bg-rose-50 text-rose-800 dark:bg-rose-500/15 dark:text-rose-200",
    },
];

const usageRows = computed(() =>
    summaryPeriods.map((period) => ({
        ...period,
        percentage: Number(props.usageStatistics?.[period.key]) || 0,
    })),
);

const couponCodes = computed(() => props.statistics?.data ?? []);

const orderStatuses = [
    {
        key: "pending_orders_count",
        label: "pending",
        icon: "ri-hourglass-fill",
        color: "bg-amber-50 text-amber-800 dark:bg-amber-500/15 dark:text-amber-200",
    },
    {
        key: "canceled_orders_count",
        label: "canceled",
        icon: "ri-close-circle-fill",
        color: "bg-rose-50 text-rose-800 dark:bg-rose-500/15 dark:text-rose-200",
    },
    {
        key: "confirmed_orders_count",
        label: "confirmed",
        icon: "ri-checkbox-fill",
        color: "bg-emerald-50 text-emerald-800 dark:bg-emerald-500/15 dark:text-emerald-200",
    },
    {
        key: "shipped_orders_count",
        label: "shipped",
        icon: "ri-truck-fill",
        color: "bg-violet-50 text-violet-800 dark:bg-violet-500/15 dark:text-violet-200",
    },
    {
        key: "delivered_orders_count",
        label: "delivered",
        icon: "ri-home-9-fill",
        color: "bg-sky-50 text-sky-800 dark:bg-sky-500/15 dark:text-sky-200",
    },
    {
        key: "returned_orders_count",
        label: "returned",
        icon: "ri-truck-fill",
        color: "bg-slate-100 text-slate-700 dark:bg-neutral-700 dark:text-neutral-200",
    },
];

function toggleCouponDetails(id) {
    const expanded = new Set(expandedCouponIds.value);

    if (expanded.has(id)) {
        expanded.delete(id);
    } else {
        expanded.add(id);
    }

    expandedCouponIds.value = expanded;
}
</script>

<template>
    <Head :title="$t('Coupon Codes statistics')" />
    <PageHeader :page-title="$t('Coupon Codes statistics')" />

    <section class="mb-6 grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5">
        <article
            v-for="period in summaryPeriods"
            :key="period.key"
            class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md dark:border-neutral-700 dark:bg-neutral-800 dark:shadow-black/20"
        >
            <div class="flex items-start justify-between gap-3">
                <div>
                    <p class="text-sm font-medium text-slate-500 dark:text-neutral-400">{{ $t(period.label) }}</p>
                    <p class="mt-2 text-xl font-semibold tracking-tight text-slate-800 dark:text-neutral-100">
                        {{ count[period.key] }}
                    </p>
                </div>
                <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-slate-100 text-lg text-slate-600 dark:bg-neutral-700 dark:text-neutral-200">
                    <i :class="period.icon" aria-hidden="true"></i>
                </span>
            </div>
            <div class="mt-4 h-1 rounded-full bg-teal-700"></div>
        </article>
    </section>

    <section class="mb-6 grid gap-3 sm:grid-cols-2">
        <article
            v-for="status in statusSummary"
            :key="status.key"
            class="flex items-center justify-between gap-4 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm dark:border-neutral-700 dark:bg-neutral-800 dark:shadow-black/20"
        >
            <div>
                <p class="text-sm font-medium capitalize text-slate-500 dark:text-neutral-400">{{ $t(status.label) }}</p>
                <p class="mt-1 text-2xl font-semibold text-slate-800 dark:text-neutral-100">{{ count[status.key] }}</p>
            </div>
            <span class="flex h-11 w-11 items-center justify-center rounded-xl text-xl" :class="status.color">
                <i :class="status.icon" aria-hidden="true"></i>
            </span>
        </article>
    </section>

    <section class="mb-8 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-neutral-700 dark:bg-neutral-800 dark:shadow-black/20">
        <header class="flex items-center gap-3 border-b border-slate-100 px-5 py-4 dark:border-neutral-700">
            <div class="flex items-center gap-3">
                <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-teal-50 text-lg text-teal-700 dark:bg-teal-500/15 dark:text-teal-300"><i class="ri-percent-line" aria-hidden="true"></i></span>
                <h2 class="text-lg font-semibold text-slate-800 dark:text-neutral-100">{{ $t("Discounted Orders Percentage") }}</h2>
            </div>
        </header>
        <div class="p-5">
            <div class="overflow-x-auto">
                <table class="statistics-table">
                    <thead>
                        <tr>
                            <th>{{ $t("Period") }}</th>
                            <th>{{ $t("Percentage") }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="period in usageRows" :key="period.key">
                            <td class="font-medium">{{ $t(period.label) }}</td>
                            <td>
                                <span class="inline-flex min-w-20 justify-center rounded-lg bg-teal-50 px-3 py-1.5 font-semibold text-teal-800 dark:bg-teal-500/15 dark:text-teal-200">
                                    {{ `${period.percentage}%` }}
                                </span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-neutral-700 dark:bg-neutral-800 dark:shadow-black/20">
        <header class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-100 px-5 py-4 dark:border-neutral-700">
            <div class="flex items-center gap-3">
                <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-violet-50 text-lg text-violet-700 dark:bg-violet-500/15 dark:text-violet-300"><i class="ri-coupon-3-line" aria-hidden="true"></i></span>
                <h2 class="text-lg font-semibold text-slate-800 dark:text-neutral-100">{{ $t("Coupon code by orders statuses") }}</h2>
            </div>
            <SelectInput variant="admin" v-model="currentStatisticsPeriod" :aria-label="$t('Period')">
                <option value="total">{{ $t("Total") }}</option>
                <option value="day">{{ $t("Today") }}</option>
                <option value="week">{{ $t("This week") }}</option>
                <option value="month">{{ $t("This month") }}</option>
                <option value="year">{{ $t("This year") }}</option>
            </SelectInput>
        </header>

        <div class="p-5">
            <ResultsCounter :count="statistics.meta.total" />
            <div v-if="couponCodes.length">
                <div class="overflow-x-auto">
                    <table class="statistics-table">
                        <thead>
                            <tr>
                                <th>{{ $t("Code") }}</th>
                                <th>{{ $t("Status") }}</th>
                                <th>{{ $t("Discount") }}</th>
                                <th>{{ $t("Orders") }}</th>
                                <th>{{ $t("Actions") }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            <template v-for="code in couponCodes" :key="code.id">
                                <tr>
                                    <td>
                                        <span class="font-mono font-semibold tracking-wide text-slate-800 dark:text-neutral-100">{{ code.code }}</span>
                                    </td>
                                    <td>
                                        <span
                                            class="rounded-full px-3 py-1 text-xs font-semibold capitalize"
                                            :class="code.status === 'active' ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-500/15 dark:text-emerald-200' : 'bg-rose-100 text-rose-800 dark:bg-rose-500/15 dark:text-rose-200'"
                                        >
                                            {{ $t(code.status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="rounded-lg bg-violet-50 px-3 py-1.5 font-semibold text-violet-800 dark:bg-violet-500/15 dark:text-violet-200">
                                            {{ `${code.value}%` }}
                                        </span>
                                    </td>
                                    <td class="font-semibold">{{ code.count[currentStatisticsPeriod].orders_count }}</td>
                                    <td>
                                        <button
                                            type="button"
                                            class="inline-flex items-center gap-2 rounded-lg bg-slate-100 px-3 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-200 dark:bg-neutral-700 dark:text-neutral-200 dark:hover:bg-neutral-600"
                                            :aria-expanded="expandedCouponIds.has(code.id)"
                                            :aria-label="`${$t('Order breakdown')}: ${code.code}`"
                                            @click="toggleCouponDetails(code.id)"
                                        >
                                            {{ $t("Order breakdown") }}
                                            <i :class="expandedCouponIds.has(code.id) ? 'ri-arrow-up-s-line' : 'ri-arrow-down-s-line'" aria-hidden="true"></i>
                                        </button>
                                    </td>
                                </tr>
                                <tr v-if="expandedCouponIds.has(code.id)">
                                    <td colspan="5" class="!text-start">
                                        <div class="grid grid-cols-[repeat(auto-fit,minmax(115px,1fr))] gap-2 py-1">
                                            <div
                                                v-for="status in orderStatuses"
                                                :key="status.key"
                                                class="rounded-xl px-3 py-2.5 text-center"
                                                :class="status.color"
                                            >
                                                <p class="text-lg font-semibold leading-tight">{{ code.count[currentStatisticsPeriod][status.key] }}</p>
                                                <div class="mt-1 flex items-center justify-center gap-1.5 text-xs font-medium">
                                                    <i :class="status.icon" aria-hidden="true"></i>
                                                    <span>{{ $t(status.label) }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
                <Paginator
                    :links="statistics.meta.links"
                    :previous="statistics.links.prev"
                    :next="statistics.links.next"
                    class="mt-4"
                />
            </div>
            <NotFound v-else />
        </div>
    </section>
</template>
