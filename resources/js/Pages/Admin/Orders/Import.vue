<script setup>
import InputError from "@/js/Components/InputError.vue";
import InputLabel from "@/js/Components/InputLabel.vue";
import { useForm } from "@inertiajs/vue3";
import { useToast } from "vue-toastification";
import { getToastOptions } from "@/js/Utils/toast";

import { trans } from "laravel-vue-i18n";
import { ref } from "vue";
const toast = useToast();

const form = useForm({
    file: null,
});
function handleFileChange(e) {
    let uploadedFile = e.target.files[0];
    if (uploadedFile) {
        form.clearErrors();

        form.file = uploadedFile;
    }
}

const modalRef = ref(null);

function submitForm() {
    form.post(route("orders.import.store"), {
        onSuccess: () => {
            let message = trans("OrdersUpdates.imported_successfully");
            toast.success(message, getToastOptions());
            modalRef.value.close();
            form.reset("file");
        },
    });
}
</script>

<template>
    <Modal
        v-slot="{ close }"
        ref="modalRef"
        max-width="md"
        panel-classes="max-h-70 md:max-h-[400px] bg-white rounded-md shadow-1 overflow-y-auto mt-4"
    >
        <div class="flex justify-between items-center">
            <h1 class="text-xl text-primary font-bold">
                {{ $t("Orders.import") }}
            </h1>
            <button type="button" class="text-red-500 font-bold" @click="close">
                <i class="ri-close-large-line text-xl"></i>
            </button>
        </div>
        <form @submit.prevent="submitForm">
            <div class="md:flex md:flex-row gap-4">
                <div class="mt-4 w-full">
                    <InputLabel for="file" :value="$t('The excel file')" />

                    <input
                        required
                        @input="handleFileChange"
                        type="file"
                        id="file"
                        class="w-full mt-1 block text-gray-400 text-sm bg-white border file:cursor-pointer cursor-pointer file:border-0 file:py-3 file:px-4 ltr:file:mr-4 rtl:file:ml-4 file:bg-gray-100 file:hover:bg-gray-200 file:text-gray-500 rounded"
                    />

                    <InputError class="mt-2" :message="form.errors.file" />
                </div>
            </div>
            <div v-if="form.hasErrors" class="mt-2">
                <div v-for="(failure, index) in form.errors" :key="index">
                    <p class="text-red-500 mb-2 text-sm" v-if="index != 'file'">
                        -
                        <span v-if="failure.includes(':')">
                            {{ $t("Problem on row ") }}</span
                        >
                        {{ failure }}
                    </p>
                </div>
            </div>
            <button
                :class="{
                    'opacity-25 cursor-not-allowed': form.processing,
                }"
                :disabled="form.processing"
                type="Submit"
                class="w-full cursor-pointer rounded-lg border border-primary bg-primary px-2 py-3 font-medium text-white transition hover:bg-opacity-90 mt-4"
            >
                {{ $t("Orders.import") }}
            </button>
        </form>
    </Modal>
</template>
