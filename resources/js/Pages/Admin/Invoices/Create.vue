<script setup>
import InputError from "@/js/Components/InputError.vue";
import InputLabel from "@/js/Components/InputLabel.vue";
import TextInput from "@/js/Components/TextInput.vue";
import { useForm } from "@inertiajs/vue3";
import { useToast } from "vue-toastification";
import { getToastOptions } from "@/js/Utils/toast";
import { ref } from "vue";
import { trans } from "laravel-vue-i18n";

const toast = useToast();

const form = useForm({
    title: null,
    category: "other",
    type: "revenue",
    amount: null,
    description: null,
});

const modalRef = ref(null);

function submitForm() {
    form.post(route("invoices.store"), {
        onSuccess: () => {
            let message = trans("Invoice.created_successfully");
            toast.success(message, getToastOptions());
            modalRef.value.close();
            form.reset("title");
            form.reset("amount");
            form.reset("description");
            form.reset("category");
            form.reset("type");
        },
    });
}
</script>

<template>
    <Modal
        v-slot="{ close }"
        ref="modalRef"
        panel-classes="bg-white rounded mx-auto md:max-w-lg max-h-100  overflow-y-auto"
    >
        <div class="flex justify-between items-center">
            <h1 class="text-xl text-primary font-bold">
                {{ $t("Invoice.new") }}
            </h1>
            <button type="button" class="text-red-500 font-bold" @click="close">
                <i class="ri-close-large-line text-xl"></i>
            </button>
        </div>
        <form @submit.prevent="submitForm">
            <div class="mt-4">
                <InputLabel for="title" :value="$t('Title')" />

                <TextInput
                    class="mt-2 block w-full"
                    id="title"
                    type="text"
                    v-model="form['title']"
                    :required="true"
                    :placeholder="$t('Invoice.title.placeholder')"
                />

                <InputError class="mt-2" :message="form.errors['title']" />
            </div>

            <div class="mt-4">
                <InputLabel
                    class="text-slate-600"
                    for="invoice-type"
                    :value="$t('Type')"
                />

                <select
                    required
                    id="invoice-type"
                    v-model="form.type"
                    class="w-full mt-2 rounded-md focus:ring-primary focus:border-primary border-editor text-primary"
                >
                    <option value="revenue">{{ $t("revenue") }}</option>
                    <option value="expense">{{ $t("expense") }}</option>
                </select>

                <InputError class="mt-2" :message="form.errors['type']" />
            </div>

            <div class="mt-4">
                <InputLabel
                    class="text-slate-600"
                    for="invoice-category"
                    :value="$t('Category')"
                />

                <select
                    required
                    id="invoice-category"
                    v-model="form.category"
                    class="w-full mt-2 rounded-md focus:ring-primary focus:border-primary border-editor text-primary"
                >
                    <option value="marketing">{{ $t("marketing") }}</option>
                    <option value="payments">{{ $t("payments") }}</option>
                    <option value="daily sales">{{ $t("daily sales") }}</option>
                    <option value="operating costs">
                        {{ $t("operating costs") }}
                    </option>
                    <option value="other">{{ $t("other") }}</option>
                </select>

                <InputError class="mt-2" :message="form.errors['category']" />
            </div>

            <div class="mt-4">
                <InputLabel
                    for="amount"
                    :value="`${$t('Amount')} (${$t('in DT')})`"
                />

                <TextInput
                    class="mt-2 block w-full"
                    id="amount"
                    type="number"
                    v-model="form['amount']"
                    :required="true"
                    :placeholder="`${$t('Amount')} ${$t('in DT')}`"
                    step="0.01"
                />

                <InputError class="mt-2" :message="form.errors['amount']" />
            </div>

            <div class="mt-4">
                <InputLabel
                    for="description"
                    :value="`${$t('Description')} (${$t('optional')})`"
                    class="text-slate-600"
                />

                <textarea
                    id="description"
                    class="mt-2 block w-full text-primary rounded-md border-editor shadow-sm focus:border-primary focus:ring-primary h-25 resize-none"
                    v-model="form.description"
                    :placeholder="$t('description_placeholder')"
                >
                </textarea>

                <InputError
                    class="mt-2"
                    :message="form.errors['description']"
                />
            </div>

            <button
                :class="{
                    'opacity-25 cursor-not-allowed': form.processing,
                }"
                :disabled="form.processing"
                type="Submit"
                class="w-full cursor-pointer rounded-lg border border-primary bg-primary px-2 py-3 font-medium text-white transition hover:bg-opacity-90 mt-4"
            >
                {{ $t("Invoice.create") }}
            </button>
        </form>
    </Modal>
</template>
