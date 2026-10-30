<script setup>
import PageHeader from "@/js/Components/Admin/PageHeader.vue";
import Swal from "sweetalert2";
import { trans } from "laravel-vue-i18n";
import { router } from "@inertiajs/vue3";
import Activities from "@/js/Components/Admin/Activities.vue";
import DatePicker from "@/js/Components/DatePicker.vue";
import { ref, watch, computed } from "vue";
import { Pie } from "vue-chartjs";
import { Chart as ChartJS, Title, Tooltip, Legend, ArcElement } from "chart.js";
import { useAppearanceStore } from "@/js/stores/appearance";

ChartJS.register(Title, Tooltip, Legend, ArcElement);
const appearanceStore = useAppearanceStore();

const props = defineProps([
    "employee",
    "activities",
    "stats",
    "deliveryStats",
    "filters",
]);

const pieChartData = computed(() => ({
    labels: [trans("Confirmed"), trans("Canceled"), trans("NRP")],
    datasets: [
        {
            data: [
                props.stats["order.confirmed"] || 0,
                props.stats["order.canceled"] || 0,
                props.stats["order.nrp"] || 0,
            ],
            backgroundColor: ["#bbf7d0", "#fecaca", "#075985"],
            borderColor: ["#86efac", "#fca5a5", "#0c4a6e"],
            borderWidth: 1,
        },
    ],
}));

const pieChartOptions = computed(() => ({
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: {
            position: "bottom",
            labels: {
                color: appearanceStore.isDark ? "#d4d4d4" : "#475569",
            },
        },
        tooltip: {
            callbacks: {
                label: function (context) {
                    const label = context.label || "";
                    const value = context.parsed;
                    const total = context.dataset.data.reduce(
                        (acc, current) => acc + current,
                        0,
                    );
                    let percentage = 0;
                    if (total > 0) {
                        percentage = Math.round((value / total) * 100);
                    }
                    return `${label}: ${value} (${percentage}%)`;
                },
            },
        },
    },
}));

const deliveryPieChartData = computed(() => ({
    labels: [trans("Delivered"), trans("Returned"), trans("Other")],
    datasets: [
        {
            data: [
                props.deliveryStats["delivered"] || 0,
                props.deliveryStats["returned"] || 0,
                (props.deliveryStats["confirmed"] || 0) +
                (props.deliveryStats["shipped"] || 0) +
                (props.deliveryStats["pending"] || 0) +
                (props.deliveryStats["canceled"] || 0) +
                (props.deliveryStats["abandoned"] || 0),
            ],
            backgroundColor: ["#bbf7d0", "#fed7aa", "#e2e8f0"],
            borderColor: ["#86efac", "#fdba74", "#cbd5e1"],
            borderWidth: 1,
        },
    ],
}));

const dateFilters = ref({
    start_date: props.filters?.start_date || "",
    end_date: props.filters?.end_date || "",
});

watch(
    dateFilters,
    (newVal) => {
        router.get(
            route("employees.show", props.employee.data.id),
            {
                start_date: newVal.start_date,
                end_date: newVal.end_date,
            },
            { preserveState: true, preserveScroll: true },
        );
    },
    { deep: true },
);

function deleteEmployee(employee) {
    Swal.fire({
        title: trans("Dialog.title"),
        text: trans("Dialog.warning"),
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#d33",
        cancelButtonColor: "#3085d6",
        confirmButtonText: trans("Dialog.confirmDeletionButtonText"),
        cancelButtonText: trans("Dialog.cancelButtonText"),
        width: 450,
    }).then((result) => {
        if (result.isConfirmed) {
            router.delete(route("employees.destroy", employee), {
                onSuccess: () => {
                    Swal.fire({
                        title: trans("Dialog.deletedTitle"),
                        text: trans("Employee.deleted"),
                        icon: "success",
                        confirmButtonText: trans("OK"),
                    });
                },
            });
        }
    });
}
</script>

