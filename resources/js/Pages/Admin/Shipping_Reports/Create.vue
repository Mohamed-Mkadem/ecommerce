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
import DatePicker from "@/js/Components/DatePicker.vue";
import { useAppearanceStore } from "@/js/stores/appearance";
const props = defineProps(["states", "shippers"]);
const toast = useToast();
const appearanceStore = useAppearanceStore();

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
        :panel-classes="appearanceStore.isDark
            ? 'dark mt-4 max-h-[calc(100vh-2rem)] overflow-y-auto rounded-2xl border border-neutral-700 bg-neutral-800 text-neutral-100 shadow-2xl'
            : 'mt-4 max-h-[calc(100vh-2rem)] overflow-y-auto rounded-2xl border border-slate-200 bg-white text-slate-800 shadow-2xl'"
    >
        <div class="mb-5 flex items-center justify-between gap-4 border-b border-slate-200 pb-4 dark:border-neutral-700">
            <div class="flex items-center gap-3">
                <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-primary/10 text-xl text-primary dark:bg-neutral-700 dark:text-neutral-100">
                    <i class="ri-file-chart-line" aria-hidden="true"></i>
                </span>
                <h1 class="text-lg font-semibold text-slate-800 dark:text-neutral-100">{{ $t("New Report") }}</h1>
            </div>
            <button type="button" :aria-label="$t('Close')" class="rounded-lg p-2 text-slate-400 transition hover:bg-slate-100 hover:text-slate-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-primary/40 dark:text-neutral-400 dark:hover:bg-neutral-700 dark:hover:text-white" @click="close">
                <i class="ri-close-large-line text-xl" aria-hidden="true"></i>
            </button>
        </div>
        <form @submit.prevent="submitForm">
            <div class="mt-4 w-full">
                <InputLabel class="text-slate-700 dark:text-neutral-200" for="name" :value="$t('Name')" />

                <TextInput
                    id="name"
                    type="text"
                    v-model="form.name"
                    variant="admin"
                    :required="true"
                    :placeholder="$t('reportName.placeholder')"
                />

                <InputError class="mt-2" :message="form.errors.name" />
            </div>

            <div class="mt-4 w-full">
                <InputLabel class="text-slate-700 dark:text-neutral-200" for="date" :value="$t('Delivery_date')" />

                <DatePicker
                    v-model="form.date"
                    id="date"
                    variant="admin"
                    :required="true"
                />

                <InputError class="mt-2" :message="form.errors.date" />
                <InputError class="mt-2" :message="form.errors['date.count']" />
                <div v-if="form.errors['missing.shipper']" class="mt-3 rounded-xl border border-amber-200 bg-amber-50 px-3 py-2 dark:border-amber-900/60 dark:bg-amber-950/30">
                    <InputError
                        :message="` ${$t('Please assign shippers for the following orders : ')}  ${form.errors['missing.shipper']} `"
                    />
                </div>
            </div>

            <div class="mt-4 w-full">
                <InputLabel class="text-slate-700 dark:text-neutral-200" for="shipper" :value="$t('Shipper')" />

                <SelectInput
                    variant="admin"
                    v-model="form.shipper_id"
                    id="shipper"
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
                </SelectInput>

                <InputError class="mt-2" :message="form.errors.shipper_id" />
            </div>

            <button
                :class="{
                    'opacity-25 cursor-not-allowed': form.processing,
                }"
                :disabled="form.processing"
                type="Submit"
                class="mt-5 w-full cursor-pointer rounded-xl border border-primary bg-primary px-4 py-3 font-semibold text-white shadow-sm transition hover:-translate-y-0.5 hover:bg-primary/90 hover:shadow-md focus:outline-none focus-visible:ring-2 focus-visible:ring-primary/40 disabled:cursor-not-allowed disabled:opacity-50"
            >
                {{ $t("Report.create") }}
            </button>
        </form>
    </Modal>
</template>
