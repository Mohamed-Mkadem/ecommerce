<script setup>
import PageHeader from "@/js/Components/Admin/PageHeader.vue";
import CreateNew from "@/js/Components/CreateNew.vue";
import Status from "@/js/Components/Status.vue";
import { computed } from "vue";
import { trans } from "laravel-vue-i18n";
import { Bar } from "vue-chartjs";
import {
    Chart as ChartJS,
    CategoryScale,
    LinearScale,
    BarElement,
    Title,
    Tooltip,
    Legend,
} from "chart.js";

ChartJS.register(
    CategoryScale,
    LinearScale,
    BarElement,
    Title,
    Tooltip,
    Legend,
);

const props = defineProps({
    todaysEarnings: { type: Number },
    todaysInvoicesCount: { type: Number },
    todaysOrdersCount: { type: Number },
    todaysClientsCount: { type: Number },
    todaysNotificationsCount: { type: Number },
    orders: { type: Object },
    weeklyOrders: { type: Array },
    weeklyClientOrders: { type: Array },
    clients: { type: Object },
    acceptance_dates: { type: Object },
});

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

    <section class="my-12" v-if="orders.data.length">
        <div
            class="flex items-center justify-between gap-3 flex-wrap bg-white p-3 shadow-1 mb-4"
        >
            <h2 class="text-sky-800 font-semibold text-lg">
                {{ $t("Latest Orders") }}
            </h2>
            <Link
                :href="route('orders.index')"
                class="underline text-sky-800 hover:text-sky-600"
            >
                {{ $t("All Orders") }}
            </Link>
        </div>
        <div
            class="grid grid-cols-[repeat(auto-fit,_minmax(min(350px,_100%),_1fr))] gap-4"
        >
            <div
                class="bg-white rounded-md shadow-1 flex flex-col"
                v-for="order in orders.data"
                :key="order.id"
            >
                <div class="flex justify-between gap-4 items-center p-3">
                    <div class="flex gap-2 items-center">
                        <Link
                            :href="route('orders.show', order)"
                            class="text-xl underline font-bold"
                        >
                            #{{ order.id }}
                        </Link>
                        <Status :status="order.status" class="px-2 py-1" />
                    </div>

                    <Link
                        :href="route('orders.show', order)"
                        class="bg-slate-500 text-white text-xl rounded-md px-2 py-1 hover:bg-opacity-90"
                    >
                        <i class="ri-eye-line"></i>
                    </Link>
                </div>

                <div class="px-4 py-3 text-sky-700 flex-1">
                    <Link
                        v-if="order.client.deleted_at == null"
                        :href="route('clients.show', order.client)"
                        class="text-xl font-semibold mb-1 block text-primary hover:text-slate-500 hover:underline"
                    >
                        {{ order.client_name }}
                    </Link>
                    <p
                        v-else
                        class="text-xl font-semibold mb-1 block text-primary"
                    >
                        {{ order.client_name }}
                        <small class="text-red-500 font-normal text-sm">
                            ({{ $t("Deleted") }})
                        </small>
                    </p>
                    <p class="text-lg mb-1">{{ order.phone }}</p>
                    <div class="flex gap-2 items-center">
                        <p class="text-red font-semibold text-2xl">
                            {{ `${order.amount} ${$t("currency")}` }}
                        </p>
                        <p v-if="order.coupon" class="text-neutral-600">
                            {{
                                `: "${order.coupon.code}" ${$t("applied")} (${order.coupon.value}%)`
                            }}
                            <small
                                v-if="order.coupon.deleted_at != null"
                                class="text-red-500"
                            >
                                ({{ $t("Deleted") }})
                            </small>
                        </p>
                    </div>

                    <p dir="auto">{{ order.state.name }}</p>
                    <div v-if="order.shipper" class="flex items-center gap-2">
                        <i class="ri-truck-line text-2xl text-slate-400"></i>

                        <p class="text-primary">
                            {{ order.shipper.name }}
                            <small
                                v-if="order.shipper.deleted_at != null"
                                class="text-red-500"
                            >
                                ({{ $t("Deleted") }})
                            </small>
                        </p>
                    </div>
                </div>

                <div
                    class="grid grid-cols-1 sm:grid-cols-2 p-2 border-t-2 border-gray"
                >
                    <div class="text-center">
                        <p class="flex gap-2 items-center justify-center">
                            <span>{{ $t("Delivery_date") }}</span>
                            <i
                                class="ri-calendar-line text-2xl text-slate-400"
                            ></i>
                        </p>
                        <p class="text-primary">{{ order.delivery_date }}</p>
                    </div>
                    <div class="text-center">
                        <p class="flex gap-2 items-center justify-center">
                            <span>{{ $t("Placed_at") }}</span>
                            <i
                                class="ri-calendar-line text-2xl text-slate-400"
                            ></i>
                        </p>
                        <p class="text-primary">{{ order.created_at }}</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="my-12" v-if="clients.data.length">
        <div
            class="flex items-center justify-between gap-3 flex-wrap bg-white p-3 shadow-1 mb-4"
        >
            <h2 class="text-sky-800 font-semibold text-lg">
                {{ $t("Latest Clients") }}
            </h2>
            <Link
                :href="route('clients.index')"
                class="underline text-sky-800 hover:text-sky-600"
            >
                {{ $t("All Clients") }}
            </Link>
        </div>
        <div
            class="grid grid-cols-[repeat(auto-fit,_minmax(min(300px,_100%),_1fr))] gap-4"
        >
            <div
                v-for="client in clients.data"
                :key="client.id"
                class="bg-white rounded-lg shadow-3"
            >
                <div class="py-4 px-3 text-meta-4" dir="auto">
                    <Link
                        :href="route('clients.show', client)"
                        class="text-xl font-semibold mb-2 block text-primary hover:text-slate-500 underline"
                    >
                        {{ client.name }}
                    </Link>
                    <div class="flex text-sky-700 items-center gap-2">
                        <i class="ri-customer-service-2-fill text-lg"></i>
                        <span dir="ltr">{{ client.phone }}</span>
                    </div>

                    <div class="mb-1 text-sky-700 flex items-center gap-2">
                        <i class="ri-map-pin-line text-lg"></i>
                        <span>{{ client.state.name }}</span>
                    </div>
                    <p dir="auto">
                        {{ client.address }}
                    </p>
                </div>
                <div
                    class="grid items-center grid-cols-2 p-2 border-t-2 border-gray"
                >
                    <div class="text-center">
                        <i class="ri-shopping-cart-line text-2xl"></i>
                        <p class="text-primary">{{ client.orders_count }}</p>
                    </div>
                    <div class="text-center">
                        <i class="ri-money-dollar-box-line text-2xl"></i>
                        <p class="text-primary">
                            {{ client.spent }} {{ $t("currency") }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>
