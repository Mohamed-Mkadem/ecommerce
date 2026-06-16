<script setup>
import PageTitle from "@/js/Components/FrontEnd/PageTitle.vue";
import Paginator from "@/js/Components/Paginator.vue";
import { router } from "@inertiajs/vue3";
import TextInput from "@/js/Components/TextInput.vue";
import NotFound from "@/js/Components/NotFound.vue";
import WrapperCard from "@/js/Components/FrontEnd/WrapperCard.vue";
import { watch, ref } from "vue";
import { debounce } from "lodash";

const props = defineProps({
    wrappers: { type: Object, required: true },
    filters: { type: Object, default: () => ({}) },
});

const validSorts = [
    "highest_price",
    "lowest_price",
    "highest_rate",
    "lowest_rate",
];
let search = ref(props.filters.search || "");
let sort = ref(
    validSorts.includes(props.filters.sort)
        ? props.filters.sort
        : "lowest_price",
);

watch(
    search,
    debounce((value) => {
        router.get(
            route("FE.shop"),
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
            route("FE.shop"),
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
    <Head :title="$t('Nav.shop')" />
    <div class="py-12 relative px-4 md:px-12 max-w-screen-3xl mx-auto">
        <PageTitle class="mb-12">{{ $t("Nav.shop") }}</PageTitle>

        <div
            class="flex items-center justify-between gap-4 md:gap-6 flex-col sm:flex-row"
        >
            <div class="sm:w-1/3 w-full">
                <TextInput
                    class="mt-1 block w-full rounded-full shadow-sm focus:border-sky-700 focus:ring focus:ring-sky-700/30 transition-all duration-300"
                    id="search"
                    type="search"
                    v-model="search"
                    :required="false"
                    :placeholder="$t('Filter.search_placeholder')"
                />
            </div>
            <div class="sm:w-1/3 w-full">
                <select
                    class="w-full mt-1 rounded-full shadow-sm focus:ring-sky-700 focus:border-sky-700 border-gray-300 text-primary transition-colors duration-300 cursor-pointer"
                    id="sort_id"
                    v-model="sort"
                >
                    <option value="lowest_price">
                        {{ $t("Sort.lowest_price") }}
                    </option>
                    <option value="highest_price">
                        {{ $t("Sort.highest_price") }}
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

        <div v-if="wrappers.data.length">
            <div
                class="grid md:grid-cols-2 lg:grid-cols-3 md:items-stretch gap-x-4 md:gap-x-6 gap-y-12 mt-12"
            >
                <WrapperCard
                    v-for="wrapper in wrappers.data"
                    :key="wrapper.id"
                    :wrapper="wrapper"
                />
            </div>
            <Paginator
                :links="wrappers.links"
                :previous="wrappers.prev_page_url"
                :next="wrappers.next_page_url"
                class="mt-8"
            />
        </div>
        <NotFound v-else />
    </div>
</template>
