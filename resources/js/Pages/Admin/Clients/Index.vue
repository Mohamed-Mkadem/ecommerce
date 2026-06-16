<script setup>
import PageHeader from "@/js/Components/Admin/PageHeader.vue";
import Paginator from "@/js/Components/Paginator.vue";
import { useForm } from "@inertiajs/vue3";
import InputLabel from "@/js/Components/InputLabel.vue";
import TextInput from "@/js/Components/TextInput.vue";
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
                class="bg-sky-800 text-white text-center px-4 py-2 rounded-md hover:bg-opacity-75"
            >
                {{ $t("Import") }}
            </ModalLink>
        </div>
    </PageHeader>

    <div class="bg-white px-4 py-8 rounded-md shadow-1 mb-8">
        <h2 class="text-2xl mb-4 font-semibold text-graydark">
            {{ $t("Filter.title") }}
        </h2>

        <form @submit.prevent="submitForm" @reset.prevent="resetFrom">
            <div
                class="flex gap-4 flex-col sm:flex-row sm:justify-between sm:items-center"
            >
                <div class="w-full">
                    <InputLabel
                        for="search"
                        :value="$t('Filter.search_by_client_name')"
                    />

                    <TextInput
                        class="mt-1 block w-full"
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

                    <select
                        class="w-full mt-1 rounded-md focus:ring-primary focus:border-primary border-editor text-primary"
                        v-model="form.sort"
                        id="sort"
                    >
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
                    </select>
                </div>
            </div>

            <div class="flex flex-col gap-4 mt-4 lg:flex-row">
                <div class="w-full">
                    <InputLabel
                        for="phone"
                        :value="$t('Filter.search_by_client_phone')"
                    />

                    <TextInput
                        class="mt-1 block w-full"
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

                    <select
                        class="w-full mt-1 rounded-md focus:ring-primary focus:border-primary border-editor text-primary"
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
                    </select>
                </div>
            </div>
            <div class="flex flex-col gap-4 mt-4 lg:flex-row">
                <div
                    class="flex flex-col gap-4 sm:flex-row w-full sm:items-end"
                >
                    <div class="mt-1 w-full">
                        <InputLabel for="min_spent" :value="$t('min_spent')" />
                        <TextInput
                            v-model="form.minSpent"
                            id="min_spent"
                            type="number"
                            class="mt-1 w-full"
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
                            class="mt-1 w-full"
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
                            step="0.1"
                            class="mt-1 w-full"
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
                            step="0.1"
                            id="max_orders"
                            type="number"
                            class="mt-1 w-full"
                            :placeholder="$t('eg-100')"
                        />
                    </div>
                </div>
            </div>

            <div class="flex gap-4 mt-8">
                <input
                    :class="{
                        'opacity-25 !cursor-not-allowed':
                            formIsResetting || form.processing,
                    }"
                    :disabled="formIsResetting || form.processing"
                    type="Submit"
                    :value="$t('Filter.value')"
                    class="w-full sm:w-1/4 cursor-pointer rounded-lg border border-primary bg-primary py-2 px-4 font-medium text-white transition hover:bg-opacity-90"
                />
                <input
                    :disabled="formIsResetting || form.processing"
                    :class="{
                        'opacity-75 !cursor-not-allowed':
                            formIsResetting || form.processing,
                    }"
                    type="reset"
                    :value="$t('form.reset')"
                    class="w-full sm:w-1/4 cursor-pointer rounded-lg border border-bodydark bg-bodydark py-2 px-4 font-medium text-white transition hover:bg-opacity-90"
                />
            </div>
        </form>
    </div>

    <h2 class="my-6 font-medium text-sky-900 text-xl">
        {{ $t("Results") }} : {{ clients.meta.total }}
    </h2>

    <div v-if="clients.data.length">
        <div
            class="grid grid-cols-[repeat(auto-fit,_minmax(min(300px,_100%),_1fr))] gap-4"
        >
            <div
                v-for="client in clients.data"
                :key="client.id"
                class="bg-white rounded-lg shadow-3"
            >
                <div
                    class="flex p-4 rtl:justify-end items-center gap-2 border-b-2 border-gray"
                >
                    <ModalLink
                        :close-button="false"
                        :href="route('clients.edit', client)"
                        class="bg-slate-700 text-white text-xl rounded-md px-2 py-1 hover:bg-opacity-90"
                    >
                        <i class="ri-edit-line"></i>
                    </ModalLink>
                    <button
                        @click="deleteClient(client)"
                        class="bg-red-500 text-white text-xl rounded-md px-2 py-1 hover:bg-opacity-90"
                    >
                        <i class="ri-delete-bin-5-fill"></i>
                    </button>
                </div>

                <div class="py-4 px-3 text-meta-4" dir="auto">
                    <Link
                        :href="route('clients.show', client)"
                        class="text-xl font-semibold mb-2 block text-primary hover:text-slate-700"
                    >
                        {{ client.name }}
                    </Link>
                    <div class="flex text-sky-700 items-center gap-2">
                        <i class="ri-customer-service-2-fill text-lg"></i>
                        <span dir="ltr"
                            >{{ client.phone }}
                            <span v-if="client.phone2">
                                / {{ client.phone2 }}</span
                            ></span
                        >
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
            class="mt-4"
        />
    </div>
    <NotFound message="There no clients" v-else />
</template>
