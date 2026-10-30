<script setup>
import InputError from "@/js/Components/InputError.vue";
import InputLabel from "@/js/Components/InputLabel.vue";
import { useAppearanceStore } from "@/js/stores/appearance";
import { useForm } from "@inertiajs/vue3";
import { useToast } from "vue-toastification";
import { getToastOptions } from "@/js/Utils/toast";
import { trans } from "laravel-vue-i18n";
import { ref } from "vue";
import DatePicker from "@/js/Components/DatePicker.vue";

const props = defineProps({
    shippingSetting: {
        required: true,
        type: Object,
    },
});
const toast = useToast();
const appearanceStore = useAppearanceStore();

const form = useForm({
    tunisDeliveryDate: props.shippingSetting.tunis_acceptance_delivery_date,
    wilayetDeliveryDate: props.shippingSetting.wilayet_acceptance_delivery_date,
});

const modalRef = ref(null);

function submitForm() {
    form.put(route("shippingSettings.update", props.shippingSetting), {
        onSuccess: () => {
            let message = trans("shipping_settings.updated_successfully");
            toast.success(message, getToastOptions());
            modalRef.value.close();
            form.reset("tunisDeliveryDate");
            form.reset("wilayetDeliveryDate");
        },
    });
}
</script>

<template>
    <Modal
        v-slot="{ close }"
        max-width="lg"
        ref="modalRef"
        position="center"
        padding-classes="p-0"
        :panel-classes="appearanceStore.isDark
            ? 'dark max-h-115 overflow-hidden rounded-2xl border border-neutral-700 bg-neutral-800 text-neutral-100 shadow-2xl'
            : 'max-h-115 overflow-hidden rounded-2xl border border-slate-200 bg-white text-slate-800 shadow-2xl'"
    >
        <div class="max-h-115 overflow-y-auto p-5 sm:p-6">
            <div class="mb-5 flex items-center justify-between gap-4 border-b border-slate-100 pb-4 dark:border-neutral-700">
                <div class="flex items-center gap-3">
                    <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-teal-50 text-lg text-teal-700 dark:bg-neutral-700 dark:text-teal-300"><i class="ri-calendar-event-line" aria-hidden="true"></i></span>
                    <h1 class="text-lg font-semibold tracking-tight text-slate-800 dark:text-neutral-100">{{ $t("Shipping_settings.edit") }}</h1>
                </div>
                <button type="button" :aria-label="$t('Close')" class="rounded-lg p-2 text-slate-400 transition hover:bg-slate-100 hover:text-slate-700 dark:text-neutral-400 dark:hover:bg-neutral-700 dark:hover:text-white" @click="close">
                    <i class="ri-close-large-line text-xl"></i>
                </button>
            </div>
            <form @submit.prevent="submitForm" class="space-y-4">
                <div>
                    <div class="mb-1 flex flex-wrap items-center gap-x-2 gap-y-1">
                        <InputLabel class="text-slate-700 dark:text-neutral-200" for="tunisDeliveryDate" :value="$t('Tunis acceptance delivery date')" />
                        <span class="text-xs font-medium text-slate-500 dark:text-neutral-400">{{ props.shippingSetting.tunis_acceptance_delivery_date }}</span>
                    </div>
                    <DatePicker variant="admin" v-model="form.tunisDeliveryDate" :label="$t('Tunis acceptance delivery date')" :placeholder="$t('Tunis acceptance delivery date')" id="tunisDeliveryDate" />
                    <InputError class="mt-2" :message="form.errors.tunisDeliveryDate" />
                </div>
                <div>
                    <div class="mb-1 flex flex-wrap items-center gap-x-2 gap-y-1">
                        <InputLabel class="text-slate-700 dark:text-neutral-200" for="wilayetDeliveryDate" :value="$t('The states acceptance delivery date')" />
                        <span class="text-xs font-medium text-slate-500 dark:text-neutral-400">{{ props.shippingSetting.wilayet_acceptance_delivery_date }}</span>
                    </div>
                    <DatePicker variant="admin" v-model="form.wilayetDeliveryDate" :label="$t('The states acceptance delivery date')" :placeholder="$t('The states acceptance delivery date')" id="wilayetDeliveryDate" />
                    <InputError class="mt-2" :message="form.errors.wilayetDeliveryDate" />
                </div>
                <button :class="{ 'opacity-60 !cursor-not-allowed': form.processing }" :disabled="form.processing" type="submit" class="inline-flex w-full cursor-pointer items-center justify-center gap-2 rounded-xl border border-teal-700 bg-teal-700 px-4 py-3 font-semibold text-white transition hover:border-teal-800 hover:bg-teal-800">
                    <i class="ri-save-3-line" aria-hidden="true"></i>
                    {{ $t("form.save") }}
                </button>
            </form>
        </div>
    </Modal>
</template>
