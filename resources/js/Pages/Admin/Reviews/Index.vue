use App\Models\Shipper;
<script setup>
import AdminReviewCard from "@/js/Components/Admin/AdminReviewCard.vue";
import NotFound from "@/js/Components/NotFound.vue";
import PageHeader from "@/js/Components/Admin/PageHeader.vue";
import InputLabel from "@/js/Components/InputLabel.vue";
import TextInput from "@/js/Components/TextInput.vue";
import DatePicker from "@/js/Components/DatePicker.vue";
import { useForm } from "@inertiajs/vue3";
import { ref, onMounted } from "vue";
import Paginator from "@/js/Components/Paginator.vue";
const formIsResetting = ref(false);
const props = defineProps({
    reviews: {
        type: Object,
    },
    filters: {
        type: Object,
    },
});
const initialFormValues = {
    search: null,
    sort: "newest",
    minCreationDate: null,
    maxCreationDate: null,
    stars: [],
};
const form = useForm({
    ...initialFormValues,
});

function submitForm() {
    form.get(route("reviews.index"), {
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
    <Head :title="$t('Reviews')" />
    <PageHeader :page-title="$t('Reviews')"> </PageHeader>

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
                        :placeholder="$t('type the product name')"
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
                    </select>
                </div>
            </div>

            <div
                class="flex gap-4 flex-col sm:flex-row sm:justify-between sm:items-center mt-4"
            >
                <div class="w-full">
                    <InputLabel :value="$t('Stars')" />
                    <div
                        class="grid grid-cols-[repeat(auto-fit,_minmax(150px,_1fr))] mt-1 gap-2"
                    >
                        <div
                            class="flex bg-meta-9 px-4 py-2 rounded-md w-full h-[42px]"
                        >
                            <div
                                class="flex items-center gap-3 justify-center w-full"
                            >
                                <input
                                    type="checkbox"
                                    id="stars-1"
                                    v-model="form.stars"
                                    value="1"
                                />
                                <label
                                    for="stars-1"
                                    class="cursor-pointer select-none"
                                    >1</label
                                >
                            </div>
                        </div>
                        <div
                            class="flex bg-meta-9 px-4 py-2 rounded-md w-full h-[42px]"
                        >
                            <div
                                class="flex items-center gap-3 justify-center w-full"
                            >
                                <input
                                    type="checkbox"
                                    id="stars-2"
                                    v-model="form.stars"
                                    value="2"
                                />
                                <label
                                    for="stars-2"
                                    class="cursor-pointer select-none"
                                    >2</label
                                >
                            </div>
                        </div>
                        <div
                            class="flex bg-meta-9 px-4 py-2 rounded-md w-full h-[42px]"
                        >
                            <div
                                class="flex items-center gap-3 justify-center w-full"
                            >
                                <input
                                    type="checkbox"
                                    id="stars-3"
                                    v-model="form.stars"
                                    value="3"
                                />
                                <label
                                    for="stars-3"
                                    class="cursor-pointer select-none"
                                    >3</label
                                >
                            </div>
                        </div>
                        <div
                            class="flex bg-meta-9 px-4 py-2 rounded-md w-full h-[42px]"
                        >
                            <div
                                class="flex items-center gap-3 justify-center w-full"
                            >
                                <input
                                    type="checkbox"
                                    id="stars-4"
                                    v-model="form.stars"
                                    value="4"
                                />
                                <label
                                    for="stars-4"
                                    class="cursor-pointer select-none"
                                    >4</label
                                >
                            </div>
                        </div>
                        <div
                            class="flex bg-meta-9 px-4 py-2 rounded-md w-full h-[42px]"
                        >
                            <div
                                class="flex items-center gap-3 justify-center w-full"
                            >
                                <input
                                    type="checkbox"
                                    id="stars-5"
                                    v-model="form.stars"
                                    value="5"
                                />
                                <label
                                    for="stars-5"
                                    class="cursor-pointer select-none"
                                    >5</label
                                >
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex flex-col gap-4 mt-4 sm:flex-row">
                <div class="w-full">
                    <InputLabel
                        :value="`${$t('From')} ${$t('Creation_date')}`"
                    />

                    <DatePicker
                        v-model="form.minCreationDate"
                        :disabled="false"
                    />
                </div>
                <div class="w-full">
                    <InputLabel :value="`${$t('To')} ${$t('Creation_date')}`" />

                    <DatePicker
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
    <div v-if="reviews.data.length">
        <div class="grid grid-cols-[repeat(auto-fit,minmax(300px,1fr))] gap-4">
            <AdminReviewCard
                v-for="(review, index) in reviews.data"
                :review="review"
                :key="review.id"
                class="mb-2"
            />
        </div>
        <Paginator
            :links="reviews.meta.links"
            :previous="reviews.links.prev"
            :next="reviews.links.next"
            class="mt-4"
        />
    </div>

    <NotFound v-else />
</template>
