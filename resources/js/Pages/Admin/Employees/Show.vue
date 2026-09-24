<script setup>
import PageHeader from "@/js/Components/Admin/PageHeader.vue";
import NotFound from "@/js/Components/NotFound.vue";
import Paginator from "@/js/Components/Paginator.vue";
import Swal from "sweetalert2";
import { trans } from "laravel-vue-i18n";
import { router } from "@inertiajs/vue3";
import Activities from "@/js/Components/Admin/Activities.vue";
import DatePicker from "@/js/Components/DatePicker.vue";
import { ref, watch, computed } from "vue";
import { Pie } from "vue-chartjs";
import { Chart as ChartJS, Title, Tooltip, Legend, ArcElement } from "chart.js";

ChartJS.register(Title, Tooltip, Legend, ArcElement);

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

const pieChartOptions = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: {
            position: "bottom",
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
};

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
        <div class="flex rtl:justify-end items-center gap-2" v-if="employee.data.id != $page.props.auth.user.id">
            <ModalLink :close-button="false" :href="route('employees.edit', employee.data)"
                class="bg-slate-700 text-white text-xl rounded-md px-2 py-1 hover:bg-opacity-90">
                <i class="ri-edit-line"></i>
            </ModalLink>
            <button @click="deleteEmployee(employee.data)"
                class="bg-red-500 text-white text-xl rounded-md px-2 py-1 hover:bg-opacity-90">
                <i class="ri-delete-bin-5-fill"></i>
            </button>
        </div>
    </PageHeader>

    <div
        class="bg-white rounded-lg shadow-1 p-4 grid justify-center gap-8 md:grid-cols-[200px_1fr] md:gap-4 md:items-center">
        <img :src="`${$page.props.base_url}storage/${employee.data.avatar}`" class="max-w-75 md:w-50 rounded-full" />
        <div class="text-center md:text-start">
            <p class="text-white px-3 py-1 font-medium capitalize rounded text-base w-max mx-auto mb-2 md:mx-0" :class="{
                'bg-red-500': employee.data.status == 'banned',
                'bg-green-600': employee.data.status == 'active',
            }">
                {{ $t(`user.${employee.data.status}`) }}
            </p>
            <h2 class="text-2xl font-bold text-sky-800">
                {{ employee.data.full_name }}
            </h2>

            <div class="text-slate-600 flex items-center justify-center gap-1 md:justify-start md:text-xl text-lg">
                <i class="ri-admin-fill"></i>
                <span>{{ $t(employee.data.role) }}</span>
            </div>
            <div class="text-slate-600 flex items-center justify-center gap-1 md:justify-start md:text-xl text-lg">
                <i class="ri-mail-fill"></i>
                <span>{{ employee.data.email }}</span>
            </div>
            <div class="text-slate-600 flex items-center justify-center gap-1 md:justify-start md:text-xl text-lg">
                <i class="ri-calendar-fill"></i>
                <span>{{ employee.data.created_at }}</span>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-1 p-6 my-6">
        <div class="flex flex-col md:flex-row md:items-center justify-between mb-6 gap-4">
            <h3 class="text-xl font-bold text-slate-800">
                <i class="ri-bar-chart-box-line mr-2"></i>
                {{ $t("Order Actions") }}
            </h3>
            <div class="grid sm:grid-cols-[1fr,20px,1fr] items-center gap-2">
                <div class="w-full">
                    <DatePicker v-model="dateFilters.start_date" />
                </div>
                <span class="text-slate-500">{{ $t("to") }}</span>
                <div class="w-full">
                    <DatePicker v-model="dateFilters.end_date" />
                </div>
            </div>
        </div>

        <div class="grid grid-cols-[repeat(auto-fit,minmax(200px,1fr))] gap-4">
            <div
                class="cursor-pointer bg-slate-50 rounded-lg p-4 text-center border border-slate-100 shadow-sm transition hover:shadow-md">
                <div class="text-slate-500 text-sm font-semibold mb-1">
                    <i class="ri-shopping-cart-fill me-2"></i>{{ $t("Created") }}
                </div>
                <div class="text-2xl font-bold text-slate-800">
                    {{ stats["order.created"] || 0 }}
                </div>
            </div>
            <div
                class="cursor-pointer bg-blue-50 rounded-lg p-4 text-center border border-blue-100 shadow-sm transition hover:shadow-md">
                <div class="text-blue-500 text-sm font-semibold mb-1">
                    <i class="ri-checkbox-fill me-2"></i>{{ $t("Confirmed") }}
                </div>
                <div class="text-2xl font-bold text-blue-800">
                    {{ stats["order.confirmed"] || 0 }}
                </div>
            </div>
            <div
                class="cursor-pointer bg-indigo-50 rounded-lg p-4 text-center border border-indigo-100 shadow-sm transition hover:shadow-md">
                <div class="text-indigo-500 text-sm font-semibold mb-1">
                    <i class="ri-truck-fill me-2"></i>{{ $t("Shipped") }}
                </div>
                <div class="text-2xl font-bold text-indigo-800">
                    {{ stats["order.shipped"] || 0 }}
                </div>
            </div>
            <div
                class="cursor-pointer bg-green-50 rounded-lg p-4 text-center border border-green-100 shadow-sm transition hover:shadow-md">
                <div class="text-green-500 text-sm font-semibold mb-1">
                    <i class="ri-home-9-fill me-2"></i>{{ $t("Delivered") }}
                </div>
                <div class="text-2xl font-bold text-green-800">
                    {{ stats["order.delivered"] || 0 }}
                </div>
            </div>
            <div
                class="cursor-pointer bg-red-50 rounded-lg p-4 text-center border border-red-100 shadow-sm transition hover:shadow-md">
                <div class="text-red-500 text-sm font-semibold mb-1">
                    <i class="ri-close-circle-fill me-2"></i>{{ $t("Canceled") }}
                </div>
                <div class="text-2xl font-bold text-red-800">
                    {{ stats["order.canceled"] || 0 }}
                </div>
            </div>
            <div
                class="cursor-pointer bg-orange-50 rounded-lg p-4 text-center border border-orange-100 shadow-sm transition hover:shadow-md">
                <div class="text-orange-500 text-sm font-semibold mb-1">
                    <i class="ri-arrow-go-back-line me-2"></i>{{ $t("Returned") }}
                </div>
                <div class="text-2xl font-bold text-orange-800">
                    {{ stats["order.returned"] || 0 }}
                </div>
            </div>
            <div
                class="cursor-pointer bg-gray-50 rounded-lg p-4 text-center border border-gray-200 shadow-sm transition hover:shadow-md">
                <div class="text-gray-500 text-sm font-semibold mb-1">
                    <i class="ri-time-line me-2"></i>{{ $t("NRP") }}
                </div>
                <div class="text-2xl font-bold text-gray-800">
                    {{ stats["order.nrp"] || 0 }}
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-1 p-6 my-6 grid md:grid-cols-2 gap-6 items-center">
        <div>
            <h3 class="text-xl font-bold text-slate-800 mb-2">
                <i class="ri-pie-chart-2-line mr-2"></i>
                {{ $t("Rates Overview") }}
            </h3>
            <p class="text-slate-500 text-sm mb-6">
                {{
                    $t(
                        "A visual breakdown of confirmation, cancellation, and NRP rates.",
                    )
                }}
            </p>
        </div>
        <div class="max-w-sm mx-auto h-72">
            <Pie :data="pieChartData" :options="pieChartOptions" />
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-1 p-6 my-6 grid md:grid-cols-2 gap-6 items-center">
        <div>
            <h3 class="text-xl font-bold text-slate-800 mb-2">
                <i class="ri-pie-chart-2-line mr-2"></i>
                {{ $t("Delivery Rate") }}
            </h3>
            <p class="text-slate-500 text-sm mb-6">
                {{
                    $t(
                        "A visual breakdown of delivered vs returned orders confirmed by this employee.",
                    )
                }}
            </p>
        </div>
        <div class="max-w-sm mx-auto h-72">
            <Pie :data="deliveryPieChartData" :options="pieChartOptions" />
        </div>
    </div>

    <Activities :activities="activities" />
</template>
