use App\Models\Shipper;
<script setup>
import NotFound from "@/js/Components/NotFound.vue";
import Paginator from "@/js/Components/Paginator.vue";
import PageHeader from "@/js/Components/Admin/PageHeader.vue";
import InputLabel from "@/js/Components/InputLabel.vue";
import TextInput from "@/js/Components/TextInput.vue";
import SelectInput from "@/js/Components/SelectInput.vue";
import DatePicker from "@/js/Components/DatePicker.vue";
import { ref, onMounted } from "vue";
import { useForm } from "@inertiajs/vue3";

const props = defineProps({
    nrps: {
        type: Object,
    },
    filters: {
        type: Object,
    },
});

const initialFormValues = {
    phone: null,
    minAmount: null,
    maxAmount: null,
    minCreationDate: null,
    maxCreationDate: null,
    minTries: null,
    maxTries: null,
    sort: null,
};
const formIsResetting = ref(false);
const form = useForm({ ...initialFormValues });
function submitForm() {
    form.get(route("nrp.index"), {
        preserveState: true,
        preserveScroll: true,
        replace: true,
    });
}
function resetForm() {
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
    <Head :title="$t('NRP')" />
    <PageHeader :page-title="$t('NRP')"> </PageHeader>

    <!-- Filters Start -->
    <div
        class="mb-8 rounded-2xl border border-slate-200/80 bg-white px-5 py-6 shadow-[0_8px_30px_rgba(15,23,42,0.06)] sm:px-6 sm:py-7 dark:border-neutral-700 dark:bg-neutral-800 dark:shadow-black/30"
    >
        <h2
            class="mb-5 text-xl font-semibold tracking-tight text-slate-800 dark:text-neutral-100"
        >
            {{ $t("Filter.title") }}
        </h2>

        <form
            @submit.prevent="submitForm"
            @reset.prevent="resetForm"
            class="text-slate-700 dark:text-neutral-200"
        >
            <div
                class="flex gap-4 flex-col sm:flex-row sm:justify-between sm:items-center"
            >
                <div class="w-full">
                    <InputLabel
                        for="clientPhone"
                        :value="$t('Filter.search_by_client_phone')"
                    />

                    <TextInput
                        variant="admin"
                        id="clientPhone"
                        type="text"
                        v-model="form.phone"
                        :required="false"
                        :placeholder="
                            $t('Filter.search_by_client_phone_placeholder')
                        "
                    />
                </div>
                <div class="w-full">
                    <InputLabel for="sort" :value="$t('Filter.sort')" />

                    <SelectInput variant="admin" v-model="form.sort" id="sort">
                        <option :value="null">
                            {{ $t("Filter.sort") }}
                        </option>
                        <option value="highest_tries">
                            {{ $t("Sort.highest_tries") }}
                        </option>
                        <option value="lowest_tries">
                            {{ $t("Sort.lowest_tries") }}
                        </option>
                        <option value="highest_amount">
                            {{ $t("Sort.highest_amount") }}
                        </option>
                        <option value="lowest_amount">
                            {{ $t("Sort.lowest_amount") }}
                        </option>
                        <option value="newest">
                            {{ $t("Sort.newest") }}
                        </option>
                        <option value="oldest">
                            {{ $t("Sort.oldest") }}
                        </option>
                    </SelectInput>
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
                            variant="admin"
                            v-model="form.minAmount"
                            id="min_amount"
                            type="number"
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
                            variant="admin"
                            v-model="form.maxAmount"
                            id="max_amount"
                            type="number"
                            :placeholder="$t('in DT')"
                            step="0.01"
                        />
                    </div>
                </div>
                <div class="flex flex-col gap-4 sm:flex-row w-full">
                    <div class="mt-1 w-full">
                        <InputLabel
                            for="min_tries"
                            :value="$t('Minimum Tries')"
                        />
                        <TextInput
                            variant="admin"
                            v-model="form.minTries"
                            id="min_tries"
                            type="number"
                            :placeholder="$t('MinTries_placeholder')"
                            min="1"
                        />
                    </div>
                    <div class="mt-1 w-full">
                        <InputLabel
                            for="max_tries"
                            :value="$t('Maximum Tries')"
                        />
                        <TextInput
                            variant="admin"
                            v-model="form.maxTries"
                            id="max_tries"
                            type="number"
                            :placeholder="$t('MaxTries_placeholder')"
                            min="1"
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

            <div class="flex gap-4 mt-8">
                <input
                    :disabled="formIsResetting || form.processing"
                    :class="{
                        'opacity-25 !cursor-not-allowed':
                            formIsResetting || form.processing,
                    }"
                    type="Submit"
                    :value="$t('Filter.value')"
                    class="w-full cursor-pointer rounded-xl border border-teal-700 bg-teal-700 px-5 py-2.5 font-semibold text-white shadow-sm transition hover:-translate-y-0.5 hover:bg-teal-700/90 hover:shadow-md focus:outline-none focus-visible:ring-2 focus-visible:ring-teal-700/40 disabled:cursor-not-allowed disabled:opacity-50 sm:w-1/4"
                />
                <input
                    :disabled="formIsResetting || form.processing"
                    :class="{
                        'opacity-75 !cursor-not-allowed':
                            formIsResetting || form.processing,
                    }"
                    type="reset"
                    :value="$t('form.reset')"
                    class="w-full cursor-pointer rounded-xl border border-neutral-500 bg-neutral-500 px-5 py-2.5 font-semibold text-white shadow-sm transition hover:bg-neutral-600 hover:shadow-md focus:outline-none focus-visible:ring-2 focus-visible:ring-neutral-500/40 disabled:cursor-not-allowed disabled:opacity-50 sm:w-1/4 dark:border-neutral-600 dark:bg-neutral-700 dark:hover:bg-neutral-600"
                />
            </div>
        </form>
    </div>
    <!-- Filters End -->

    <h2 class="my-6 text-xl font-semibold text-primary dark:text-neutral-100">
        {{ $t("Results") }} : {{ nrps.meta.total }}
    </h2>

    <div v-if="nrps.data.length">
        <div
            class="grid grid-cols-[repeat(auto-fit,_minmax(min(350px,_100%),_1fr))] gap-4"
        >
            <!-- Start NRP card -->
            <article
                v-for="nrp in nrps.data"
                :key="nrp.id"
                class="flex flex-col overflow-hidden rounded-2xl border border-slate-200/80 bg-white shadow-sm transition duration-200 hover:-translate-y-0.5 hover:shadow-md dark:border-neutral-700 dark:bg-neutral-800 dark:shadow-black/30"
            >
                <header
                    class="flex items-center justify-between gap-3 border-b border-slate-100 px-4 py-3 dark:border-neutral-700"
                >
                    <div>
                        <p
                            class="text-xs font-medium uppercase tracking-wide text-slate-500 dark:text-neutral-400"
                        >
                            {{ $t("Tries") }}
                        </p>
                        <p
                            class="mt-0.5 text-lg font-semibold text-slate-800 dark:text-neutral-100"
                        >
                            {{ nrp.tries }}
                        </p>
                    </div>
                    <Link
                        :href="route('orders.show', nrp.order_id)"
                        class="rounded-lg bg-slate-100 p-2 text-lg text-slate-600 transition hover:bg-slate-200 dark:bg-neutral-700 dark:text-neutral-200 dark:hover:bg-neutral-600"
                        :title="$t('View')"
                        :aria-label="$t('View')"
                    >
                        <i class="ri-eye-line"></i>
                    </Link>
                </header>

                <div class="flex-1 space-y-4 p-4">
                    <div
                        class="flex items-center gap-3 rounded-xl bg-slate-50 px-3 py-3 dark:bg-neutral-700/60"
                    >
                        <span
                            class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-white text-xl text-slate-500 dark:bg-neutral-800 dark:text-neutral-300"
                        >
                            <i
                                class="ri-money-dollar-box-line"
                                aria-hidden="true"
                            ></i>
                        </span>
                        <div class="min-w-0">
                            <p
                                class="text-xs font-medium text-slate-500 dark:text-neutral-400"
                            >
                                {{ $t("Amount") }}
                            </p>
                            <p
                                class="mt-0.5 text-xl font-bold text-primary dark:text-neutral-100"
                            >
                                {{ `${nrp.amount} ${$t("currency")}` }}
                            </p>
                        </div>
                    </div>
                    <div
                        class="flex items-center gap-3 text-slate-600 dark:text-neutral-300"
                    >
                        <i
                            class="ri-customer-service-2-fill text-xl text-slate-400 dark:text-neutral-500"
                            aria-hidden="true"
                        ></i>
                        <p dir="ltr" class="text-base font-medium">
                            {{ nrp.phone }}
                        </p>
                    </div>
                    <div
                        class="flex items-center gap-3 text-slate-600 dark:text-neutral-300"
                    >
                        <i
                            class="ri-calendar-line text-xl text-slate-400 dark:text-neutral-500"
                            aria-hidden="true"
                        ></i>
                        <p class="text-sm">{{ nrp.created_at }}</p>
                    </div>
                </div>
            </article>
            <!-- End NRP card -->
        </div>
        <Paginator
            variant="admin"
            :links="nrps.meta.links"
            :previous="nrps.links.prev"
            :next="nrps.links.next"
            class="mt-4"
        />
    </div>
    <div v-else>
        <NotFound />
    </div>
</template>
