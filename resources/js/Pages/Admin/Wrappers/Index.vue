<script setup>
import PageHeader from "@/js/Components/Admin/PageHeader.vue";
import CreateNew from "@/js/Components/CreateNew.vue";
import ResultsCounter from "@/js/Components/Admin/ResultsCounter.vue";
import Card from "./Partials/Card.vue";
import Paginator from "@/js/Components/Paginator.vue";
import NotFound from "@/js/Components/NotFound.vue";
import { useForm, usePage } from "@inertiajs/vue3";
import { onMounted } from "vue";
import InputLabel from "@/js/Components/InputLabel.vue";
import TextInput from "@/js/Components/TextInput.vue";
import SelectInput from "@/js/Components/SelectInput.vue";
import { useToast } from "vue-toastification";
import { getToastOptions } from "@/js/Utils/toast";
import { trans } from "laravel-vue-i18n";

const props = defineProps({
    wrappers: {
        type: Object,
        required: true,
    },
    filters: {
        type: Object,
        default: () => ({}),
    },
});

const initialFormValues = {
    name: "",
    sort: "",
    status: "",
    minProductsCount: "",
    maxProductsCount: "",
};
const form = useForm({ ...initialFormValues });

function submitForm() {
    form.get(route("wrappers.index"), {
        preserveState: true,
        preserveScroll: true,
        replace: true,
    });
}

function resetForm() {
    Object.assign(form, initialFormValues);
    form.clearErrors();
    submitForm();
}

const toast = useToast();
const page = usePage();

onMounted(() => {
    Object.assign(form, props.filters);
    if (page.props.flash?.success) {
        toast.success(page.props.flash.success, getToastOptions());
    }
});
</script>

<template>
    <Head :title="$t('Wrappers')" />

    <PageHeader :page-title="$t('Wrappers')">
        <CreateNew
            v-if="$page.props.auth.user.role === 'admin'"
            href="wrappers.create"
            :label="$t('New Wrapper')"
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

        <form @submit.prevent="submitForm" @reset.prevent="resetForm" class="space-y-5">
            <div class="grid gap-5 md:grid-cols-3">
                <div>
                    <InputLabel for="wrapper-name" :value="$t('Wrapper.filter_name')" class="text-slate-700 dark:text-neutral-200" />
                    <TextInput variant="admin" id="wrapper-name" type="search" class="mt-1 block w-full" v-model="form.name" :placeholder="$t('Filter.search_placeholder')" />
                </div>
                <div>
                    <InputLabel for="wrapper-status" :value="$t('Wrapper.status')" class="text-slate-700 dark:text-neutral-200" />
                    <SelectInput variant="admin" id="wrapper-status" v-model="form.status">
                        <option value="">{{ $t("All") }}</option>
                        <option value="active">{{ $t("Wrapper.active") }}</option>
                        <option value="inactive">{{ $t("Wrapper.inactive") }}</option>
                    </SelectInput>
                </div>
                <div>
                    <InputLabel for="wrapper-sort" :value="$t('Filter.sort')" class="text-slate-700 dark:text-neutral-200" />
                    <SelectInput variant="admin" id="wrapper-sort" v-model="form.sort">
                        <option value="">{{ $t("Filter.sort") }}</option>
                        <option value="highest_products">{{ $t("Wrapper.highest_products") }}</option>
                        <option value="lowest_products">{{ $t("Wrapper.lowest_products") }}</option>
                    </SelectInput>
                </div>
            </div>

            <div class="grid grid-cols-[repeat(auto-fit,minmax(min(250px,100%),1fr))] gap-5">
                <div>
                    <InputLabel for="min-products-count" :value="$t('Wrapper.min_products_count')" class="text-slate-700 dark:text-neutral-200" />
                    <TextInput variant="admin" id="min-products-count" type="number" min="0" class="mt-1 w-full" v-model="form.minProductsCount" placeholder="0" />
                </div>
                <div>
                    <InputLabel for="max-products-count" :value="$t('Wrapper.max_products_count')" class="text-slate-700 dark:text-neutral-200" />
                    <TextInput variant="admin" id="max-products-count" type="number" min="0" class="mt-1 w-full" v-model="form.maxProductsCount" placeholder="0" />
                </div>
            </div>

            <div class="flex flex-col gap-3 border-t border-slate-100 pt-5 sm:flex-row dark:border-neutral-700">
                <button :disabled="form.processing" type="submit" class="w-full cursor-pointer rounded-xl border border-teal-700 bg-teal-700 px-5 py-2.5 font-medium text-white shadow-sm transition duration-200 hover:-translate-y-0.5 hover:bg-opacity-90 hover:shadow-md focus:outline-none focus-visible:ring-2 focus-visible:ring-teal-500/40 disabled:cursor-not-allowed disabled:opacity-60 sm:w-1/4">
                    {{ $t("Filter.value") }}
                </button>
                <button :disabled="form.processing" type="reset" class="w-full cursor-pointer rounded-xl border border-neutral-300 bg-neutral-100 px-5 py-2.5 font-medium text-neutral-700 shadow-sm transition duration-200 hover:-translate-y-0.5 hover:bg-neutral-200 hover:shadow-md focus:outline-none focus-visible:ring-2 focus-visible:ring-neutral-400/50 disabled:cursor-not-allowed disabled:opacity-60 sm:w-1/4 dark:border-neutral-600 dark:bg-neutral-700 dark:text-neutral-200 dark:hover:bg-neutral-600">
                    {{ $t("form.reset") }}
                </button>
            </div>
        </form>
    </section>

    <ResultsCounter :count="wrappers.meta.total" icon="ri-gift-line" />

    <div v-if="wrappers.data.length">
        <div
            class="grid grid-cols-[repeat(auto-fit,_minmax(min(280px,_100%),_1fr))] gap-4"
        >
            <Card
                v-for="wrapper in wrappers.data"
                :key="wrapper.id"
                :wrapper="wrapper"
            />
        </div>
        <Paginator
            :links="wrappers.meta.links"
            :previous="wrappers.links.prev"
            :next="wrappers.links.next"
            variant="admin"
            class="mt-6"
        />
    </div>
    <NotFound v-else message="Wrapper.no_wrappers" />
</template>
