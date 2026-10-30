<script setup>
import { Head, useForm, usePage } from "@inertiajs/vue3";
import { onMounted } from "vue";
import PageHeader from "@/js/Components/Admin/PageHeader.vue";
import InputError from "@/js/Components/InputError.vue";
import InputLabel from "@/js/Components/InputLabel.vue";
import TextInput from "@/js/Components/TextInput.vue";
import SelectInput from "@/js/Components/SelectInput.vue";
import { useToast } from "vue-toastification";
import { getToastOptions } from "@/js/Utils/toast";
import { trans } from "laravel-vue-i18n";

const props = defineProps({
    product: { type: Object, required: true },
});

const product = usePage().props.product;
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
    form.patch(route("products.update", product), {
        onSuccess: () => {
            toast.success(trans("Product.updated"), getToastOptions());
        },
    });
}

function populateForm(product) {
    form.price = product.price / 1000 || "";
    form.shipping_name = product.shipping_name || "";
    form.discount_type = product.discount_type || "percentage";
    form.discount = product.discount || 0;

    product.translations.forEach((translation) => {
        if (form[translation.locale]) {
            form[translation.locale].name = translation.name || "";
        }
    });
}

onMounted(() => {
    populateForm(product);
});
</script>

<template>
    <Head :title="trans('Edit Product') + ` - ${product.name}`" />

    <PageHeader :page-title="product.name" />
    <form @submit.prevent="submitForm" class="space-y-6">
        <section
            class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6 dark:border-neutral-700 dark:bg-neutral-800 dark:shadow-black/20"
        >
            <div
                class="mb-6 flex items-start gap-3 border-b border-slate-100 pb-4 dark:border-neutral-700"
            >
                <span
                    class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-slate-100 text-lg text-slate-600 dark:bg-neutral-700 dark:text-neutral-200"
                >
                    <i class="ri-translate-2" aria-hidden="true"></i>
                </span>
                <div>
                    <h2
                        class="text-lg font-semibold tracking-tight text-slate-800 dark:text-neutral-100"
                    >
                        {{ trans("Essential Information") }}
                    </h2>
                </div>
            </div>

            <div class="grid gap-5 md:grid-cols-2">
                <div>
                    <InputLabel
                        for="en-name"
                        :value="trans('English.name')"
                        class="text-slate-700 dark:text-neutral-200"
                    />
                    <TextInput
                        variant="admin"
                        id="en-name"
                        type="text"
                        class="mt-1 block w-full"
                        v-model="form.en.name"
                        :required="true"
                    />
                    <InputError
                        class="mt-2"
                        :message="form.errors['en.name']"
                    />
                </div>

                <div>
                    <InputLabel
                        for="fr-name"
                        :value="trans('French.name')"
                        class="text-slate-700 dark:text-neutral-200"
                    />
                    <TextInput
                        variant="admin"
                        id="fr-name"
                        type="text"
                        class="mt-1 block w-full"
                        v-model="form.fr.name"
                        :required="true"
                    />
                    <InputError
                        class="mt-2"
                        :message="form.errors['fr.name']"
                    />
                </div>

                <div>
                    <InputLabel
                        for="ar-name"
                        :value="trans('Arabic.name')"
                        class="text-slate-700 dark:text-neutral-200"
                    />
                    <TextInput
                        variant="admin"
                        id="ar-name"
                        type="text"
                        class="mt-1 block w-full"
                        v-model="form.ar.name"
                        :required="true"
                    />
                    <InputError
                        class="mt-2"
                        :message="form.errors['ar.name']"
                    />
                </div>

                <div>
                    <InputLabel
                        for="shipping_name"
                        :value="trans('shipping.name')"
                        class="text-slate-700 dark:text-neutral-200"
                    />
                    <TextInput
                        variant="admin"
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
        </section>

        <section
            class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6 dark:border-neutral-700 dark:bg-neutral-800 dark:shadow-black/20"
        >
            <div
                class="mb-6 flex items-start gap-3 border-b border-slate-100 pb-4 dark:border-neutral-700"
            >
                <span
                    class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-slate-100 text-lg text-slate-600 dark:bg-neutral-700 dark:text-neutral-200"
                >
                    <i class="ri-price-tag-3-line" aria-hidden="true"></i>
                </span>
                <div>
                    <h2
                        class="text-lg font-semibold tracking-tight text-slate-800 dark:text-neutral-100"
                    >
                        {{ trans("Additional Information") }}
                    </h2>
                </div>
            </div>

            <div class="grid gap-5 md:grid-cols-2">
                <div>
                    <InputLabel
                        for="product-price"
                        :value="trans('Product.price')"
                        class="text-slate-700 dark:text-neutral-200"
                    />
                    <TextInput
                        variant="admin"
                        id="product-price"
                        type="number"
                        min="0"
                        class="mt-1 block w-full"
                        v-model="form.price"
                        placeholder="eg: 50000"
                        :required="true"
                        step="0.01"
                    />
                    <InputError class="mt-2" :message="form.errors.price" />
                </div>

                <div>
                    <InputLabel
                        for="discount-type"
                        :value="trans('Discount.type')"
                        class="text-slate-700 dark:text-neutral-200"
                    />
                    <SelectInput
                        variant="admin"
                        v-model="form.discount_type"
                        id="discount-type"
                        required
                    >
                        <option value="percentage">
                            {{ $t("Percentage") }}
                        </option>
                        <option value="fixed">{{ trans("Fixed") }}</option>
                    </SelectInput>
                    <InputError
                        class="mt-2"
                        :message="$page.props.errors.discount_type"
                    />
                </div>

                <div>
                    <InputLabel
                        for="discount"
                        :value="trans('Discount.value')"
                        class="text-slate-700 dark:text-neutral-200"
                    />
                    <TextInput
                        variant="admin"
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
        </section>

        <div class="flex justify-end">
            <button
                :disabled="form.processing"
                type="submit"
                class="w-full cursor-pointer rounded-xl border border-teal-700 bg-teal-700 px-5 py-3 font-semibold text-white shadow-sm transition duration-200 hover:-translate-y-0.5 hover:bg-opacity-90 hover:shadow-md focus:outline-none focus-visible:ring-2 focus-visible:ring-teal-500/40 disabled:cursor-not-allowed disabled:opacity-60 sm:w-1/4"
            >
                {{ trans("form.save_changes") }}
            </button>
        </div>
    </form>
</template>
