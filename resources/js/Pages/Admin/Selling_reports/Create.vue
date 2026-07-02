<script setup>
import InputError from "@/js/Components/InputError.vue";
import InputLabel from "@/js/Components/InputLabel.vue";
import TextInput from "@/js/Components/TextInput.vue";
import { useForm } from "@inertiajs/vue3";
import { useToast } from "vue-toastification";
import { getToastOptions } from "@/js/Utils/toast";
import DatePicker from "@/js/Components/DatePicker.vue";

import { trans } from "laravel-vue-i18n";

const toast = useToast();

const form = useForm({
    name: null,
    start_date: null,
    end_date: null,
});
import { ref } from "vue";

const modalRef = ref(null);

function submitForm() {
    form.post(route("selling_reports.store"), {
        onSuccess: () => {
            let message = trans("SellingReport.created_successfully");
            toast.success(message, getToastOptions());
            modalRef.value.close();
            form.reset("name");
            form.reset("start_date");
            form.reset("end_date");
        },
    });
}
</script>

<template>
    <Modal v-slot="{ close }" max-width="md" ref="modalRef">
        <div class="flex justify-between items-center">
            <h1 class="text-xl text-primary font-bold">
                {{ $t("New Selling Report") }}
            </h1>
            <button type="button" class="text-red-500 font-bold" @click="close">
                <i class="ri-close-large-line text-xl"></i>
            </button>
        </div>
        <form @submit.prevent.stop="submitForm">
            <div class="mt-4">
                <InputLabel for="name" :value="$t('Name')" />

                <TextInput
                    class="mt-2 block w-full"
                    id="name"
                    type="text"
                    v-model="form['name']"
                    :required="true"
                    :placeholder="$t('selling_report.placeholder')"
                />

                <InputError class="mt-2" :message="form.errors.name" />
            </div>

            <div class="mt-4 w-full">
                <InputLabel for="start_date" :value="$t('From : ')" />

                <DatePicker
                    v-model="form.start_date"
                    id="start_date"
                    class="mt-2 block w-full"
                    :required="true"
                />

                <InputError class="mt-2" :message="form.errors.start_date" />
            </div>
            <div class="mt-4 w-full">
                <InputLabel for="end_date" :value="$t('To : ')" />

                <DatePicker
                    v-model="form.end_date"
                    id="end_date"
                    class="mt-2 block w-full"
                    :required="true"
                />

                <InputError class="mt-2" :message="form.errors.end_date" />
            </div>
            <button
                :class="{
                    'opacity-25 cursor-not-allowed': form.processing,
                }"
                :disabled="form.processing"
                type="submit"
                class="w-full cursor-pointer rounded-lg border border-primary bg-primary px-2 py-3 font-medium text-white transition hover:bg-opacity-90 mt-4"
            >
                {{ $t("SellingReport.create") }}
            </button>
        </form>
    </Modal>
</template>
