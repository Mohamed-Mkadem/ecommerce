<script setup>
import PageHeader from "@/js/Components/Admin/PageHeader.vue";
import Card from "./Partials/Card.vue";
import Paginator from "@/js/Components/Paginator.vue";
import { useForm } from "@inertiajs/vue3";
import InputLabel from "@/js/Components/InputLabel.vue";
import TextInput from "@/js/Components/TextInput.vue";
import NotFound from "@/js/Components/NotFound.vue";
import { onMounted, ref } from "vue";
import CreateNew from "@/js/Components/CreateNew.vue";
const props = defineProps({
    products: {
        type: Object,
    },
    filters: {
        type: Object,
    },
});
const formIsResetting = ref(false);
const initialFormValues = {
    search: "",
    minPrice: null,
    maxPrice: null,
    maxRate: null,
    minRate: null,
    minOrdersCount: null,
    maxOrdersCount: null,
    sort: "highest_price",
};
const form = useForm({ ...initialFormValues });
function submitForm() {
    form.get(route("products.index"), {
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
</script>

<template>
    <Head :title="$t('Product.index')" />
    <PageHeader :page-title="$t('Products')">
        <CreateNew href="products.create" :label="$t('New Product')" />
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
                        <option value="highest_price">
                            {{ $t("Sort.highest_price") }}
                        </option>
                        <option value="lowest_price">
                            {{ $t("Sort.lowest_price") }}
                        </option>
                        <option value="highest_rate">
                            {{ $t("Sort.highest_rate") }}
                        </option>
                        <option value="lowest_rate">
                            {{ $t("Sort.lowest_rate") }}
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
                <div class="flex flex-col gap-4 sm:flex-row w-full">
                    <div class="mt-1 w-full">
                        <InputLabel
                            for="min_price"
                            :value="$t('Product.min_price')"
                        />
                        <TextInput
                            v-model="form.minPrice"
                            id="min_price"
                            type="number"
                            class="mt-1 w-full"
                            :placeholder="$t('in DT')"
                            step="0.01"
                        />
                    </div>
                    <div class="mt-1 w-full">
                        <InputLabel
                            for="max_price"
                            :value="$t('Product.max_price')"
                        />
                        <TextInput
                            v-model="form.maxPrice"
                            id="max_price"
                            type="number"
                            class="mt-1 w-full"
                            :placeholder="$t('in DT')"
                            step="0.01"
                        />
                    </div>
                </div>
                <div class="flex flex-col gap-4 sm:flex-row w-full">
                    <div class="w-full mt-1 gap-2">
                        <InputLabel
                            for="min_rate"
                            :value="$t('Product.min_rate')"
                        />
                        <TextInput
                            v-model="form.minRate"
                            id="min_rate"
                            type="number"
                            step="0.1"
                            class="mt-1 w-full"
                            :placeholder="$t('Product.min_rate_placeholder')"
                        />
                    </div>
                    <div class="w-full mt-1 gap-2">
                        <InputLabel
                            for="max_rate"
                            :value="$t('Product.max_rate')"
                        />
                        <TextInput
                            v-model="form.maxRate"
                            step="0.1"
                            id="max_rate"
                            type="number"
                            class="mt-1 w-full"
                            :placeholder="$t('Product.max_rate_placeholder')"
                        />
                    </div>
                </div>
            </div>

            <div class="flex flex-col gap-4 mt-4 lg:flex-row">
                <div class="flex flex-col gap-4 sm:flex-row w-full">
                    <div class="mt-1 w-full">
                        <InputLabel
                            for="min_orders_count"
                            :value="$t('Product.min_orders_count')"
                        />
                        <TextInput
                            v-model="form.minOrdersCount"
                            id="min_orders_count"
                            type="number"
                            class="mt-1 w-full"
                            :placeholder="$t('Product.min_orders_placeholder')"
                        />
                    </div>
                    <div class="mt-1 w-full">
                        <InputLabel
                            for="max_orders_count"
                            :value="$t('Product.max_orders_count')"
                        />
                        <TextInput
                            v-model="form.maxOrdersCount"
                            id="max_orders_count"
                            type="number"
                            class="mt-1 w-full"
                            :placeholder="$t('Product.max_orders_placeholder')"
                        />
                    </div>
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

    <h2 class="my-6 font-medium text-sky-900 text-xl">
        {{ $t("Results") }} : {{ products.meta.total }}
    </h2>

    <div v-if="products.data.length">
        <div
            class="grid grid-cols-[repeat(auto-fit,_minmax(min(300px,_100%),_1fr))] gap-4"
        >
            <Card
                v-for="product in props.products.data"
                :key="product.id"
                :product="product"
            />
        </div>
        <Paginator
            :links="products.meta.links"
            :previous="products.links.prev"
            :next="products.links.next"
            class="mt-4"
        />
    </div>
    <div v-else>
        <NotFound message="NotFound.products" />
    </div>
</template>
