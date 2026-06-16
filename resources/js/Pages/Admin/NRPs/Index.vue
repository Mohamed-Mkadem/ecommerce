use App\Models\Shipper;
<script setup>
import NotFound from "@/js/Components/NotFound.vue";
import Paginator from "@/js/Components/Paginator.vue";
import PageHeader from "@/js/Components/Admin/PageHeader.vue";
import InputLabel from "@/js/Components/InputLabel.vue";
import TextInput from "@/js/Components/TextInput.vue";
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
    <div class="bg-white px-4 py-8 rounded-md shadow-1 mb-8">
        <h2 class="text-2xl mb-4 font-semibold text-graydark">
            {{ $t("Filter.title") }}
        </h2>

        <form @submit.prevent="submitForm" @reset.prevent="resetForm">
            <div
                class="flex gap-4 flex-col sm:flex-row sm:justify-between sm:items-center"
            >
                <div class="w-full">
                    <InputLabel
                        for="clientPhone"
                        :value="$t('Filter.search_by_client_phone')"
                    />

                    <TextInput
                        class="mt-1 block w-full"
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

                    <select
                        class="w-full mt-1 rounded-md focus:ring-primary focus:border-primary border-editor text-primary"
                        v-model="form.sort"
                        id="sort"
                    >
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
                <div class="flex flex-col gap-4 sm:flex-row w-full">
                    <div class="mt-1 w-full">
                        <InputLabel
                            for="min_tries"
                            :value="$t('Minimum Tries')"
                        />
                        <TextInput
                            v-model="form.minTries"
                            id="min_tries"
                            type="number"
                            class="mt-1 w-full"
                            :placeholder="$t('MinTries_placeholder')"
                        />
                    </div>
                    <div class="mt-1 w-full">
                        <InputLabel
                            for="max_tries"
                            :value="$t('Maximum Tries')"
                        />
                        <TextInput
                            v-model="form.maxTries"
                            id="max_tries"
                            type="number"
                            class="mt-1 w-full"
                            :placeholder="$t('MaxTries_placeholder')"
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

    <h2 class="my-6 font-medium text-sky-900 text-xl">
        {{ $t("Results") }} : {{ nrps.meta.total }}
    </h2>

    <div v-if="nrps.data.length">
        <div
            class="grid grid-cols-[repeat(auto-fit,_minmax(min(350px,_100%),_1fr))] gap-4"
        >
            <!-- Start NRP card -->
            <div
                v-for="nrp in nrps.data"
                :key="nrp.id"
                class="bg-white p-4 rounded-md shadow-md"
            >
                <div
                    class="flex justify-between items-center py-2 gap-2 border-b border-slate-200"
                >
                    <p class="text-lg">
                        {{ $t("Tries") }}
                        :
                        <span class="font-semibold">
                            {{ nrp.tries }}
                        </span>
                    </p>

                    <Link
                        :href="route('orders.show', nrp.order_id)"
                        class="bg-slate-500 text-white text-xl rounded-md px-2 py-1 hover:bg-opacity-90"
                    >
                        <i class="ri-eye-line"></i>
                    </Link>
                </div>

                <div class="pt-2">
                    <div class="flex gap-2 items-center">
                        <i
                            class="ri-money-dollar-box-line text-2xl text-slate-400"
                        ></i>
                        <p class="text-red font-semibold text-2xl">
                            {{ `${nrp.amount} ${$t("currency")}` }}
                        </p>
                    </div>
                    <div class="flex items-center gap-3 my-1">
                        <i
                            class="ri-customer-service-2-fill text-slate-400 text-2xl"
                        ></i>

                        <p dir="ltr" class="text-lg mb-1">
                            {{ nrp.phone }}
                        </p>
                    </div>

                    <div class="flex items-center gap-3">
                        <p class="flex gap-2 items-center justify-center">
                            <i
                                class="ri-calendar-line text-2xl text-slate-400"
                            ></i>
                        </p>
                        <p class="">{{ nrp.created_at }}</p>
                    </div>
                </div>
            </div>
            <!-- End NRP card -->
        </div>
        <Paginator
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
