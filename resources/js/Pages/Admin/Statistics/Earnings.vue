<script setup>
import PageHeader from "@/js/Components/Admin/PageHeader.vue";
import { ref } from "vue";
const props = defineProps({
    earnings: { type: Object },
    expensesCategories: { type: Object },
    revenuesCategories: { type: Object },
});

const currentExpenseCategory = ref("total");
const currentRevenueCategory = ref("total");
</script>

<template>
    <Head :title="$t('Earnings statistics')" />
    <PageHeader :page-title="$t('Earnings statistics')"> </PageHeader>
    <div
        class="grid xsm:grid-cols-[repeat(auto-fit,minmax(min(150px,100%),1fr))] gap-2 lg:gap-4"
    >
        <div class="bg-white p-4 rounded-md shadow-5">
            <div class="flex mb-2 justify-end">
                <div
                    class="text-white flex items-center justify-center w-9 h-9 rounded-md mb-1"
                    :class="{
                        'bg-green-500': earnings['total'] > 0,
                        'bg-red-500': earnings['total'] < 0,
                        'bg-neutral-500': earnings['total'] == 0,
                    }"
                >
                    <i class="ri-money-dollar-box-line"></i>
                </div>
            </div>
            <p class="text-neutral-700 ltr:-tracking-tighter">
                {{ $t("Total") }}
            </p>

            <p class="font-semibold text-lg mt-2 text-sky-800">
                {{ `${earnings["total"]} ${$t("currency")}` }}
            </p>
        </div>
        <div class="bg-white p-4 rounded-md shadow-5">
            <div class="flex mb-2 justify-end">
                <div
                    class="text-white flex items-center justify-center w-9 h-9 rounded-md mb-1"
                    :class="{
                        'bg-green-500': earnings['day'] > 0,
                        'bg-red-500': earnings['day'] < 0,
                        'bg-neutral-500': earnings['day'] == 0,
                    }"
                >
                    <i class="ri-money-dollar-box-line"></i>
                </div>
            </div>
            <p class="text-neutral-700 ltr:-tracking-tighter">
                {{ $t("Today") }}
            </p>
            <p class="font-semibold text-lg mt-2 text-sky-800">
                {{ `${earnings["day"]} ${$t("currency")}` }}
            </p>
        </div>

        <div class="bg-white p-4 rounded-md shadow-5">
            <div class="flex mb-2 justify-end">
                <div
                    class="text-white flex items-center justify-center w-9 h-9 rounded-md mb-1"
                    :class="{
                        'bg-green-500': earnings['week'] > 0,
                        'bg-red-500': earnings['week'] < 0,
                        'bg-neutral-500': earnings['week'] == 0,
                    }"
                >
                    <i class="ri-money-dollar-box-line"></i>
                </div>
            </div>
            <p class="text-neutral-700 ltr:-tracking-tighter">
                {{ $t("This week") }}
            </p>
            <p class="font-semibold text-lg mt-2 text-sky-800">
                {{ `${earnings["week"]} ${$t("currency")}` }}
            </p>
        </div>
        <div class="bg-white p-4 rounded-md shadow-5">
            <div class="flex mb-2 justify-end">
                <div
                    class="text-white flex items-center justify-center w-9 h-9 rounded-md mb-1"
                    :class="{
                        'bg-green-500': earnings['month'] > 0,
                        'bg-red-500': earnings['month'] < 0,
                        'bg-neutral-500': earnings['month'] == 0,
                    }"
                >
                    <i class="ri-money-dollar-box-line"></i>
                </div>
            </div>
            <p class="text-neutral-700 ltr:-tracking-tighter">
                {{ $t("This month") }}
            </p>
            <p class="font-semibold text-lg mt-2 text-sky-800">
                {{ `${earnings["month"]} ${$t("currency")}` }}
            </p>
        </div>
        <div class="bg-white p-4 rounded-md shadow-5">
            <div class="flex mb-2 justify-end">
                <div
                    class="text-white flex items-center justify-center w-9 h-9 rounded-md mb-1"
                    :class="{
                        'bg-green-500': earnings['year'] > 0,
                        'bg-red-500': earnings['year'] < 0,
                        'bg-neutral-500': earnings['year'] == 0,
                    }"
                >
                    <i class="ri-money-dollar-box-line"></i>
                </div>
            </div>
            <p class="text-neutral-700 ltr:-tracking-tighter">
                {{ $t("This year") }}
            </p>
            <p class="font-semibold text-lg mt-2 text-sky-800">
                {{ `${earnings["year"]} ${$t("currency")}` }}
            </p>
        </div>
    </div>

    <section class="my-12">
        <div
            class="bg-white rounded-lg p-4 shadow-1 md:flex md:justify-between md:items-center md:gap-3 md:flex-wrap mb-4"
        >
            <h2 class="text-sky-800 font-semibold text-lg">
                {{ $t("Revenues by categories") }}
            </h2>

            <select
                v-model="currentRevenueCategory"
                class="w-full md:w-max min-w-20 mt-4 md:mt-0 rounded-md focus:ring-primary focus:border-primary border-editor text-primary"
            >
                <option value="total">{{ $t("Total") }}</option>
                <option value="day">{{ $t("Today") }}</option>
                <option value="week">{{ $t("This week") }}</option>
                <option value="month">{{ $t("This month") }}</option>
                <option value="year">{{ $t("This year") }}</option>
            </select>
        </div>
        <div class="relative overflow-x-auto shadow-md rounded-lg">
            <table class="w-full text-left rtl:text-right text-slate-800">
                <thead class="text-neutral-100 bg-sky-800 p-4">
                    <tr
                        class="grid grid-cols-[repeat(3,minmax(250px,1fr))] items-center text-center"
                    >
                        <th scope="col" class="px-6 py-4 text-start">
                            {{ $t("category") }}
                        </th>
                        <th scope="col" class="px-6 py-4">
                            {{ $t("Number of invoices") }}
                        </th>
                        <th scope="col" class="px-6 py-4">
                            {{ `${$t("Total amount")} (${$t("currency")})` }}
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr
                        v-for="(category, index) in revenuesCategories[
                            currentRevenueCategory
                        ]"
                        :key="index"
                        class="odd:bg-zinc-200 even:bg-white grid grid-cols-[repeat(3,minmax(250px,1fr))] items-center text-center"
                    >
                        <td class="px-6 py-4 text-start">
                            {{ $t(category.category) }}
                        </td>
                        <td class="px-6 py-4">{{ category.count }}</td>
                        <td class="px-6 py-4">
                            {{ `${category.amount} ${$t("currency")}` }}
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
                {{ $t("Expenses by categories") }}
            </h2>

            <select
                v-model="currentExpenseCategory"
                class="w-full md:w-max min-w-20 mt-4 md:mt-0 rounded-md focus:ring-primary focus:border-primary border-editor text-primary"
            >
                <option value="total">{{ $t("Total") }}</option>
                <option value="day">{{ $t("Today") }}</option>
                <option value="week">{{ $t("This week") }}</option>
                <option value="month">{{ $t("This month") }}</option>
                <option value="year">{{ $t("This year") }}</option>
            </select>
        </div>
        <div class="relative overflow-x-auto shadow-md rounded-lg">
            <table class="w-full text-left rtl:text-right text-slate-800">
                <thead class="text-neutral-100 bg-sky-800 p-4">
                    <tr
                        class="grid grid-cols-[repeat(3,minmax(250px,1fr))] items-center text-center"
                    >
                        <th scope="col" class="px-6 py-4 text-start">
                            {{ $t("category") }}
                        </th>
                        <th scope="col" class="px-6 py-4">
                            {{ $t("Number of invoices") }}
                        </th>
                        <th scope="col" class="px-6 py-4">
                            {{ `${$t("Total amount")} (${$t("currency")})` }}
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr
                        v-for="(category, index) in expensesCategories[
                            currentExpenseCategory
                        ]"
                        :key="index"
                        class="odd:bg-zinc-200 even:bg-white grid grid-cols-[repeat(3,minmax(250px,1fr))] items-center text-center"
                    >
                        <td class="px-6 py-4 text-start">
                            {{ $t(category.category) }}
                        </td>
                        <td class="px-6 py-4">{{ category.count }}</td>
                        <td class="px-6 py-4">
                            {{ `${category.amount} ${$t("currency")}` }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </section>
</template>
