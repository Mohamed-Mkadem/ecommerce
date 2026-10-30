<script setup>
import PageHeader from "@/js/Components/Admin/PageHeader.vue";
import NotFound from "@/js/Components/NotFound.vue";
import ResultsCounter from "@/js/Components/Admin/ResultsCounter.vue";
const props = defineProps(["states", "acceptance_dates"]);
</script>

<template>
    <Head :title="$t('Shipping')" />

    <PageHeader :page-title="$t('Shipping')"> </PageHeader>

    <section class="mb-6 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-neutral-700 dark:bg-neutral-800 dark:shadow-black/20 sm:p-6">
        <div class="mb-5 flex flex-wrap items-center justify-between gap-3">
            <h2 class="text-xl font-semibold text-slate-800 dark:text-neutral-100">
                {{ $t("Orders acceptance date") }}
            </h2>
            <ModalLink
                :close-button="false"
                :href="route('shippingSettings.edit', acceptance_dates.id)"
                class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50 dark:border-neutral-700 dark:bg-neutral-800 dark:text-neutral-100 dark:hover:bg-neutral-700"
            >
                <i class="ri-edit-line"></i>
                {{ $t("Edit") }}
            </ModalLink>
        </div>

        <div class="grid gap-3 sm:grid-cols-2">
            <div class="rounded-xl bg-slate-50 px-4 py-3 dark:bg-neutral-700/60">
                <h3 class="text-sm font-medium text-slate-500 dark:text-neutral-400">
                    {{ $t("Greater Tunis") }}
                </h3>
                <p class="mt-1 font-semibold text-slate-800 dark:text-neutral-100">{{ acceptance_dates.tunis_acceptance_delivery_date }}</p>
            </div>
            <div class="rounded-xl bg-slate-50 px-4 py-3 dark:bg-neutral-700/60">
                <h3 class="text-sm font-medium text-slate-500 dark:text-neutral-400">
                    {{ $t("The states") }}
                </h3>
                <p class="mt-1 font-semibold text-slate-800 dark:text-neutral-100">{{ acceptance_dates.wilayet_acceptance_delivery_date }}</p>
            </div>
        </div>
    </section>
    <section>
        <ResultsCounter :count="states.data.length" icon="ri-map-pin-line" />

        <div
            v-if="states.data.length"
            class="mt-4 overflow-x-auto rounded-2xl"
        >
            <table>
                <thead>
                    <tr>
                        <th scope="col" class="!text-center">{{ $t("ID") }}</th>
                        <th scope="col" class="!text-center">{{ $t("Name") }}</th>
                        <th scope="col" class="!text-center">{{ $t("Shipping.cost") }}</th>
                        <th scope="col" class="!text-center">{{ $t("Default Shipper") }}</th>
                        <th scope="col" class="!text-center">{{ $t("Actions") }}</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="state in states.data" :key="state.id">
                        <td class="!text-center whitespace-nowrap font-medium">{{ `#${state.id}` }}</td>
                        <td class="!text-center font-semibold">{{ state.name }}</td>
                        <td class="!text-center">
                            {{ `${state.shipping_cost} ${$t("currency")}` }}
                        </td>
                        <td class="!text-center">
                            {{
                                `${state.default_shipper ? state.default_shipper.name : "Not Set"}`
                            }}
                        </td>
                        <td class="!text-center">
                            <div class="flex items-center justify-center">
                            <ModalLink
                                :close-button="false"
                                :href="route('states.edit', state)"
                                class="inline-flex h-9 w-9 items-center justify-center rounded-lg bg-slate-100 text-slate-600 transition hover:bg-slate-200 dark:bg-neutral-700 dark:text-neutral-200 dark:hover:bg-neutral-600"
                                :aria-label="$t('Edit')"
                                :title="$t('Edit')"
                            >
                                <i class="ri-edit-line"></i>
                            </ModalLink>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <NotFound v-else />
    </section>
</template>