<template>

    <Head :title="employee.data.full_name" />

    <PageHeader :page-title="employee.data.full_name">
        <div v-if="employee.data.id != $page.props.auth.user.id" class="flex items-center gap-3">
            <ModalLink
                :close-button="false"
                :href="route('employees.edit', employee.data)"
                :title="$t('Edit')"
                :aria-label="$t('Edit')"
                class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-slate-600 text-lg text-white shadow-sm transition hover:bg-slate-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-slate-400 focus-visible:ring-offset-2 dark:focus-visible:ring-offset-neutral-900"
            >
                <i class="ri-edit-line" aria-hidden="true"></i>
            </ModalLink>
            <button
                type="button"
                @click="deleteEmployee(employee.data)"
                :title="$t('Delete')"
                :aria-label="$t('Delete')"
                class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-red-600 text-lg text-white shadow-sm transition hover:bg-red-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-red-500 focus-visible:ring-offset-2 dark:focus-visible:ring-offset-neutral-900"
            >
                <i class="ri-delete-bin-line" aria-hidden="true"></i>
            </button>
        </div>
    </PageHeader>

    <section class="grid items-center justify-center gap-6 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-neutral-700 dark:bg-neutral-800 dark:shadow-black/20 sm:p-6 lg:grid-cols-[200px_minmax(0,1fr)]">
        <img
            :src="`${$page.props.base_url}storage/${employee.data.avatar}`"
            :alt="employee.data.full_name"
            class="mx-auto h-40 w-40 rounded-full object-cover ring-4 ring-slate-100 dark:ring-neutral-700"
        />
        <div class="text-center lg:text-start">
            <p class="mx-auto mb-2 w-max rounded-lg px-3 py-1.5 text-sm font-semibold capitalize text-white lg:mx-0" :class="{
                'bg-red-500': employee.data.status == 'banned',
                'bg-green-600': employee.data.status == 'active',
            }">
                {{ $t(`user.${employee.data.status}`) }}
            </p>
            <h2 class="text-2xl font-semibold tracking-tight text-slate-800 dark:text-neutral-100">
                {{ employee.data.full_name }}
            </h2>

            <div class="mt-3 flex items-center justify-center gap-2 text-slate-600 dark:text-neutral-300 lg:justify-start">
                <i class="ri-admin-fill text-slate-400 dark:text-neutral-500" aria-hidden="true"></i>
                <span>{{ $t(employee.data.role) }}</span>
            </div>
            <div class="mt-2 flex items-center justify-center gap-2 break-all text-slate-600 dark:text-neutral-300 lg:justify-start">
                <i class="ri-mail-fill text-slate-400 dark:text-neutral-500" aria-hidden="true"></i>
                <span>{{ employee.data.email }}</span>
            </div>
            <div class="mt-2 flex items-center justify-center gap-2 text-slate-600 dark:text-neutral-300 lg:justify-start">
                <i class="ri-calendar-fill text-slate-400 dark:text-neutral-500" aria-hidden="true"></i>
                <span>{{ employee.data.created_at }}</span>
            </div>
        </div>
    </section>

    <section class="my-6 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-neutral-700 dark:bg-neutral-800 dark:shadow-black/20 sm:p-6">
        <div class="mb-6 flex flex-col justify-between gap-4 border-b border-slate-100 pb-5 dark:border-neutral-700 md:flex-row md:items-center">
            <h3 class="flex items-center gap-2 text-xl font-semibold tracking-tight text-slate-800 dark:text-neutral-100">
                <i class="ri-bar-chart-box-line text-slate-500 dark:text-neutral-400" aria-hidden="true"></i>
                {{ $t("Order Actions") }}
            </h3>
            <div class="grid items-center gap-2 sm:grid-cols-[minmax(0,1fr)_auto_minmax(0,1fr)]">
                <DatePicker variant="admin" v-model="dateFilters.start_date" />
                <span class="text-center text-sm text-slate-500 dark:text-neutral-400">{{ $t("to") }}</span>
                <DatePicker variant="admin" v-model="dateFilters.end_date" />
            </div>
        </div>

        <div class="grid grid-cols-[repeat(auto-fit,minmax(200px,1fr))] gap-4">
            <div
                class="rounded-xl border border-slate-200 bg-slate-50 p-4 text-center shadow-sm transition hover:shadow-md dark:border-neutral-700 dark:bg-neutral-700/50">
                <div class="mb-1 text-sm font-semibold text-slate-500 dark:text-neutral-400">
                    <i class="ri-shopping-cart-fill me-2"></i>{{ $t("Created") }}
                </div>
                <div class="text-2xl font-bold text-slate-800 dark:text-neutral-100">
                    {{ stats["order.created"] || 0 }}
                </div>
            </div>
            <div
                class="rounded-xl border border-blue-100 bg-blue-50 p-4 text-center shadow-sm transition hover:shadow-md dark:border-blue-900/60 dark:bg-blue-950/30">
                <div class="mb-1 text-sm font-semibold text-blue-600 dark:text-blue-300">
                    <i class="ri-checkbox-fill me-2"></i>{{ $t("Confirmed") }}
                </div>
                <div class="text-2xl font-bold text-blue-800 dark:text-blue-200">
                    {{ stats["order.confirmed"] || 0 }}
                </div>
            </div>
            <div
                class="rounded-xl border border-indigo-100 bg-indigo-50 p-4 text-center shadow-sm transition hover:shadow-md dark:border-indigo-900/60 dark:bg-indigo-950/30">
                <div class="mb-1 text-sm font-semibold text-indigo-600 dark:text-indigo-300">
                    <i class="ri-truck-fill me-2"></i>{{ $t("Shipped") }}
                </div>
                <div class="text-2xl font-bold text-indigo-800 dark:text-indigo-200">
                    {{ stats["order.shipped"] || 0 }}
                </div>
            </div>
            <div
                class="rounded-xl border border-green-100 bg-green-50 p-4 text-center shadow-sm transition hover:shadow-md dark:border-green-900/60 dark:bg-green-950/30">
                <div class="mb-1 text-sm font-semibold text-green-600 dark:text-green-300">
                    <i class="ri-home-9-fill me-2"></i>{{ $t("Delivered") }}
                </div>
                <div class="text-2xl font-bold text-green-800 dark:text-green-200">
                    {{ stats["order.delivered"] || 0 }}
                </div>
            </div>
            <div
                class="rounded-xl border border-red-100 bg-red-50 p-4 text-center shadow-sm transition hover:shadow-md dark:border-red-900/60 dark:bg-red-950/30">
                <div class="mb-1 text-sm font-semibold text-red-600 dark:text-red-300">
                    <i class="ri-close-circle-fill me-2"></i>{{ $t("Canceled") }}
                </div>
                <div class="text-2xl font-bold text-red-800 dark:text-red-200">
                    {{ stats["order.canceled"] || 0 }}
                </div>
            </div>
            <div
                class="rounded-xl border border-orange-100 bg-orange-50 p-4 text-center shadow-sm transition hover:shadow-md dark:border-orange-900/60 dark:bg-orange-950/30">
                <div class="mb-1 text-sm font-semibold text-orange-600 dark:text-orange-300">
                    <i class="ri-arrow-go-back-line me-2"></i>{{ $t("Returned") }}
                </div>
                <div class="text-2xl font-bold text-orange-800 dark:text-orange-200">
                    {{ stats["order.returned"] || 0 }}
                </div>
            </div>
            <div
                class="rounded-xl border border-slate-200 bg-slate-50 p-4 text-center shadow-sm transition hover:shadow-md dark:border-neutral-700 dark:bg-neutral-700/50">
                <div class="mb-1 text-sm font-semibold text-slate-500 dark:text-neutral-400">
                    <i class="ri-time-line me-2"></i>{{ $t("NRP") }}
                </div>
                <div class="text-2xl font-bold text-slate-800 dark:text-neutral-100">
                    {{ stats["order.nrp"] || 0 }}
                </div>
            </div>
        </div>
    </section>

    <section class="my-6 grid items-center gap-6 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-neutral-700 dark:bg-neutral-800 dark:shadow-black/20 sm:p-6 md:grid-cols-2">
        <div>
            <h3 class="mb-2 flex items-center gap-2 text-xl font-semibold tracking-tight text-slate-800 dark:text-neutral-100">
                <i class="ri-pie-chart-2-line text-slate-500 dark:text-neutral-400" aria-hidden="true"></i>
                {{ $t("Rates Overview") }}
            </h3>
            <p class="mb-6 text-sm leading-6 text-slate-500 dark:text-neutral-400">
                {{
                    $t(
                        "A visual breakdown of confirmation, cancellation, and NRP rates.",
                    )
                }}
            </p>
        </div>
        <div class="mx-auto h-72 w-full max-w-sm">
            <Pie :data="pieChartData" :options="pieChartOptions" />
        </div>
    </section>

    <section class="my-6 grid items-center gap-6 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-neutral-700 dark:bg-neutral-800 dark:shadow-black/20 sm:p-6 md:grid-cols-2">
        <div>
            <h3 class="mb-2 flex items-center gap-2 text-xl font-semibold tracking-tight text-slate-800 dark:text-neutral-100">
                <i class="ri-pie-chart-2-line text-slate-500 dark:text-neutral-400" aria-hidden="true"></i>
                {{ $t("Delivery Rate") }}
            </h3>
            <p class="mb-6 text-sm leading-6 text-slate-500 dark:text-neutral-400">
                {{
                    $t(
                        "A visual breakdown of delivered vs returned orders confirmed by this employee.",
                    )
                }}
            </p>
        </div>
        <div class="mx-auto h-72 w-full max-w-sm">
            <Pie :data="deliveryPieChartData" :options="pieChartOptions" />
        </div>
    </section>

    <Activities :activities="activities" />
</template>
