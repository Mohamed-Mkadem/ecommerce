<script setup>
import InputError from "@/js/Components/InputError.vue";
import InputLabel from "@/js/Components/InputLabel.vue";
import TextInput from "@/js/Components/TextInput.vue";
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
    <Modal v-slot="{ close }" max-width="md" ref="modalRef">
        <div class="flex justify-between items-center">
            <h1 class="text-xl text-primary font-bold">
                {{ $t("Shipping_settings.edit") }}
            </h1>
            <button type="button" class="text-red-500 font-bold" @click="close">
                <i class="ri-close-large-line text-xl"></i>
            </button>
        </div>
        <form @submit.prevent="submitForm">
            <div class="mt-4">
                <div class="flex gap-x-2 gap-y-1 flex-wrap">
                    <InputLabel
                        for="tunisDeliveryDate"
                        :value="$t('Tunis acceptance delivery date')"
                    />
                    <p class="font-semibold">
                        (
                        {{
                            props.shippingSetting
                                .tunis_acceptance_delivery_date
                        }})
                    </p>
                </div>
                <DatePicker
                    v-model="form.tunisDeliveryDate"
                    :label="$t('Tunis acceptance delivery date')"
                    :placeholder="$t('Tunis acceptance delivery date')"
                    id="tunisDeliveryDate"
                />

                <InputError
                    class="mt-2"
                    :message="form.errors['tunisDeliveryDate']"
                />
            </div>
            <div class="mt-4">
                <div class="flex gap-x-2 gap-y-1 flex-wrap">
                    <InputLabel
                        for="wilayetDeliveryDate"
                        :value="$t('The states acceptance delivery date')"
                    />
                    <p class="font-semibold">
                        (
                        {{
                            props.shippingSetting
                                .wilayet_acceptance_delivery_date
                        }})
                    </p>
                </div>
                <DatePicker
                    v-model="form.wilayetDeliveryDate"
                    :label="$t('The states acceptance delivery date')"
                    :placeholder="$t('The states acceptance delivery date')"
                    id="wilayetDeliveryDate"
                />

                <InputError
                    class="mt-2"
                    :message="form.errors['wilayetDeliveryDate']"
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
                {{ $t("form.save") }}
            </button>
        </form>
    </Modal>
</template>
