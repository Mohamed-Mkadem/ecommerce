<script setup>
import { Head, router, useForm } from "@inertiajs/vue3";
import PageHeader from "@/js/Components/Admin/PageHeader.vue";
import InputError from "@/js/Components/InputError.vue";
import InputLabel from "@/js/Components/InputLabel.vue";
import TextInput from "@/js/Components/TextInput.vue";
import { useToast } from "vue-toastification";
import { getToastOptions } from "@/js/Utils/toast";
import { trans } from "laravel-vue-i18n";

const toast = useToast();

const form = useForm({
    en: { name: "" },
    ar: { name: "" },
    fr: { name: "" },
    price: "",
    shipping_name: "",
    discount_type: "percentage",
    discount: 0,
});

function submitForm() {
    form.post(route("products.store"), {
        onSuccess: () => {
            form.reset();
            toast.success(
                trans("Product.created_successfully"),
                getToastOptions(),
            );
        },
    });
}
</script>

<template>
    <Head :title="trans('New Product')" />

    <PageHeader :page-title="trans('New Product')"> </PageHeader>

    <form @submit.prevent="submitForm" class="space-y-8">
        <div class="bg-white px-4 py-8 rounded-md shadow-1">
            <h2 class="text-2xl mb-4 font-semibold text-graydark">
                {{ trans("Essential Information") }}
            </h2>

            <div class="mt-4">
                <div class="mt-4">
                    <InputLabel for="en-name" :value="trans('English.name')" />

                    <TextInput
                        class="mt-1 block w-full"
                        id="en-name"
                        type="text"
                        v-model="form.en['name']"
                        :required="true"
                        :placeholder="trans('English.name')"
                    />

                    <InputError
                        class="mt-2"
                        :message="$page.props.errors['en.name']"
                    />
                </div>
            </div>

            <div class="mt-4">
                <div class="mt-4">
                    <InputLabel for="fr-name" :value="trans('French.name')" />

                    <TextInput
                        id="fr-name"
                        type="text"
                        class="mt-1 block w-full"
                        v-model="form.fr.name"
                        :required="true"
                        :placeholder="trans('French.name')"
                    />

                    <InputError
                        class="mt-2"
                        :message="$page.props.errors['fr.name']"
                    />
                </div>
            </div>

            <div class="mt-4">
                <div class="mt-4">
                    <InputLabel for="ar-name" :value="trans('Arabic.name')" />

                    <TextInput
                        id="ar-name"
                        type="text"
                        class="mt-1 block w-full"
                        v-model="form.ar['name']"
                        :required="true"
                        :placeholder="trans('Arabic.name')"
                    />

                    <InputError
                        class="mt-2"
                        :message="$page.props.errors['ar.name']"
                    />
                </div>
            </div>

            <div class="mt-4">
                <InputLabel
                    for="shipping_name"
                    :value="trans('shipping.name')"
                />

                <TextInput
                    id="shipping_name"
                    type="text"
                    class="mt-1 block w-full"
                    v-model="form.shipping_name"
                    :required="true"
                    :placeholder="trans('shipping_name_placeholder')"
                />

                <InputError
                    class="mt-2"
                    :message="$page.props.errors['shipping_name']"
                />
            </div>
        </div>

        <div class="bg-white px-4 py-8 rounded-md shadow-1">
            <h2 class="text-2xl mb-4 font-semibold text-graydark">
                {{ trans("Additional Information") }}
            </h2>

            <div class="flex sm:gap-4 flex-col sm:flex-row">
                <div class="mt-4 w-full">
                    <InputLabel
                        for="product-price"
                        :value="trans('Product.price')"
                    />

                    <TextInput
                        id="product-price"
                        type="number"
                        class="mt-1 block w-full"
                        v-model="form.price"
                        :placeholder="$t('in DT')"
                        :required="false"
                        step="0.01"
                    />

                    <InputError
                        class="mt-2"
                        :message="$page.props.errors.price"
                    />
                </div>
            </div>

            <div class="flex sm:gap-4 flex-col sm:flex-row">
                <div class="mt-4 w-full">
                    <InputLabel
                        for="discount-type"
                        :value="trans('Discount.type')"
                    />

                    <select
                        class="w-full mt-1 rounded-md focus:ring-primary focus:border-primary border-editor text-primary"
                        v-model="form.discount_type"
                        id="discount-type"
                        required
                    >
                        <option value="percentage">
                            {{ $t("Percentage") }}
                        </option>
                        <option value="fixed">
                            {{ trans("Fixed") }}
                        </option>
                    </select>

                    <InputError
                        class="mt-2"
                        :message="$page.props.errors.discount_type"
                    />
                </div>

                <div class="mt-4 w-full">
                    <InputLabel
                        for="discount"
                        :value="trans('Discount.value')"
                    />

                    <TextInput
                        id="discount"
                        type="number"
                        class="mt-1 block w-full"
                        v-model="form.discount"
                        :placeholder="$t('Discount.value_placeholder')"
                        :required="false"
                        step="0.01"
                    />

                    <InputError
                        class="mt-2"
                        :message="$page.props.errors.discount"
                    />
                </div>
            </div>
        </div>

        <div class="flex justify-end">
            <input
                :class="{
                    'opacity-25 cursor-not-allowed': form.processing,
                }"
                :disabled="form.processing"
                type="Submit"
                :value="$t('Product.create')"
                class="w-full sm:w-1/4 cursor-pointer rounded-lg border border-primary bg-primary py-2 px-4 font-medium text-white transition hover:bg-opacity-90 mt-4"
            />
        </div>
    </form>
</template>
