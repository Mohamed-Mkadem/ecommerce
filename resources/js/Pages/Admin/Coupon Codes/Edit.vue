<script setup>
import InputError from "@/js/Components/InputError.vue";
import InputLabel from "@/js/Components/InputLabel.vue";
import { useForm } from "@inertiajs/vue3";
import { useToast } from "vue-toastification";
import { getToastOptions } from "@/js/Utils/toast";

import { trans } from "laravel-vue-i18n";

const toast = useToast();
const props = defineProps(["code"]);
const form = useForm({
    status: props.code.status,
});
import { ref } from "vue";

const modalRef = ref(null);

function submitForm() {
    form.patch(route("coupons.update", props.code), {
        onSuccess: () => {
            let message = trans("Code.updated_successfully");
            toast.success(message, getToastOptions());
            modalRef.value.close();

            form.reset("status");
        },
    });
}
</script>

<template>
    <Modal v-slot="{ close }" max-width="md" ref="modalRef">
        <div class="flex justify-between items-center">
            <h1 class="text-xl text-primary font-bold">
                {{ $t("Edit Coupon Code") }}
            </h1>
            <button type="button" class="text-red-500 font-bold" @click="close">
                <i class="ri-close-large-line text-xl"></i>
            </button>
        </div>
        <form @submit.prevent="submitForm">
            <div class="mt-4">
                <InputLabel for="status" :value="$t('Status')" />

                <select
                    required
                    v-model="form.status"
                    id="status"
                    class="w-full mt-1 rounded-md focus:ring-primary focus:border-primary border-editor text-primary"
                >
                    <option value="active" class="capitalize">
                        {{ $t("active") }}
                    </option>
                    <option value="inactive" class="capitalize">
                        {{ $t("inactive") }}
                    </option>
                </select>

                <InputError class="mt-2" :message="form.errors.status" />
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
