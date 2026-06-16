<script setup>
import InputError from "@/js/Components/InputError.vue";
import InputLabel from "@/js/Components/InputLabel.vue";
import TextInput from "@/js/Components/TextInput.vue";
import { useForm } from "@inertiajs/vue3";
import { useToast } from "vue-toastification";
import { getToastOptions } from "@/js/Utils/toast";

import { trans } from "laravel-vue-i18n";

const toast = useToast();

const form = useForm({
    value: null,
    code: null,
    status: "active",
});
import { ref } from "vue";

const modalRef = ref(null);

function submitForm() {
    form.post(route("coupons.store"), {
        onSuccess: () => {
            let message = trans("ِCode.created_successfully");
            toast.success(message, getToastOptions());
            modalRef.value.close();
            form.reset("value");
            form.reset("code");
            form.reset("status");
        },
    });
}
</script>

<template>
    <Modal v-slot="{ close }" max-width="md" ref="modalRef">
        <div class="flex justify-between items-center">
            <h1 class="text-xl text-primary font-bold">
                {{ $t("New Coupon Code") }}
            </h1>
            <button type="button" class="text-red-500 font-bold" @click="close">
                <i class="ri-close-large-line text-xl"></i>
            </button>
        </div>
        <form @submit.prevent="submitForm">
            <div class="mt-4">
                <InputLabel for="code" :value="$t('Code')" />

                <TextInput
                    class="mt-2 block w-full"
                    id="code"
                    type="text"
                    v-model="form['code']"
                    :required="true"
                    :placeholder="$t('Code.placeholder')"
                />

                <InputError class="mt-2" :message="form.errors.code" />
            </div>

            <div class="mt-4">
                <InputLabel for="value" :value="$t('Discount Percentage')" />

                <TextInput
                    class="mt-2 block w-full"
                    id="value"
                    type="number"
                    v-model="form.value"
                    :required="true"
                    :placeholder="$t('DiscountValue.placeholder')"
                />

                <InputError class="mt-2" :message="form.errors.value" />
            </div>
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
                {{ $t("Code.create") }}
            </button>
        </form>
    </Modal>
</template>
