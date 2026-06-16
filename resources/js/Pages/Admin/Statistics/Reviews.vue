<script setup>
import PageHeader from "@/js/Components/Admin/PageHeader.vue";
import { ref } from "vue";

const props = defineProps({
    statistics: { type: Object },
});
const currentStatisticsPeriod = ref("total");
</script>

<template>
    <Head :title="$t('Reviews statistics')" />
    <PageHeader :page-title="$t('Reviews statistics')"> </PageHeader>

    <div
        class="grid xsm:grid-cols-[repeat(auto-fit,minmax(min(150px,100%),1fr))] gap-2 lg:gap-4"
    >
        <div class="bg-white p-4 rounded-md shadow-5">
            <p class="text-neutral-700 ltr:-tracking-tighter">
                {{ $t("Total") }}
            </p>

            <div class="flex justify-between items-end">
                <p class="font-semibold text-lg text-sky-800">
                    {{ statistics["total"]["total"] }}
                </p>
                <div
                    class="text-white flex items-center justify-center w-9 h-9 rounded-md bg-sky-800"
                >
                    <i class="ri-star-line"></i>
                </div>
            </div>
        </div>
        <div class="bg-white p-4 rounded-md shadow-5">
            <p class="text-neutral-700 ltr:-tracking-tighter">
                {{ $t("Today") }}
            </p>

            <div class="flex justify-between items-end">
                <p class="font-semibold text-lg text-sky-800">
                    {{ statistics["total"]["day"] }}
                </p>
                <div
                    class="text-white flex items-center justify-center w-9 h-9 rounded-md bg-sky-800"
                >
                    <i class="ri-star-line"></i>
                </div>
            </div>
        </div>
        <div class="bg-white p-4 rounded-md shadow-5">
            <p class="text-neutral-700 ltr:-tracking-tighter">
                {{ $t("This week") }}
            </p>

            <div class="flex justify-between items-end">
                <p class="font-semibold text-lg text-sky-800">
                    {{ statistics["total"]["week"] }}
                </p>
                <div
                    class="text-white flex items-center justify-center w-9 h-9 rounded-md bg-sky-800"
                >
                    <i class="ri-star-line"></i>
                </div>
            </div>
        </div>
        <div class="bg-white p-4 rounded-md shadow-5">
            <p class="text-neutral-700 ltr:-tracking-tighter">
                {{ $t("This month") }}
            </p>

            <div class="flex justify-between items-end">
                <p class="font-semibold text-lg text-sky-800">
                    {{ statistics["total"]["month"] }}
                </p>
                <div
                    class="text-white flex items-center justify-center w-9 h-9 rounded-md bg-sky-800"
                >
                    <i class="ri-star-line"></i>
                </div>
            </div>
        </div>
        <div class="bg-white p-4 rounded-md shadow-5">
            <p class="text-neutral-700 ltr:-tracking-tighter">
                {{ $t("This year") }}
            </p>

            <div class="flex justify-between items-end">
                <p class="font-semibold text-lg text-sky-800">
                    {{ statistics["total"]["year"] }}
                </p>
                <div
                    class="text-white flex items-center justify-center w-9 h-9 rounded-md bg-sky-800"
                >
                    <i class="ri-star-line"></i>
                </div>
            </div>
        </div>
    </div>

    <section class="my-12">
        <div
            class="bg-white rounded-lg p-4 shadow-1 md:flex md:justify-between md:items-center md:gap-3 md:flex-wrap mb-4"
        >
            <h2 class="text-sky-800 font-semibold text-lg">
                {{ $t("Reviews By Stars") }}
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
        <div class="relative overflow-x-auto shadow-md rounded-lg">
            <table class="w-full text-left rtl:text-right text-slate-800">
                <thead class="text-neutral-100 bg-sky-800 p-4">
                    <tr
                        class="grid grid-cols-[repeat(2,minmax(250px,1fr))] items-center text-center"
                    >
                        <th scope="col" class="px-6 py-4 text-start">
                            {{ $t("Stars") }}
                        </th>
                        <th scope="col" class="px-6 py-4">
                            {{ $t("Number of reviews") }}
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr
                        class="odd:bg-zinc-200 even:bg-white grid grid-cols-[repeat(2,minmax(250px,1fr))] items-center text-center"
                    >
                        <td
                            class="px-6 py-4 text-start text-xl flex items-center gap-2"
                        >
                            <i class="ri-star-line text-slate-400"></i>
                            5
                        </td>
                        <td class="px-6 py-4">
                            {{ statistics["five"][currentStatisticsPeriod] }}
                        </td>
                    </tr>
                    <tr
                        class="odd:bg-zinc-200 even:bg-white grid grid-cols-[repeat(2,minmax(250px,1fr))] items-center text-center"
                    >
                        <td
                            class="px-6 py-4 text-start text-xl flex items-center gap-2"
                        >
                            <i class="ri-star-line text-slate-400"></i>
                            4
                        </td>
                        <td class="px-6 py-4">
                            {{ statistics["four"][currentStatisticsPeriod] }}
                        </td>
                    </tr>
                    <tr
                        class="odd:bg-zinc-200 even:bg-white grid grid-cols-[repeat(2,minmax(250px,1fr))] items-center text-center"
                    >
                        <td
                            class="px-6 py-4 text-start text-xl flex items-center gap-2"
                        >
                            <i class="ri-star-line text-slate-400"></i>
                            3
                        </td>
                        <td class="px-6 py-4">
                            {{ statistics["three"][currentStatisticsPeriod] }}
                        </td>
                    </tr>
                    <tr
                        class="odd:bg-zinc-200 even:bg-white grid grid-cols-[repeat(2,minmax(250px,1fr))] items-center text-center"
                    >
                        <td
                            class="px-6 py-4 text-start text-xl flex items-center gap-2"
                        >
                            <i class="ri-star-line text-slate-400"></i>
                            2
                        </td>
                        <td class="px-6 py-4">
                            {{ statistics["two"][currentStatisticsPeriod] }}
                        </td>
                    </tr>
                    <tr
                        class="odd:bg-zinc-200 even:bg-white grid grid-cols-[repeat(2,minmax(250px,1fr))] items-center text-center"
                    >
                        <td
                            class="px-6 py-4 text-start text-xl flex items-center gap-2"
                        >
                            <i class="ri-star-line text-slate-400"></i>
                            1
                        </td>
                        <td class="px-6 py-4">
                            {{ statistics["one"][currentStatisticsPeriod] }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </section>
</template>
