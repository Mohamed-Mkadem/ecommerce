<script setup>
import PageHeader from "@/js/Components/Admin/PageHeader.vue";
import WrapperForm from "./Partials/Form.vue";
import { useWrapperForm } from "@/js/Composables/useWrapperForm";
import { trans } from "laravel-vue-i18n";

const props = defineProps({
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
} = useWrapperForm(props.products);
</script>

<template>
    <Head :title="trans('New Wrapper')" />

    <PageHeader :page-title="trans('New Wrapper')" />

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
