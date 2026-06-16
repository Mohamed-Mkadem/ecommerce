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
            class="bg-bodydark text-white px-3 py-2 rounded-md hover:bg-opacity-90"
        >
            {{ $t("Wrapper.back_to_details") }}
        </Link>
    </PageHeader>

    <WrapperForm
        v-model:search="search"
        :form="form"
        :available-products="availableProducts"
        :is-editing="isEditing"
        :type-label="typeLabel"
        @submit="submitForm"
        @add-product="addProduct"
        @remove-product="removeProduct"
        @set-default="setDefault"
        @move-product="moveProduct"
    />
</template>
