<script setup>
import PageHeader from "@/js/Components/Admin/PageHeader.vue";
import NotFound from "@/js/Components/NotFound.vue";
import Paginator from "@/js/Components/Paginator.vue";
import Swal from "sweetalert2";
import InputLabel from "@/js/Components/InputLabel.vue";
import TextInput from "@/js/Components/TextInput.vue";
import SelectInput from "@/js/Components/SelectInput.vue";
import DatePicker from "@/js/Components/DatePicker.vue";
import ResultsCounter from "@/js/Components/Admin/ResultsCounter.vue";
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
    <section class="mb-6 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6 dark:border-neutral-700 dark:bg-neutral-800 dark:shadow-black/20">
        <div class="mb-6 flex items-start gap-3 border-b border-slate-100 pb-4 dark:border-neutral-700">
            <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-slate-100 text-lg text-slate-600 dark:bg-neutral-700 dark:text-neutral-200"><i class="ri-filter-3-line" aria-hidden="true"></i></span>
            <h2 class="pt-1.5 text-lg font-semibold tracking-tight text-slate-800 dark:text-neutral-100">{{ $t("Filter.title") }}</h2>
        </div>

        <form @submit.prevent="submitForm" @reset.prevent="resetFrom" class="text-slate-700 dark:text-neutral-200">
            <div class="grid gap-4 md:grid-cols-2">
                <div class="w-full">
                    <InputLabel for="search" :value="$t('Filter.search')" />

                    <TextInput
                        variant="admin"
                        id="search"
                        type="text"
                        v-model="form.search"
                        :required="false"
                        :placeholder="$t('Filter.search_placeholder')"
                    />
                </div>
                <div class="w-full">
                    <InputLabel for="sort" :value="$t('Filter.sort')" />

                    <SelectInput variant="admin" v-model="form.sort" id="sort">
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
                    </SelectInput>
                </div>
            </div>
            <div class="mt-4 grid gap-4 md:grid-cols-2">
                <div class="w-full">
                    <InputLabel for="type" :value="$t('Type')" />

                    <SelectInput variant="admin" v-model="form.type" id="type">
                        <option :value="null">{{ $t("All") }}</option>
                        <option value="revenue">
                            {{ $t("revenue") }}
                        </option>
                        <option value="expense">
                            {{ $t("expense") }}
                        </option>
                    </SelectInput>
                </div>
                <div class="w-full">
                    <InputLabel for="category" :value="$t('Category')" />

                    <SelectInput variant="admin" v-model="form.category" id="category">
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
                    </SelectInput>
                </div>
            </div>

            <div class="mt-4 grid gap-4 lg:grid-cols-2">
                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <InputLabel
                            for="min_amount"
                            :value="$t('Minimum Amount')"
                        />
                        <TextInput
                            v-model="form.minAmount"
                            id="min_amount"
                            type="number"
                            variant="admin"
                            :placeholder="$t('in DT')"
                            step="0.01"
                        />
                    </div>
                    <div>
                        <InputLabel
                            for="max_amount"
                            :value="$t('Maximum Amount')"
                        />
                        <TextInput
                            v-model="form.maxAmount"
                            id="max_amount"
                            type="number"
                            variant="admin"
                            :placeholder="$t('in DT')"
                            step="0.01"
                        />
                    </div>
                </div>
            </div>

            <div class="mt-4 grid gap-4 sm:grid-cols-2">
                <div class="w-full">
                    <InputLabel
                        for="min_creationDate"
                        :value="`${$t('From')} ${$t('Creation_date')}`"
                    />

                    <DatePicker
                        variant="admin"
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
                        variant="admin"
                        id="max_creationDate"
                        v-model="form.maxCreationDate"
                        :disabled="false"
                    />
                </div>
            </div>

            <div class="mt-6 flex flex-col gap-3 border-t border-slate-100 pt-5 sm:flex-row dark:border-neutral-700">
                <input
                    :disabled="formIsResetting || form.processing"
                    :class="{
                        'opacity-25 !cursor-not-allowed':
                            formIsResetting || form.processing,
                    }"
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
    <!-- Filters End -->

    <ResultsCounter :count="invoices.meta.total" icon="ri-receipt-line" />

    <div v-if="invoices.data.length">
        <div
            class="grid grid-cols-[repeat(auto-fit,_minmax(min(300px,_100%),_1fr))] gap-4"
        >
            <div
                class="flex flex-col overflow-hidden rounded-2xl border border-slate-200 bg-white p-4 shadow-sm dark:border-neutral-700 dark:bg-neutral-800 dark:shadow-black/20"
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
                    <div class="flex items-center gap-2">
                        <ModalLink
                            :close-button="false"
                            :href="route('invoices.show', invoice)"
                            class="inline-flex h-9 w-9 items-center justify-center rounded-lg bg-slate-100 text-slate-600 transition hover:bg-slate-200 dark:bg-neutral-700 dark:text-neutral-200 dark:hover:bg-neutral-600"
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
                            class="inline-flex h-9 w-9 items-center justify-center rounded-lg bg-slate-100 text-slate-600 transition hover:bg-slate-200 dark:bg-neutral-700 dark:text-neutral-200 dark:hover:bg-neutral-600"
                        >
                            <i class="ri-edit-line"></i>
                        </ModalLink>
                        <button
                            v-if="
                                invoice.invoiceable_type ==
                                'App\\Models\\Invoiceable'
                            "
                            @click="deleteInvoice(invoice)"
                            class="inline-flex h-9 w-9 items-center justify-center rounded-lg bg-red-50 text-red-600 transition hover:bg-red-100 dark:bg-red-500/15 dark:text-red-300 dark:hover:bg-red-500/25"
                        >
                            <i class="ri-delete-bin-5-fill"></i>
                        </button>
                    </div>
                </div>
                <div class="my-4 flex-1 text-slate-700 dark:text-neutral-200">
                    <p dir="auto" class="text-lg font-semibold text-slate-800 dark:text-neutral-100">{{ invoice.title }}</p>
                    <p class="text-sm text-slate-500 dark:text-neutral-400">{{ $t(invoice.category) }}</p>
                    <p class="mt-2 text-lg font-semibold text-slate-800 dark:text-neutral-100">
                        {{ `${invoice.amount} ${$t("currency")}` }}
                    </p>
                </div>
                <div class="flex items-center gap-2 border-t border-slate-100 pt-4 text-slate-500 dark:border-neutral-700 dark:text-neutral-400">
                    <i class="ri-calendar-line text-lg" aria-hidden="true"></i>

                    <p class="text-sm font-medium text-slate-700 dark:text-neutral-200">{{ invoice.created_at }}</p>
                </div>
            </div>
        </div>

        <Paginator
            class="mt-8"
            :links="invoices.meta.links"
            :previous="invoices.links.prev"
            :next="invoices.links.next"
            variant="admin"
        />
    </div>
    <NotFound v-else />
</template>
