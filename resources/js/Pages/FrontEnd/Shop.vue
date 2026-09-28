<script setup>
import Paginator from "@/js/Components/Paginator.vue";
import { router } from "@inertiajs/vue3";
import TextInput from "@/js/Components/TextInput.vue";
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
    <main class="mx-auto min-h-screen max-w-screen-3xl bg-lightCream px-5 py-12 sm:px-8 md:px-12 md:py-16">
        <div class="mx-auto max-w-screen-xl">
            <header class="mb-9 border-b border-line pb-7 sm:mb-10 sm:pb-8">
                <p class="mb-2 text-[10px] font-semibold uppercase tracking-[0.2em] text-cOrangeDark">
                    {{ $t("shop.eyebrow") }}
                </p>
                <h1 class="font-serif text-3xl leading-tight tracking-tight text-brown sm:text-4xl md:text-5xl">
                    {{ $t("shop.title") }}
                </h1>
                <p class="mt-3 max-w-xl text-sm leading-6 text-brown/75">
                    {{ $t("shop.description") }}
                </p>
            </header>

            <div class="mb-8 grid gap-4 rounded-md border border-line bg-offwhite p-4 sm:grid-cols-[minmax(0,1fr)_minmax(220px,280px)] sm:items-end sm:p-5">
                <div class="w-full">
                    <label for="search" class="mb-2 block text-xs font-semibold text-brown">
                        {{ $t("shop.search_label") }}
                    </label>
                    <div class="relative">
                        <i class="ri-search-line pointer-events-none absolute start-4 top-1/2 -translate-y-1/2 text-base text-cOrangeDark" aria-hidden="true"></i>
                <TextInput
                            class="block w-full rounded-sm border-line bg-white py-3 ps-11 pe-4 text-sm text-brown shadow-none placeholder:text-brown/45 focus:border-corange focus:ring-corange/20"
                    id="search"
                    type="search"
                    v-model="search"
                    :required="false"
                    :placeholder="$t('Filter.search_placeholder')"
                />
                    </div>
            </div>
                <div class="w-full">
                    <label for="sort_id" class="mb-2 block text-xs font-semibold text-brown">
                        {{ $t("shop.sort_label") }}
                    </label>
                <select
                            class="w-full cursor-pointer rounded-sm border-line bg-white px-4 py-3 text-sm text-brown shadow-none transition-colors focus:border-corange focus:outline-none focus:ring-corange/20"
                    id="sort_id"
                    v-model="sort"
                >
                    <option value="lowest_price">
                        {{ $t("Sort.lowest_price") }}
                    </option>
                    <option value="highest_price">
                        {{ $t("Sort.highest_price") }}
                    </option>
                </select>
                </div>
            </div>

        <div v-if="wrappers.data.length">
            <div
                    class="grid min-[600px]:grid-cols-2 gap-x-4 gap-y-8 sm:gap-x-6 sm:gap-y-10 lg:grid-cols-4 lg:gap-x-5 xl:gap-x-6"
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
                    variant="sweetia"
                    class="mt-10 justify-center sm:mt-12"
            />
        </div>
            <div v-else class="rounded-md border border-line bg-offwhite px-5 py-16 text-center">
                <i class="ri-cake-3-line text-4xl text-corange" aria-hidden="true"></i>
                <p class="mt-3 font-serif text-xl text-brown">{{ $t("shop.empty_title") }}</p>
                <p class="mt-2 text-sm text-brown/70">{{ $t("shop.empty_description") }}</p>
            </div>
        </div>
    </main>
</template>
