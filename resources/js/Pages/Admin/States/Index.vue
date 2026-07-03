<script setup>
import PageHeader from "@/js/Components/Admin/PageHeader.vue";
import NotFound from "@/js/Components/NotFound.vue";
const props = defineProps(["states", "acceptance_dates"]);
</script>

<template>
    <Head :title="$t('Shipping')" />

    <PageHeader :page-title="$t('Shipping')"> </PageHeader>

    <section class="mb-4">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-sky-800 text-xl font-semibold">
                {{ $t("Orders acceptance date") }}
            </h2>
            <ModalLink
                :close-button="false"
                :href="route('shippingSettings.edit', acceptance_dates.id)"
                class="bg-slate-700 text-white text-xl rounded-md px-2 py-1 hover:bg-opacity-90"
            >
                {{ $t("Edit") }}
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
    <section>
        <h2 class="text-sky-800 text-xl font-semibold mb-4">
            {{ $t("States") }}
        </h2>

        <div
            v-if="states.data.length"
            class="relative overflow-x-auto shadow-md sm:rounded-lg"
        >
            <table class="w-full text-left rtl:text-right text-slate-800">
                <thead class="text-gray bg-graydark p-4">
                    <tr
                        class="grid grid-cols-[50px_200px_200px_1fr_1fr_1fr_120px] items-center text-center"
                    >
                        <th scope="col" class="px-6 py-4">
                            {{ $t("ID") }}
                        </th>
                        <th scope="col" class="px-6 py-4">
                            {{ $t("Name") }}
                        </th>
                        <th scope="col" class="px-6 py-4">
                            {{ $t("Shipping.cost") }}
                        </th>
                        <th scope="col" class="px-6 py-4">
                            {{ $t("delivery_cost") }}
                        </th>
                        <th scope="col" class="px-6 py-4">
                            {{ $t("return_cost") }}
                        </th>

                        <th scope="col" class="px-6 py-4">
                            {{ $t("Default Shipper") }}
                        </th>

                        <th scope="col" class="px-6 py-4">
                            {{ $t("Actions") }}
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr
                        v-for="(state, index) in states.data"
                        :key="index"
                        class="odd:bg-slate-200 even:bg-white grid grid-cols-[50px_200px_200px_1fr_1fr_1fr_120px] items-center text-center"
                    >
                        <th
                            scope="row"
                            class="px-6 py-4 font-medium text-primary whitespace-nowrap"
                        >
                            {{ `#${state.id}` }}
                        </th>

                        <td class="px-6 py-4">{{ state.name }}</td>

                        <td class="px-6 py-4">
                            {{ `${state.shipping_cost} ${$t("currency")}` }}
                        </td>
                        <td class="px-6 py-4">
                            {{ `${state.delivery_cost} ${$t("currency")}` }}
                        </td>
                        <td class="px-6 py-4">
                            {{ `${state.return_cost} ${$t("currency")}` }}
                        </td>
                        <td class="px-6 py-4">
                            {{
                                `${state.default_shipper ? state.default_shipper.name : "Not Set"}`
                            }}
                        </td>

                        <td
                            class="px-6 py-4 flex items-center gap-3 justify-center"
                        >
                            <ModalLink
                                :close-button="false"
                                :href="route('states.edit', state)"
                                class="bg-slate-700 text-white text-xl rounded-md px-2 py-1 hover:bg-opacity-90"
                            >
                                <i class="ri-edit-line"></i>
                            </ModalLink>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <NotFound v-else />
    </section>
</template>
