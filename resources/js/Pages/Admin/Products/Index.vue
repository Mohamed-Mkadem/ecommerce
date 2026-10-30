<script setup>
import PageHeader from "@/js/Components/Admin/PageHeader.vue";
import ResultsCounter from "@/js/Components/Admin/ResultsCounter.vue";
import Card from "./Partials/Card.vue";
import Paginator from "@/js/Components/Paginator.vue";
import { useForm } from "@inertiajs/vue3";
import InputLabel from "@/js/Components/InputLabel.vue";
import TextInput from "@/js/Components/TextInput.vue";
import SelectInput from "@/js/Components/SelectInput.vue";
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
        <CreateNew
            v-if="$page.props.auth.user.role === 'admin'"
            href="products.create"
            :label="$t('New Product')"
        />
    </PageHeader>

    <section class="mb-6 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6 dark:border-neutral-700 dark:bg-neutral-800 dark:shadow-black/20">
        <div class="mb-6 flex items-start gap-3 border-b border-slate-100 pb-4 dark:border-neutral-700">
            <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-slate-100 text-lg text-slate-600 dark:bg-neutral-700 dark:text-neutral-200">
                <i class="ri-filter-3-line" aria-hidden="true"></i>
            </span>
            <h2 class="pt-1.5 text-lg font-semibold tracking-tight text-slate-800 dark:text-neutral-100">
                {{ $t("Filter.title") }}
            </h2>
        </div>

        <form @submit.prevent="submitForm" @reset.prevent="resetFrom" class="space-y-5">
            <div class="grid gap-5 md:grid-cols-2">
                <div>
                    <InputLabel for="search" :value="$t('Filter.search')" class="text-slate-700 dark:text-neutral-200" />
                    <TextInput variant="admin" class="mt-1 block w-full" id="search" type="text" v-model="form.search" :required="false" :placeholder="$t('Filter.search_placeholder')" />
                </div>
                <div>
                    <InputLabel for="sort" :value="$t('Filter.sort')" class="text-slate-700 dark:text-neutral-200" />
                    <SelectInput variant="admin" v-model="form.sort" id="sort">
                        <option value="highest_price">{{ $t("Sort.highest_price") }}</option>
                        <option value="lowest_price">{{ $t("Sort.lowest_price") }}</option>
                        <option value="highest_rate">{{ $t("Sort.highest_rate") }}</option>
                        <option value="lowest_rate">{{ $t("Sort.lowest_rate") }}</option>
                        <option value="highest_orders">{{ $t("Sort.highest_orders") }}</option>
                        <option value="lowest_orders">{{ $t("Sort.lowest_orders") }}</option>
                    </SelectInput>
                </div>
            </div>

            <div class="space-y-5">
                <div class="grid grid-cols-[repeat(auto-fit,minmax(min(250px,100%),1fr))] gap-5">
                    <div>
                        <InputLabel for="min_price" :value="$t('Product.min_price')" class="text-slate-700 dark:text-neutral-200" />
                        <TextInput variant="admin" v-model="form.minPrice" id="min_price" type="number" class="mt-1 w-full" :placeholder="$t('in DT')" step="0.01" />
                    </div>
                    <div>
                        <InputLabel for="max_price" :value="$t('Product.max_price')" class="text-slate-700 dark:text-neutral-200" />
                        <TextInput variant="admin" v-model="form.maxPrice" id="max_price" type="number" class="mt-1 w-full" :placeholder="$t('in DT')" step="0.01" />
                    </div>
                </div>
                <div class="grid grid-cols-[repeat(auto-fit,minmax(min(250px,100%),1fr))] gap-5">
                    <div>
                        <InputLabel for="min_rate" :value="$t('Product.min_rate')" class="text-slate-700 dark:text-neutral-200" />
                        <TextInput variant="admin" v-model="form.minRate" id="min_rate" type="number" step="0.1" class="mt-1 w-full" :placeholder="$t('Product.min_rate_placeholder')" />
                    </div>
                    <div>
                        <InputLabel for="max_rate" :value="$t('Product.max_rate')" class="text-slate-700 dark:text-neutral-200" />
                        <TextInput variant="admin" v-model="form.maxRate" id="max_rate" type="number" step="0.1" class="mt-1 w-full" :placeholder="$t('Product.max_rate_placeholder')" />
                    </div>
                </div>
                <div class="grid grid-cols-[repeat(auto-fit,minmax(min(250px,100%),1fr))] gap-5">
                    <div>
                        <InputLabel for="min_orders_count" :value="$t('Product.min_orders_count')" class="text-slate-700 dark:text-neutral-200" />
                        <TextInput variant="admin" v-model="form.minOrdersCount" id="min_orders_count" type="number" class="mt-1 w-full" :placeholder="$t('Product.min_orders_placeholder')" />
                    </div>
                    <div>
                        <InputLabel for="max_orders_count" :value="$t('Product.max_orders_count')" class="text-slate-700 dark:text-neutral-200" />
                        <TextInput variant="admin" v-model="form.maxOrdersCount" id="max_orders_count" type="number" class="mt-1 w-full" :placeholder="$t('Product.max_orders_placeholder')" />
                    </div>
                </div>
            </div>

            <div class="flex flex-col gap-3 border-t border-slate-100 pt-5 sm:flex-row dark:border-neutral-700">
                <button :disabled="formIsResetting || form.processing" type="submit" class="w-full cursor-pointer rounded-xl border border-teal-700 bg-teal-700 px-5 py-2.5 font-medium text-white shadow-sm transition duration-200 hover:-translate-y-0.5 hover:bg-opacity-90 hover:shadow-md focus:outline-none focus-visible:ring-2 focus-visible:ring-teal-500/40 disabled:cursor-not-allowed disabled:opacity-60 sm:w-1/4">
                    {{ $t("Filter.value") }}
                </button>
                <button :disabled="formIsResetting || form.processing" type="reset" class="w-full cursor-pointer rounded-xl border border-neutral-300 bg-neutral-100 px-5 py-2.5 font-medium text-neutral-700 shadow-sm transition duration-200 hover:-translate-y-0.5 hover:bg-neutral-200 hover:shadow-md focus:outline-none focus-visible:ring-2 focus-visible:ring-neutral-400/50 disabled:cursor-not-allowed disabled:opacity-60 sm:w-1/4 dark:border-neutral-600 dark:bg-neutral-700 dark:text-neutral-200 dark:hover:bg-neutral-600">
                    {{ $t("form.reset") }}
                </button>
            </div>
        </form>
    </section>

    <ResultsCounter :count="products.meta.total" />

    <div v-if="products.data.length">
        <div
            class="grid grid-cols-[repeat(auto-fit,_minmax(min(260px,_100%),_1fr))] gap-4"
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
            variant="admin"
            class="mt-6"
        />
    </div>
    <div v-else>
        <NotFound message="NotFound.products" />
    </div>
</template>
