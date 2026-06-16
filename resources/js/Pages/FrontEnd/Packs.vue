<script setup>
import PageTitle from "@/js/Components/FrontEnd/PageTitle.vue";
import { useLanguageStore } from "@/js/stores/Language";
import Paginator from "@/js/Components/Paginator.vue";
import { router } from "@inertiajs/vue3";
import TextInput from "@/js/Components/TextInput.vue";
import NotFound from "@/js/Components/NotFound.vue";
import ProductCard from "@/js/Components/FrontEnd/ProductCard.vue";
import { watch, ref } from "vue";
import { debounce } from "lodash";

const languageStore = useLanguageStore();
const props = defineProps({
    products: { type: Object },
    filters: { type: Object },
});
const validSorts = ["highest_price", "lowest_price", "highest_rate", "lowest_rate"];
let search = ref(props.filters?.search || "");
let sort = ref(validSorts.includes(props.filters?.sort) ? props.filters.sort : "lowest_price");

watch(
    search,
    debounce((value) => {
        router.get(
            route("FE.packs"),
            { search: value, sort: sort.value },
            {
                preserveState: true,
                preserveScroll: true,
                replace: true,
            },
        );
    }, 500),
);
watch(
    sort,
    debounce((value) => {
        router.get(
            route("FE.packs"),
            { sort: value, search: search.value },
            {
                preserveState: true,
                preserveScroll: true,
                replace: true,
            },
        );
    }, 500),
);
</script>

<template>
    <Head :title="$t('Nav.packs')" />
    <div class="py-12 relative px-4 md:px-12 max-w-screen-3xl mx-auto">
        <PageTitle class="mb-12">{{ $t("Nav.packs") }}</PageTitle>

        <div
            class="flex items-center justify-between gap-4 md:gap-6 flex-col sm:flex-row"
        >
            <div class="sm:w-1/3 w-full">
                <TextInput
                    class="mt-1 block w-full"
                    id="search"
                    type="search"
                    v-model="search"
                    :required="false"
                    :placeholder="$t('Filter.search_placeholder')"
                />
            </div>
            <div class="sm:w-1/3 w-full">
                <select
                    class="w-full mt-1 rounded-md focus:ring-primary focus:border-primary border-editor text-primary"
                    id="sort_id"
                    v-model="sort"
                >
                    <option value="">
                        {{ $t("Sort") }}
                    </option>
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
                </select>
            </div>
        </div>

        <div v-if="products.data.length">
            <div
                class="grid md:grid-cols-2 lg:grid-cols-3 md:items-stretch gap-x-4 md:gap-x-6 gap-y-12 mt-12"
            >
                <ProductCard
                    v-for="(product, index) in products.data"
                    :product="product"
                    :key="index"
                    :with-rate="true"
                />
            </div>
            <Paginator
                :links="products.links"
                :previous="products.prev_page_url"
                :next="products.next_page_url"
                class="mt-8"
            />
        </div>
        <NotFound v-else />
    </div>
</template>
