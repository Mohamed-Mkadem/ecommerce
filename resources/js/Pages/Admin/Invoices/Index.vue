<script setup>
import PageHeader from "@/js/Components/Admin/PageHeader.vue";
import NotFound from "@/js/Components/NotFound.vue";
import Paginator from "@/js/Components/Paginator.vue";
import Swal from "sweetalert2";
import InputLabel from "@/js/Components/InputLabel.vue";
import TextInput from "@/js/Components/TextInput.vue";
import DatePicker from "@/js/Components/DatePicker.vue";
import { trans } from "laravel-vue-i18n";
import { router, useForm } from "@inertiajs/vue3";
import { onMounted, ref } from "vue";
import CreateNewModal from "@/js/Components/CreateNewModal.vue";
const props = defineProps(["invoices", "filters"]);
const formIsResetting = ref(false);
function deleteInvoice(invoice) {
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
            router.delete(route("invoices.destroy", invoice), {
                onSuccess: () => {
                    Swal.fire({
                        title: trans("Dialog.deletedTitle"),
                        text: trans("Invoice.deleted"),
                        icon: "success",
                        confirmButtonText: trans("OK"),
                    });
                },
            });
        }
    });
}

const initialFormValues = {
    search: null,
    minCreationDate: null,
    maxCreationDate: null,
    minAmount: null,
    maxAmount: null,
    sort: "newest",
    type: null,
    category: null,
};

const form = useForm({
    ...initialFormValues,
});

function submitForm() {
    form.get(route("invoices.index"), {
        preserveState: true,
        preserveScroll: true,
        replace: true,
    });
}
function resetFrom() {
    formIsResetting.value = true;

    Object.assign(form, initialFormValues);

    submitForm();

    clearFlatpickrInputs();

    formIsResetting.value = false;
}
function clearFlatpickrInputs() {
    const flatpickrInputs = document.querySelectorAll(".flatpickr-input");

    flatpickrInputs.forEach((input) => {
        if (input._flatpickr) {
            input._flatpickr.clear();
        }
    });
}
onMounted(() => {
    Object.assign(form, props.filters);
});
</script>

