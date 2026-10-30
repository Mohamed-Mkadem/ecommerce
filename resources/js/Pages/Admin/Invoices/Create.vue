<script setup>
import InputError from "@/js/Components/InputError.vue";
import InputLabel from "@/js/Components/InputLabel.vue";
import TextInput from "@/js/Components/TextInput.vue";
import SelectInput from "@/js/Components/SelectInput.vue";
import { useForm } from "@inertiajs/vue3";
import { useToast } from "vue-toastification";
import { getToastOptions } from "@/js/Utils/toast";
import { ref } from "vue";
import { trans } from "laravel-vue-i18n";
import { useAppearanceStore } from "@/js/stores/appearance";

const toast = useToast();
const appearanceStore = useAppearanceStore();

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
        max-width="lg"
        position="center"
        padding-classes="p-0"
        :panel-classes="appearanceStore.isDark
            ? 'dark max-h-115 overflow-hidden rounded-2xl border border-neutral-700 bg-neutral-800 text-neutral-100 shadow-2xl'
            : 'max-h-115 overflow-hidden rounded-2xl border border-slate-200 bg-white text-slate-800 shadow-2xl'"
    >
        <div class="max-h-115 overflow-y-auto p-5 sm:p-6">
            <div class="mb-5 flex items-center justify-between gap-4 border-b border-slate-100 pb-4 dark:border-neutral-700">
                <div class="flex items-center gap-3">
                    <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-teal-50 text-lg text-teal-700 dark:bg-neutral-700 dark:text-teal-300"><i class="ri-receipt-line" aria-hidden="true"></i></span>
                    <h1 class="text-lg font-semibold tracking-tight text-slate-800 dark:text-neutral-100">{{ $t("Invoice.new") }}</h1>
                </div>
                <button type="button" :aria-label="$t('Close')" class="rounded-lg p-2 text-slate-400 transition hover:bg-slate-100 hover:text-slate-700 dark:text-neutral-400 dark:hover:bg-neutral-700 dark:hover:text-white" @click="close">
                    <i class="ri-close-large-line text-xl"></i>
                </button>
            </div>
            <form @submit.prevent="submitForm" class="space-y-4">
                <div>
                    <InputLabel class="text-slate-700 dark:text-neutral-200" for="title" :value="$t('Title')" />
                    <TextInput variant="admin" id="title" type="text" v-model="form.title" :required="true" :placeholder="$t('Invoice.title.placeholder')" />
                    <InputError class="mt-2" :message="form.errors.title" />
                </div>
                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <InputLabel class="text-slate-700 dark:text-neutral-200" for="invoice-type" :value="$t('Type')" />
                        <SelectInput variant="admin" required id="invoice-type" v-model="form.type">
                            <option value="revenue">{{ $t("revenue") }}</option>
                            <option value="expense">{{ $t("expense") }}</option>
                        </SelectInput>
                        <InputError class="mt-2" :message="form.errors.type" />
                    </div>
                    <div>
                        <InputLabel class="text-slate-700 dark:text-neutral-200" for="invoice-category" :value="$t('Category')" />
                        <SelectInput variant="admin" required id="invoice-category" v-model="form.category">
                            <option value="marketing">{{ $t("marketing") }}</option>
                            <option value="payments">{{ $t("payments") }}</option>
                            <option value="daily sales">{{ $t("daily sales") }}</option>
                            <option value="operating costs">{{ $t("operating costs") }}</option>
                            <option value="other">{{ $t("other") }}</option>
                        </SelectInput>
                        <InputError class="mt-2" :message="form.errors.category" />
                    </div>
                </div>
                <div>
                    <InputLabel class="text-slate-700 dark:text-neutral-200" for="amount" :value="`${$t('Amount')} (${$t('in DT')})`" />
                    <TextInput variant="admin" id="amount" type="number" v-model="form.amount" :required="true" :placeholder="`${$t('Amount')} ${$t('in DT')}`" step="0.01" />
                    <InputError class="mt-2" :message="form.errors.amount" />
                </div>
                <div>
                    <InputLabel class="text-slate-700 dark:text-neutral-200" for="description" :value="`${$t('Description')} (${$t('optional')})`" />
                    <textarea id="description" class="mt-1 block h-40 w-full resize-none overflow-y-auto rounded-xl border border-slate-200 bg-slate-50/50 p-3 text-slate-800 shadow-sm placeholder:text-neutral-400 focus:border-primary focus:ring-primary/20 dark:border-neutral-600 dark:bg-neutral-700 dark:text-neutral-100 dark:placeholder:text-neutral-400 dark:focus:border-neutral-400 dark:focus:ring-neutral-500/30" v-model="form.description" :placeholder="$t('description_placeholder')"></textarea>
                    <InputError class="mt-2" :message="form.errors.description" />
                </div>
                <button :class="{ 'opacity-60 !cursor-not-allowed': form.processing }" :disabled="form.processing" type="submit" class="inline-flex w-full cursor-pointer items-center justify-center gap-2 rounded-xl border border-teal-700 bg-teal-700 px-4 py-3 font-semibold text-white transition hover:border-teal-800 hover:bg-teal-800">
                    <i class="ri-save-3-line" aria-hidden="true"></i>
                    {{ $t("Invoice.create") }}
                </button>
            </form>
        </div>
    </Modal>
</template>
