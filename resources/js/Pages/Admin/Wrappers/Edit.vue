<script setup>
import PageHeader from "@/js/Components/Admin/PageHeader.vue";
import WrapperForm from "./Partials/Form.vue";
import { useWrapperForm } from "@/js/Composables/useWrapperForm";
import { trans } from "laravel-vue-i18n";
import { Link } from "@inertiajs/vue3";

const props = defineProps({
    wrapper: {
        type: Object,
        required: true,
    },
    products: {
        type: Array,
        required: true,
    },
});

const {
    form,
    search,
    isEditing,
    availableProducts,
    localizedProductName,
    addProduct,
    removeProduct,
    setDefault,
    moveProduct,
    submitForm,
    typeLabel,
} = useWrapperForm(props.products, props.wrapper);
</script>

<template>
    <Head :title="`${$t('Edit')} — ${wrapper.title}`" />

    <PageHeader :page-title="`${$t('Edit')} — ${wrapper.title}`">
        <Link
            :href="route('wrappers.show', wrapper.slug)"
            class="inline-flex items-center gap-2 rounded-xl bg-slate-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-slate-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-slate-400 focus-visible:ring-offset-2 dark:focus-visible:ring-offset-neutral-900"
        >
            <i class="ri-arrow-left-line" aria-hidden="true"></i>
            {{ $t("Wrapper.back_to_details") }}
        </Link>
    </PageHeader>

    <WrapperForm
        v-model:search="search"
        :form="form"
        :available-products="availableProducts"
        :localized-product-name="localizedProductName"
        :is-editing="isEditing"
        :type-label="typeLabel"
        @submit="submitForm"
        @add-product="addProduct"
        @remove-product="removeProduct"
        @set-default="setDefault"
        @move-product="moveProduct"
    />
</template>
