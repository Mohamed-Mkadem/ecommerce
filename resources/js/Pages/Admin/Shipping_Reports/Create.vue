<script setup>
import InputError from "@/js/Components/InputError.vue";
import InputLabel from "@/js/Components/InputLabel.vue";
import TextInput from "@/js/Components/TextInput.vue";
import { useForm } from "@inertiajs/vue3";
import { useToast } from "vue-toastification";
import { getToastOptions } from "@/js/Utils/toast";
import { ref } from "vue";
import { trans } from "laravel-vue-i18n";
import DatePicker from "@/js/Components/DatePicker.vue";
const props = defineProps(["states", "shippers"]);
const toast = useToast();

const form = useForm({
    name: null,
    date: null,
    shipper_id: null,
});

const modalRef = ref(null);

function submitForm() {
    form.post(route("shipping_reports.store"), {
        onSuccess: () => {
            let message = trans("ReportRequest.created_successfully");
            toast.success(message, { ...getToastOptions(), timeout: 10000 });
            modalRef.value.close();
            form.reset("name");
            form.reset("date");
        },
    });
}
</script>

<template>
    <Modal
        v-slot="{ close }"
        ref="modalRef"
        max-width="md"
        panel-classes="bg-white  rounded overflow-y-auto max-h-70 md:max-h-100 mt-auto"
    >
        <div class="flex justify-between items-center">
            <h1 class="text-xl text-primary font-bold">
                {{ $t("New Report") }}
            </h1>
            <button type="button" class="text-red-500 font-bold" @click="close">
                <i class="ri-close-large-line text-xl"></i>
            </button>
        </div>
        <form @submit.prevent="submitForm">
            <div class="mt-4 w-full">
                <InputLabel for="name" :value="$t('Name')" />

                <TextInput
                    class="mt-2 block w-full"
                    id="name"
                    type="text"
                    v-model="form.name"
                    :required="true"
                    :placeholder="$t('reportName.placeholder')"
                />

                <InputError class="mt-2" :message="form.errors.name" />
            </div>

            <div class="mt-4 w-full">
                <InputLabel for="date" :value="$t('Delivery_date')" />

                <DatePicker
                    v-model="form.date"
                    id="date"
                    class="mt-2 block w-full"
                    :required="true"
                />

                <InputError class="mt-2" :message="form.errors.date" />
                <InputError class="mt-2" :message="form.errors['date.count']" />
                <div v-if="form.errors['missing.shipper']" class="flex gap-2">
                    <InputError
                        class="mt-2"
                        :message="` ${$t('Please assign shippers for the following orders : ')}  ${form.errors['missing.shipper']} `"
                    />
                </div>
            </div>

            <div class="mt-4 w-full">
                <InputLabel for="shipper" :value="$t('Shipper')" />

                <select
                    v-model="form.shipper_id"
                    id="shipper"
                    class="w-full mt-2 rounded-md focus:ring-primary focus:border-primary border-editor text-primary"
                    :required="true"
                >
                    <option :value="null">{{ $t("Select a shipper") }}</option>
                    <option value="gt">{{ $t("Grand Tunis") }}</option>
                    <option
                        :value="shipper.id"
                        v-for="shipper in shippers"
                        :key="shipper.id"
                    >
                        {{ shipper.name }}
                    </option>
                </select>

                <InputError class="mt-2" :message="form.errors.shipper_id" />
            </div>

            <button
                :class="{
                    'opacity-25 cursor-not-allowed': form.processing,
                }"
                :disabled="form.processing"
                type="Submit"
                class="w-full cursor-pointer rounded-lg border border-primary bg-primary px-2 py-3 font-medium text-white transition hover:bg-opacity-90 mt-4"
            >
                {{ $t("Report.create") }}
            </button>
        </form>
    </Modal>
</template>
