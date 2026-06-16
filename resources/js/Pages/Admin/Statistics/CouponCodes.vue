<script setup>
import PageHeader from "@/js/Components/Admin/PageHeader.vue";
import { ref } from "vue";
import Paginator from "@/js/Components/Paginator.vue";
import NotFound from "@/js/Components/NotFound.vue";
const props = defineProps({
    count: { type: Object },
    usageStatistics: { type: Object },
    statistics: { type: Object },
});
const currentStatisticsPeriod = ref("total");
</script>

<template>
    <Head :title="$t('Coupon Codes statistics')" />
    <PageHeader :page-title="$t('Coupon Codes statistics')"> </PageHeader>

    <div
        class="grid xsm:grid-cols-[repeat(auto-fit,minmax(min(150px,100%),1fr))] gap-2 lg:gap-4 mb-2 lg-mb-4"
    >
        <div class="bg-white p-4 rounded-md shadow-5">
            <p class="text-neutral-700 ltr:-tracking-tighter">
                {{ $t("Total") }}
            </p>

            <div class="flex justify-between items-end">
                <p class="font-semibold text-lg text-sky-800">
                    {{ count["total"] }}
                </p>
                <div
                    class="text-white flex items-center justify-center w-9 h-9 rounded-md bg-sky-800"
                >
                    <i class="ri-advertisement-line"></i>
                </div>
            </div>
        </div>
        <div class="bg-white p-4 rounded-md shadow-5">
            <p class="text-neutral-700 ltr:-tracking-tighter">
                {{ $t("Today") }}
            </p>

            <div class="flex justify-between items-end">
                <p class="font-semibold text-lg text-sky-800">
                    {{ count["day"] }}
                </p>
                <div
                    class="text-white flex items-center justify-center w-9 h-9 rounded-md bg-sky-800"
                >
                    <i class="ri-advertisement-line"></i>
                </div>
            </div>
        </div>
        <div class="bg-white p-4 rounded-md shadow-5">
            <p class="text-neutral-700 ltr:-tracking-tighter">
                {{ $t("This week") }}
            </p>

            <div class="flex justify-between items-end">
                <p class="font-semibold text-lg text-sky-800">
                    {{ count["week"] }}
                </p>
                <div
                    class="text-white flex items-center justify-center w-9 h-9 rounded-md bg-sky-800"
                >
                    <i class="ri-advertisement-line"></i>
                </div>
            </div>
        </div>
        <div class="bg-white p-4 rounded-md shadow-5">
            <p class="text-neutral-700 ltr:-tracking-tighter">
                {{ $t("This month") }}
            </p>

            <div class="flex justify-between items-end">
                <p class="font-semibold text-lg text-sky-800">
                    {{ count["month"] }}
                </p>
                <div
                    class="text-white flex items-center justify-center w-9 h-9 rounded-md bg-sky-800"
                >
                    <i class="ri-advertisement-line"></i>
                </div>
            </div>
        </div>
        <div class="bg-white p-4 rounded-md shadow-5">
            <p class="text-neutral-700 ltr:-tracking-tighter">
                {{ $t("This year") }}
            </p>

            <div class="flex justify-between items-end">
                <p class="font-semibold text-lg text-sky-800">
                    {{ count["year"] }}
                </p>
                <div
                    class="text-white flex items-center justify-center w-9 h-9 rounded-md bg-sky-800"
                >
                    <i class="ri-advertisement-line"></i>
                </div>
            </div>
        </div>
    </div>

    <div
        class="grid xsm:grid-cols-[repeat(auto-fit,minmax(min(150px,100%),1fr))] gap-2 lg:gap-4"
    >
        <div class="bg-white p-4 rounded-md shadow-5">
            <p class="text-neutral-700 ltr:-tracking-tighter capitalize">
                {{ $t("active") }}
            </p>

            <div class="flex justify-between items-end">
                <p class="font-semibold text-lg text-sky-800">
                    {{ count["active"] }}
                </p>
                <div
                    class="text-white flex items-center justify-center w-9 h-9 rounded-md bg-green-600"
                >
                    <i class="ri-check-double-line"></i>
                </div>
            </div>
        </div>
        <div class="bg-white p-4 rounded-md shadow-5">
            <p class="text-neutral-700 ltr:-tracking-tighter capitalize">
                {{ $t("inactive") }}
            </p>

            <div class="flex justify-between items-end">
                <p class="font-semibold text-lg text-sky-800">
                    {{ count["inactive"] }}
                </p>
                <div
                    class="text-white flex items-center justify-center w-9 h-9 rounded-md bg-red-600"
                >
                    <i class="ri-eye-off-line"></i>
                </div>
            </div>
        </div>
    </div>

    <section class="my-12">
        <div
            class="bg-white rounded-lg p-4 shadow-1 md:flex md:justify-between md:items-center md:gap-3 md:flex-wrap mb-4"
        >
            <h2 class="text-sky-800 font-semibold text-lg">
                {{ $t("Discounted Orders Percentage") }}
            </h2>
        </div>
        <div class="relative overflow-x-auto shadow-md rounded-lg">
            <table class="w-full text-left rtl:text-right text-slate-800">
                <thead class="text-neutral-100 bg-sky-800 p-4">
                    <tr
                        class="grid grid-cols-[repeat(2,minmax(250px,1fr))] items-center text-center"
                    >
                        <th scope="col" class="px-6 py-4 text-start">
                            {{ $t("Period") }}
                        </th>
                        <th scope="col" class="px-6 py-4">
                            {{ $t("Percentage") }}
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr
                        class="odd:bg-zinc-200 even:bg-white grid grid-cols-[repeat(2,minmax(250px,1fr))] items-center text-center"
                    >
                        <td
                            class="px-6 py-4 text-start flex items-center gap-2"
                        >
                            {{ $t("Total") }}
                        </td>
                        <td class="px-6 py-4">
                            {{ `${usageStatistics["total"]}%` }}
                        </td>
                    </tr>
                    <tr
                        class="odd:bg-zinc-200 even:bg-white grid grid-cols-[repeat(2,minmax(250px,1fr))] items-center text-center"
                    >
                        <td
                            class="px-6 py-4 text-start flex items-center gap-2"
                        >
                            {{ $t("Today") }}
                        </td>
                        <td class="px-6 py-4">
                            {{ `${usageStatistics["day"]}%` }}
                        </td>
                    </tr>
                    <tr
                        class="odd:bg-zinc-200 even:bg-white grid grid-cols-[repeat(2,minmax(250px,1fr))] items-center text-center"
                    >
                        <td
                            class="px-6 py-4 text-start flex items-center gap-2"
                        >
                            {{ $t("This week") }}
                        </td>
                        <td class="px-6 py-4">
                            {{ `${usageStatistics["week"]}%` }}
                        </td>
                    </tr>
                    <tr
                        class="odd:bg-zinc-200 even:bg-white grid grid-cols-[repeat(2,minmax(250px,1fr))] items-center text-center"
                    >
                        <td
                            class="px-6 py-4 text-start flex items-center gap-2"
                        >
                            {{ $t("This month") }}
                        </td>
                        <td class="px-6 py-4">
                            {{ `${usageStatistics["month"]}%` }}
                        </td>
                    </tr>
                    <tr
                        class="odd:bg-zinc-200 even:bg-white grid grid-cols-[repeat(2,minmax(250px,1fr))] items-center text-center"
                    >
                        <td
                            class="px-6 py-4 text-start flex items-center gap-2"
                        >
                            {{ $t("This year") }}
                        </td>
                        <td class="px-6 py-4">
                            {{ `${usageStatistics["year"]}%` }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </section>
    <section class="my-12">
        <div
            class="bg-white rounded-lg p-4 shadow-1 md:flex md:justify-between md:items-center md:gap-3 md:flex-wrap mb-4"
        >
            <h2 class="text-sky-800 font-semibold text-lg">
                {{ $t("Coupon code by orders statuses") }}
            </h2>

            <select
                v-model="currentStatisticsPeriod"
                class="w-full md:w-max min-w-20 mt-4 md:mt-0 rounded-md focus:ring-primary focus:border-primary border-editor text-primary"
            >
                <option value="total">{{ $t("Total") }}</option>
                <option value="day">{{ $t("Today") }}</option>
                <option value="week">{{ $t("This week") }}</option>
                <option value="month">{{ $t("This month") }}</option>
                <option value="year">{{ $t("This year") }}</option>
            </select>
        </div>
        <div v-if="statistics.data.length">
            <div
                class="grid grid-cols-[repeat(auto-fit,minmax(350px,_1fr))] gap-4 items-center"
            >
                <div
                    v-for="code in statistics.data"
                    :key="code.id"
                    class="bg-white rounded-md shadow-1 p-4"
                >
                    <div
                        class="flex items-center justify-between gap-2 flex-wrap border-b border-slate-200 pb-2"
                    >
                        <p
                            class="text-white px-3 py-1 font-medium rounded-md capitalize"
                            :class="{
                                'bg-green-600': code.status == 'active',
                                'bg-red-600': code.status == 'inactive',
                            }"
                        >
                            {{ $t(code.status) }}
                        </p>
                        <p class="text-slate-500 text-xl">
                            {{ `${code.value}%` }}
                        </p>
                    </div>
                    <p
                        class="my-8 text-xl text-center font-semibold text-sky-700"
                    >
                        {{ code.code }}
                    </p>
                    <div class="p-2 border-t-2 border-gray mt-3">
                        <div
                            class="text-center text-white bg-sky-800 p-3 rounded-md mb-2"
                        >
                            <p class="font-semibold">
                                {{
                                    code.count[currentStatisticsPeriod]
                                        .orders_count
                                }}
                            </p>

                            <div class="flex items-center gap-2 justify-center">
                                <i class="ri-shopping-cart-fill text-xl"></i>

                                <p>{{ $t("Total") }}</p>
                            </div>
                        </div>
                        <div
                            class="grid items-center grid-cols-[repeat(auto-fit,_minmax(min(200px,_100%),_1fr))] gap-2"
                        >
                            <div
                                class="text-center bg-yellow-200 text-orange-800 p-3 rounded-md"
                            >
                                <p class="font-semibold">
                                    {{
                                        code.count[currentStatisticsPeriod]
                                            .pending_orders_count
                                    }}
                                </p>

                                <div
                                    class="flex items-center gap-2 justify-center"
                                >
                                    <i class="ri-hourglass-fill text-xl"></i>

                                    <p>{{ $t("pending") }}</p>
                                </div>
                            </div>
                            <div
                                class="text-center bg-red-200 text-red-800 p-3 rounded-md"
                            >
                                <p class="font-semibold">
                                    {{
                                        code.count[currentStatisticsPeriod]
                                            .canceled_orders_count
                                    }}
                                </p>

                                <div
                                    class="flex items-center gap-2 justify-center"
                                >
                                    <i class="ri-close-circle-fill text-xl"></i>

                                    <p>{{ $t("canceled") }}</p>
                                </div>
                            </div>

                            <div
                                class="text-center bg-green-200 text-green-800 p-3 rounded-md"
                            >
                                <p class="font-semibold">
                                    {{
                                        code.count[currentStatisticsPeriod]
                                            .confirmed_orders_count
                                    }}
                                </p>

                                <div
                                    class="flex items-center gap-2 justify-center"
                                >
                                    <i class="ri-checkbox-fill text-xl"></i>

                                    <p>{{ $t("confirmed") }}</p>
                                </div>
                            </div>
                            <div
                                class="text-center bg-purple-200 text-purple-800 p-3 rounded-md"
                            >
                                <p class="font-semibold">
                                    {{
                                        code.count[currentStatisticsPeriod]
                                            .shipped_orders_count
                                    }}
                                </p>

                                <div
                                    class="flex items-center gap-2 justify-center"
                                >
                                    <i class="ri-truck-fill text-xl"></i>

                                    <p>{{ $t("shipped") }}</p>
                                </div>
                            </div>
                            <div
                                class="text-center bg-blue-200 text-blue-800 p-3 rounded-md"
                            >
                                <p class="font-semibold">
                                    {{
                                        code.count[currentStatisticsPeriod]
                                            .delivered_orders_count
                                    }}
                                </p>

                                <div
                                    class="flex items-center gap-2 justify-center"
                                >
                                    <i class="ri-home-9-fill text-xl"></i>

                                    <p>{{ $t("delivered") }}</p>
                                </div>
                            </div>
                            <div
                                class="text-center bg-zinc-200 text-zinc-700 p-3 rounded-md"
                            >
                                <p class="font-semibold">
                                    {{
                                        code.count[currentStatisticsPeriod]
                                            .returned_orders_count
                                    }}
                                </p>

                                <div
                                    class="flex items-center gap-2 justify-center"
                                >
                                    <i class="ri-truck-fill text-xl"></i>

                                    <p>{{ $t("returned") }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <Paginator
                :links="statistics.meta.links"
                :previous="statistics.links.prev"
                :next="statistics.links.next"
                class="mt-4"
            />
        </div>
        <div v-else>
            <NotFound />
        </div>
    </section>
</template>
