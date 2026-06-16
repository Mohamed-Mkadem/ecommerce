<script setup>
import InputError from "@/js/Components/InputError.vue";
import InputLabel from "@/js/Components/InputLabel.vue";
import TextInput from "@/js/Components/TextInput.vue";
import { useForm } from "@inertiajs/vue3";
import { useToast } from "vue-toastification";
import { getToastOptions } from "@/js/Utils/toast";

import { trans } from "laravel-vue-i18n";
const props = defineProps({
    state: {
        required: true,
        type: Object,
    },
});
const toast = useToast();

const form = useForm({
    price: props.state.shipping_cost / 1000,
    return_cost: props.state.return_cost / 1000,
    delivery_cost: props.state.delivery_cost / 1000,
});
import { ref } from "vue";

const modalRef = ref(null);

function submitForm() {
    form.put(route("states.update", props.state), {
        onSuccess: () => {
            let message = trans("ShippingCost.updated_successfully");
            toast.success(message, getToastOptions());
            modalRef.value.close();
            form.reset("price");
        },
    });
}
</script>

<template>
    <Modal v-slot="{ close }" max-width="md" ref="modalRef">
        <div class="flex justify-between items-center">
            <h1 class="text-xl text-primary font-bold">
                {{ $t("State.edit") }}
            </h1>
            <button type="button" class="text-red-500 font-bold" @click="close">
                <i class="ri-close-large-line text-xl"></i>
            </button>
        </div>
        <form @submit.prevent="submitForm">
            <div class="mt-4">
                <InputLabel
                    for="price"
                    :value="$t('Shipping Cost : (in DT)')"
                />

                <TextInput
                    class="mt-2 block w-full"
                    id="price"
                    type="number"
                    v-model="form['price']"
                    :required="true"
                    :placeholder="$t('Shipping.cost')"
                    step="0.01"
                />

                <InputError class="mt-2" :message="form.errors['price']" />
            </div>
            <div class="mt-4">
                <InputLabel
                    for="delivery_cost"
                    :value="`${$t('delivery_cost')} : (${$t('in DT')})`"
                />

                <TextInput
                    class="mt-2 block w-full"
                    id="delivery_cost"
                    type="number"
                    v-model="form['delivery_cost']"
                    :required="true"
                    :placeholder="$t('Shipping.cost')"
                    step="0.01"
                />

                <InputError
                    class="mt-2"
                    :message="form.errors['delivery_cost']"
                />
            </div>
            <div class="mt-4">
                <InputLabel
                    for="return_cost"
                    :value="`${$t('return_cost')} : (${$t('in DT')})`"
                />

                <TextInput
                    class="mt-2 block w-full"
                    id="return_cost"
                    type="number"
                    v-model="form['return_cost']"
                    :required="true"
                    :placeholder="$t('Shipping.cost')"
                    step="0.01"
                />

                <InputError
                    class="mt-2"
                    :message="form.errors['return_cost']"
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
