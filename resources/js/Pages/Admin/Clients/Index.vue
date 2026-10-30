<script setup>
import PageHeader from "@/js/Components/Admin/PageHeader.vue";
import Paginator from "@/js/Components/Paginator.vue";
import { useForm } from "@inertiajs/vue3";
import InputLabel from "@/js/Components/InputLabel.vue";
import TextInput from "@/js/Components/TextInput.vue";
import SelectInput from "@/js/Components/SelectInput.vue";
import ResultsCounter from "@/js/Components/Admin/ResultsCounter.vue";
import NotFound from "@/js/Components/NotFound.vue";
import { onMounted, ref } from "vue";
import CreateNewModal from "@/js/Components/CreateNewModal.vue";
import Swal from "sweetalert2";
import { trans } from "laravel-vue-i18n";
import { router } from "@inertiajs/vue3";
const props = defineProps({
    clients: {
        type: Object,
    },
    filters: {
        type: Object,
    },
    states: {
        type: Object,
    },
});
const formIsResetting = ref(false);
const initialFormValues = {
    search: "",
    phone: "",
    minOrdersCount: null,
    maxOrdersCount: null,
    minSpent: null,
    maxSpent: null,
    state: null,
    sort: "highest_spent",
    minDeliveryRate: null,
    maxDeliveryRate: null,
};
const form = useForm({ ...initialFormValues });
function submitForm() {
    form.get(route("clients.index"), {
        preserveState: true,
        preserveScroll: true,
        replace: true,
    });
}
function resetFrom() {
    formIsResetting.value = true;
    Object.assign(form, initialFormValues);
    submitForm();
    formIsResetting.value = false;
}
onMounted(() => {
    Object.assign(form, props.filters);
});