<template>
    <Head :title="$t('Invoices')" />

    <PageHeader :page-title="$t('Invoices')">
        <CreateNewModal
            :label="$t('Invoice.new')"
            href="invoices.create"
            :close-button="false"
        >
        </CreateNewModal>
    </PageHeader>

    <!-- Filters Start -->
    <div class="bg-white px-4 py-8 rounded-md shadow-1 mb-8">
        <h2 class="text-2xl mb-4 font-semibold text-graydark">
            {{ $t("Filter.title") }}
        </h2>

        <form @submit.prevent="submitForm" @reset.prevent="resetFrom">
            <div
                class="flex gap-4 flex-col sm:flex-row sm:justify-between sm:items-center"
            >
                <div class="w-full">
                    <InputLabel for="search" :value="$t('Filter.search')" />

                    <TextInput
                        class="mt-1 block w-full"
                        id="search"
                        type="text"
                        v-model="form.search"
                        :required="false"
                        :placeholder="$t('Filter.search_placeholder')"
                    />
                </div>
                <div class="w-full">
                    <InputLabel for="sort" :value="$t('Filter.sort')" />

                    <select
                        class="w-full mt-1 rounded-md focus:ring-primary focus:border-primary border-editor text-primary"
                        v-model="form.sort"
                        id="sort"
                    >
                        <option value="newest">
                            {{ $t("Sort.newest") }}
                        </option>
                        <option value="oldest">
                            {{ $t("Sort.oldest") }}
                        </option>
                        <option value="highest_amount">
                            {{ $t("Sort.highest_amount") }}
                        </option>
                        <option value="lowest_amount">
                            {{ $t("Sort.lowest_amount") }}
                        </option>
                    </select>
                </div>
            </div>
            <div
                class="flex gap-4 mt-4 flex-col sm:flex-row sm:justify-between sm:items-center"
            >
                <div class="w-full">
                    <InputLabel for="type" :value="$t('Type')" />

                    <select
                        class="w-full mt-1 rounded-md focus:ring-primary focus:border-primary border-editor text-primary"
                        v-model="form.type"
                        id="type"
                    >
                        <option :value="null">{{ $t("All") }}</option>
                        <option value="revenue">
                            {{ $t("revenue") }}
                        </option>
                        <option value="expense">
                            {{ $t("expense") }}
                        </option>
                    </select>
                </div>
                <div class="w-full">
                    <InputLabel for="category" :value="$t('Category')" />

                    <select
                        class="w-full mt-1 rounded-md focus:ring-primary focus:border-primary border-editor text-primary"
                        v-model="form.category"
                        id="category"
                    >
                        <option :value="null">{{ $t("All") }}</option>
                        <option value="payments">
                            {{ $t("payments") }}
                        </option>
                        <option value="daily sales">
                            {{ $t("daily sales") }}
                        </option>
                        <option value="marketing">
                            {{ $t("marketing") }}
                        </option>
                        <option value="operating costs">
                            {{ $t("operating costs") }}
                        </option>
                        <option value="other">
                            {{ $t("other") }}
                        </option>
                    </select>
                </div>
            </div>

            <div class="flex flex-col gap-4 mt-4 lg:flex-row">
                <div class="flex flex-col gap-4 sm:flex-row w-full">
                    <div class="mt-1 w-full">
                        <InputLabel
                            for="min_amount"
                            :value="$t('Minimum Amount')"
                        />
                        <TextInput
                            v-model="form.minAmount"
                            id="min_amount"
                            type="number"
                            class="mt-1 w-full"
                            :placeholder="$t('in DT')"
                            step="0.01"
                        />
                    </div>
                    <div class="mt-1 w-full">
                        <InputLabel
                            for="max_amount"
                            :value="$t('Maximum Amount')"
                        />
                        <TextInput
                            v-model="form.maxAmount"
                            id="max_amount"
                            type="number"
                            class="mt-1 w-full"
                            :placeholder="$t('in DT')"
                            step="0.01"
                        />
                    </div>
                </div>
            </div>

            <div class="flex flex-col gap-4 mt-4 sm:flex-row">
                <div class="w-full">
                    <InputLabel
                        for="min_creationDate"
                        :value="`${$t('From')} ${$t('Creation_date')}`"
                    />

                    <DatePicker
                        id="min_creationDate"
                        v-model="form.minCreationDate"
                        :disabled="false"
                    />
                </div>
                <div class="w-full">
                    <InputLabel
                        for="max_creationDate"
                        :value="`${$t('To')} ${$t('Creation_date')}`"
                    />

                    <DatePicker
                        id="max_creationDate"
                        v-model="form.maxCreationDate"
                        :disabled="false"
                    />
                </div>
            </div>

            <div class="flex gap-4 mt-8">
                <input
                    :disabled="formIsResetting || form.processing"
                    :class="{
                        'opacity-25 !cursor-not-allowed':
                            formIsResetting || form.processing,
                    }"
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
    <!-- Filters End -->

    <div v-if="invoices.data.length">
        <div
            class="grid grid-cols-[repeat(auto-fit,_minmax(min(300px,_100%),_1fr))] gap-4"
        >
            <div
                class="bg-white rounded p-4 shadow-1"
                v-for="invoice in invoices.data"
                :key="invoice.id"
            >
                <div class="flex items-center justify-between gap-3 py-1">
                    <p
                        class="text-white px-4 py-1 font-medium capitalize rounded"
                        :class="{
                            'bg-red-500': invoice.type == 'expense',
                            'bg-green-600': invoice.type == 'revenue',
                        }"
                    >
                        {{ $t(invoice.type) }}
                    </p>
                    <div class="flex rtl:justify-end items-center gap-2">
                        <ModalLink
                            :close-button="false"
                            :href="route('invoices.show', invoice)"
                            class="bg-slate-500 text-white text-xl rounded-md px-2 py-1 hover:bg-opacity-90"
                        >
                            <i class="ri-eye-line"></i>
                        </ModalLink>
                        <ModalLink
                            v-if="
                                invoice.invoiceable_type ==
                                'App\\Models\\Invoiceable'
                            "
                            :close-button="false"
                            :href="route('invoices.edit', invoice)"
                            class="bg-slate-700 text-white text-xl rounded-md px-2 py-1 hover:bg-opacity-90"
                        >
                            <i class="ri-edit-line"></i>
                        </ModalLink>
                        <button
                            v-if="
                                invoice.invoiceable_type ==
                                'App\\Models\\Invoiceable'
                            "
                            @click="deleteInvoice(invoice)"
                            class="bg-red-500 text-white text-xl rounded-md px-2 py-1 hover:bg-opacity-90"
                        >
                            <i class="ri-delete-bin-5-fill"></i>
                        </button>
                    </div>
                </div>
                <div class="my-4 text-primary text-xl">
                    <p dir="auto" class="font-medium">{{ invoice.title }}</p>
                    <p class="">{{ $t(invoice.category) }}</p>
                    <p class="text-sky-700 font-medium">
                        {{ `${invoice.amount} ${$t("currency")}` }}
                    </p>
                </div>
                <div class="flex gap-2 items-center pt-4 border-t border-gray">
                    <i class="ri-calendar-line text-2xl text-slate-400"></i>

                    <p class="text-primary">{{ invoice.created_at }}</p>
                </div>
            </div>
        </div>

        <Paginator
            class="mt-8"
            :links="invoices.meta.links"
            :previous="invoices.links.prev"
            :next="invoices.links.next"
        />
    </div>
    <NotFound v-else />
</template>
