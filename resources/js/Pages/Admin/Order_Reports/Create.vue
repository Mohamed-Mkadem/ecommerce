<script setup>
import InputError from "@/js/Components/InputError.vue";
import InputLabel from "@/js/Components/InputLabel.vue";
import TextInput from "@/js/Components/TextInput.vue";
import DatePicker from "@/js/Components/DatePicker.vue";
import { useForm } from "@inertiajs/vue3";
import { useToast } from "vue-toastification";
import { getToastOptions } from "@/js/Utils/toast";
import { trans } from "laravel-vue-i18n";
import { ref } from "vue";

const toast = useToast();
const modalRef = ref(null);
const form = useForm({ name: null, start_date: null, end_date: null });

function submitForm() {
    form.post(route("order_reports.store"), {
        onSuccess: () => {
            toast.success(trans("OrderReport.created_successfully"), getToastOptions());
            modalRef.value.close();
            form.reset();
        },
    });
}
</script>

<template>
    <Modal v-slot="{ close }" max-width="md" ref="modalRef">
        <div class="flex items-center justify-between">
            <h1 class="text-xl font-bold text-primary">{{ $t("New Order Report") }}</h1>
            <button type="button" class="font-bold text-red-500" @click="close">
                <i class="ri-close-large-line text-xl"></i>
            </button>
        </div>
        <p class="mt-2 text-sm text-slate-600">{{ $t("OrderReport.queued_notice") }}</p>
        <form @submit.prevent.stop="submitForm">
            <div class="mt-4">
                <InputLabel for="name" :value="$t('Name')" />
                <TextInput id="name" v-model="form.name" class="mt-2 block w-full" type="text" required
                    :placeholder="$t('OrderReport.name_placeholder')" />
                <InputError class="mt-2" :message="form.errors.name" />
            </div>
            <div class="mt-4">
                <InputLabel for="start_date" :value="$t('Start date')" />
                <DatePicker id="start_date" v-model="form.start_date" class="mt-2 block w-full" :required="true" />
                <InputError class="mt-2" :message="form.errors.start_date" />
            </div>
            <div class="mt-4">
                <InputLabel for="end_date" :value="$t('End date')" />
                <DatePicker id="end_date" v-model="form.end_date" class="mt-2 block w-full" :required="true" />
                <InputError class="mt-2" :message="form.errors.end_date" />
            </div>
            <button type="submit" :disabled="form.processing"
                class="mt-4 w-full rounded-lg border border-primary bg-primary px-2 py-3 font-medium text-white transition hover:bg-opacity-90"
                :class="{ 'cursor-not-allowed opacity-25': form.processing }">
                {{ $t("OrderReport.create") }}
            </button>
        </form>
    </Modal>
</template>