function deleteClient(client) {
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
            router.delete(route("clients.destroy", client), {
                onSuccess: () => {
                    Swal.fire({
                        title: trans("Dialog.deletedTitle"),
                        text: trans("Client.deleted"),
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
    <Head :title="$t('Clients')" />
    <PageHeader :page-title="$t('Clients')">
        <div class="flex flex-wrap gap-4 justify-center md:justify-end">
            <CreateNewModal
                href="clients.create"
                :close-button="false"
                :label="$t('New Client')"
            />
            <ModalLink
                :href="route('clients.import.create')"
                :close-button="false"
                class="inline-flex items-center justify-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2.5 font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50 dark:border-neutral-700 dark:bg-neutral-800 dark:text-neutral-100 dark:hover:bg-neutral-700"
            >
                <i class="ri-upload-2-line" aria-hidden="true"></i>
                {{ $t("Import") }}
            </ModalLink>
        </div>
    </PageHeader>

    <section
        class="mb-6 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6 dark:border-neutral-700 dark:bg-neutral-800 dark:shadow-black/20"
    >
        <div
            class="mb-6 flex items-start gap-3 border-b border-slate-100 pb-4 dark:border-neutral-700"
        >
            <span
                class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-slate-100 text-lg text-slate-600 dark:bg-neutral-700 dark:text-neutral-200"
                ><i class="ri-filter-3-line" aria-hidden="true"></i
            ></span>
            <h2
                class="pt-1.5 text-lg font-semibold tracking-tight text-slate-800 dark:text-neutral-100"
            >
                {{ $t("Filter.title") }}
            </h2>
        </div>

        <form
            @submit.prevent="submitForm"
            @reset.prevent="resetFrom"
            class="text-slate-700 dark:text-neutral-200"
        >
            <div class="grid gap-4 md:grid-cols-2">
                <div class="w-full">
                    <InputLabel
                        for="search"
                        :value="$t('Filter.search_by_client_name')"
                    />

                    <TextInput
                        variant="admin"
                        id="search"
                        type="text"
                        v-model="form.search"
                        :required="false"
                        :placeholder="
                            $t('Filter.search_by_client_name_placeholder')
                        "
                    />
                </div>

                <div class="w-full">
                    <InputLabel for="sort" :value="$t('Filter.sort')" />

                    <SelectInput variant="admin" v-model="form.sort" id="sort">
                        <option value="highest_spent">
                            {{ $t("Sort.highest_spent") }}
                        </option>
                        <option value="lowest_spent">
                            {{ $t("Sort.lowest_spent") }}
                        </option>
                        <option value="highest_orders">
                            {{ $t("Sort.highest_orders") }}
                        </option>
                        <option value="lowest_orders">
                            {{ $t("Sort.lowest_orders") }}
                        </option>
                        <option value="highest_delivery_rate">
                            {{ $t("Sort.highest_delivery_rate") }}
                        </option>
                        <option value="lowest_delivery_rate">
                            {{ $t("Sort.lowest_delivery_rate") }}
                        </option>
                    </SelectInput>
                </div>
            </div>

            <div class="mt-4 grid gap-4 md:grid-cols-2">
                <div class="w-full">
                    <InputLabel
                        for="phone"
                        :value="$t('Filter.search_by_client_phone')"
                    />

                    <TextInput
                        variant="admin"
                        id="phone"
                        type="text"
                        v-model="form.phone"
                        :required="false"
                        :placeholder="
                            $t('Filter.search_by_client_phone_placeholder')
                        "
                    />
                </div>
                <div class="w-full">
                    <InputLabel for="state_id" :value="$t('State')" />

                    <SelectInput
                        variant="admin"
                        id="state_id"
                        v-model="form.state"
                    >
                        <option :value="null">{{ $t("All") }}</option>
                        <option
                            :value="state.id"
                            v-for="state in props.states"
                            :key="state.id"
                        >
                            {{ state.name }}
                        </option>
                    </SelectInput>
                </div>
            </div>
            <div class="mt-4 grid gap-4 xl:grid-cols-2">
                <div
                    class="flex flex-col gap-4 sm:flex-row w-full sm:items-end"
                >
                    <div class="mt-1 w-full">
                        <InputLabel for="min_spent" :value="$t('min_spent')" />
                        <TextInput
                            v-model="form.minSpent"
                            id="min_spent"
                            type="number"
                            min="0"
                            variant="admin"
                            :placeholder="$t('in DT')"
                            step="0.01"
                        />
                    </div>
                    <div class="mt-1 w-full">
                        <InputLabel for="max_spent" :value="$t('max_spent')" />
                        <TextInput
                            v-model="form.maxSpent"
                            id="max_spent"
                            type="number"
                            min="0"
                            variant="admin"
                            :placeholder="$t('in DT')"
                            step="0.01"
                        />
                    </div>
                </div>
                <div
                    class="flex flex-col gap-4 sm:flex-row w-full sm:items-end"
                >
                    <div class="w-full mt-1 gap-2">
                        <InputLabel
                            for="min_orders"
                            :value="$t('min_orders_count')"
                        />
                        <TextInput
                            v-model="form.minOrdersCount"
                            id="min_orders"
                            type="number"
                            min="0"
                            step="1"
                            variant="admin"
                            :placeholder="$t('eg-50')"
                        />
                    </div>
                    <div class="w-full mt-1 gap-2">
                        <InputLabel
                            for="max_orders"
                            :value="$t('max_orders_count')"
                        />
                        <TextInput
                            v-model="form.maxOrdersCount"
                            step="1"
                            id="max_orders"
                            min="0"
                            type="number"
                            variant="admin"
                            :placeholder="$t('eg-100')"
                        />
                    </div>
                </div>
            </div>
            <div class="mt-4 grid gap-4 md:grid-cols-2">
                <div class="w-full">
                    <InputLabel
                        for="min_delivery_rate"
                        :value="$t('Min Client Delivery Rate')"
                    />

                    <TextInput
                        v-model="form.minDeliveryRate"
                        id="min_delivery_rate"
                        type="number"
                        variant="admin"
                        placeholder="e.g., 0"
                        min="0"
                        max="100"
                        step="0.01"
                    />
                </div>
                <div class="w-full">
                    <InputLabel
                        for="max_delivery_rate"
                        :value="$t('Max Client Delivery Rate')"
                    />

                    <TextInput
                        v-model="form.maxDeliveryRate"
                        id="max_delivery_rate"
                        type="number"
                        variant="admin"
                        placeholder="e.g., 100"
                        min="0"
                        max="100"
                        step="0.01"
                    />
                </div>
            </div>
            <div
                class="mt-6 flex flex-col gap-3 border-t border-slate-100 pt-5 sm:flex-row dark:border-neutral-700"
            >
                <input
                    :class="{
                        'opacity-25 !cursor-not-allowed':
                            formIsResetting || form.processing,
                    }"
                    :disabled="formIsResetting || form.processing"
                    type="Submit"
                    :value="$t('Filter.value')"
                    class="w-full cursor-pointer rounded-xl border border-teal-700 bg-teal-700 px-4 py-2.5 font-semibold text-white transition hover:border-teal-800 hover:bg-teal-800 sm:w-44"
                />
                <input
                    :disabled="formIsResetting || form.processing"
                    :class="{
                        'opacity-75 !cursor-not-allowed':
                            formIsResetting || form.processing,
                    }"
                    type="reset"
                    :value="$t('form.reset')"
                    class="w-full cursor-pointer rounded-xl border border-slate-200 bg-slate-100 px-4 py-2.5 font-semibold text-slate-700 transition hover:bg-slate-200 dark:border-neutral-600 dark:bg-neutral-700 dark:text-neutral-100 dark:hover:bg-neutral-600 sm:w-44"
                />
            </div>
        </form>
    </section>

    <ResultsCounter :count="clients.meta.total" icon="ri-user-3-line" />

    <div v-if="clients.data.length">
        <div
            class="grid grid-cols-[repeat(auto-fit,_minmax(min(300px,_100%),_1fr))] gap-4"
        >
            <div
                v-for="client in clients.data"
                :key="client.id"
                class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-neutral-700 dark:bg-neutral-800 dark:shadow-black/20"
            >
                <div
                    class="flex items-center justify-end gap-2 border-b border-slate-100 p-4 dark:border-neutral-700"
                >
                    <ModalLink
                        :close-button="false"
                        :href="route('clients.edit', client)"
                        class="inline-flex h-9 w-9 items-center justify-center rounded-lg bg-slate-100 text-slate-600 transition hover:bg-slate-200 dark:bg-neutral-700 dark:text-neutral-200 dark:hover:bg-neutral-600"
                    >
                        <i class="ri-edit-line"></i>
                    </ModalLink>
                    <button
                        @click="deleteClient(client)"
                        class="inline-flex h-9 w-9 items-center justify-center rounded-lg bg-red-50 text-red-600 transition hover:bg-red-100 dark:bg-red-500/15 dark:text-red-300 dark:hover:bg-red-500/25"
                    >
                        <i class="ri-delete-bin-5-fill"></i>
                    </button>
                </div>

                <div
                    class="px-4 py-4 text-slate-600 dark:text-neutral-300"
                    dir="auto"
                >
                    <Link
                        :href="route('clients.show', client)"
                        class="mb-2 block text-lg font-semibold text-slate-800 transition hover:text-teal-700 dark:text-neutral-100 dark:hover:text-teal-300"
                    >
                        {{ client.name }}
                        <span
                            v-if="
                                client.delivery_rate !== null &&
                                client.delivery_rate !== undefined
                            "
                            v-tippy="$t('client.delivery_rate_tooltip')"
                            :title="$t('client.delivery_rate_tooltip')"
                            class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-semibold"
                            :class="{
                                'bg-green-100 text-green-800':
                                    client.delivery_rate >= 70,
                                'bg-yellow-100 text-yellow-800':
                                    client.delivery_rate >= 40 &&
                                    client.delivery_rate < 70,
                                'bg-red-100 text-red-800':
                                    client.delivery_rate < 40,
                            }"
                        >
                            <i
                                class="ri-truck-line text-neutral-500 dark:text-neutral-400"
                            ></i>
                            {{ client.delivery_rate }}%
                        </span>
                    </Link>
                    <div
                        class="flex items-center gap-2 text-slate-500 dark:text-neutral-400"
                    >
                        <i
                            class="ri-customer-service-2-fill text-lg text-neutral-500 dark:text-neutral-400"
                        ></i>
                        <span dir="ltr"
                            >{{ client.phone }}
                            <span v-if="client.phone2">
                                / {{ client.phone2 }}</span
                            ></span
                        >
                    </div>

                    <div
                        class="mb-1 flex items-center gap-2 text-slate-500 dark:text-neutral-400"
                    >
                        <i
                            class="ri-map-pin-line text-lg text-neutral-500 dark:text-neutral-400"
                        ></i>
                        <span>{{ client.state.name }}</span>
                    </div>
                    <p dir="auto">
                        {{ client.address }}
                    </p>
                </div>
                <div
                    class="grid grid-cols-2 items-center border-t border-slate-100 p-3 dark:border-neutral-700"
                >
                    <div class="text-center">
                        <i
                            class="ri-shopping-cart-line text-2xl text-slate-500 dark:text-neutral-400"
                        ></i>
                        <p
                            class="font-semibold text-slate-700 dark:text-neutral-200"
                        >
                            {{ client.orders_count }}
                        </p>
                    </div>
                    <div class="text-center">
                        <i
                            class="ri-money-dollar-box-line text-2xl text-slate-500 dark:text-neutral-400"
                        ></i>
                        <p
                            class="font-semibold text-slate-700 dark:text-neutral-200"
                        >
                            {{ client.spent }}
                            {{ $t("currency") }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
        <Paginator
            :links="clients.meta.links"
            :previous="clients.links.prev"
            :next="clients.links.next"
            variant="admin"
            class="mt-4"
        />
    </div>
    <NotFound message="There no clients" v-else />
</template>
