<script setup>
import InputError from "@/js/Components/InputError.vue";
import InputLabel from "@/js/Components/InputLabel.vue";
import TextInput from "@/js/Components/TextInput.vue";
import { useForm } from "@inertiajs/vue3";
import { useToast } from "vue-toastification";
import { getToastOptions } from "@/js/Utils/toast";
import DatePicker from "@/js/Components/DatePicker.vue";
import { useAppearanceStore } from "@/js/stores/appearance";
import { ref } from "vue";
import { trans } from "laravel-vue-i18n";

const toast = useToast();
const appearanceStore = useAppearanceStore();

const form = useForm({
    name: null,
    start_date: null,
    end_date: null,
});

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
    <Modal
        v-slot="{ close }"
        max-width="md"
        ref="modalRef"
        :panel-classes="appearanceStore.isDark
            ? 'dark rounded-2xl border border-neutral-700 bg-neutral-800 text-neutral-100 shadow-2xl'
            : 'rounded-2xl border border-slate-200 bg-white text-slate-800 shadow-2xl'"
    >
        <div class="mb-5 flex items-center justify-between gap-4 border-b border-slate-200 pb-4 dark:border-neutral-700">
            <div class="flex items-center gap-3">
                <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-primary/10 text-xl text-primary dark:bg-neutral-700 dark:text-neutral-100">
                    <i class="ri-file-chart-line" aria-hidden="true"></i>
                </span>
                <h1 class="text-lg font-semibold text-slate-800 dark:text-neutral-100">
                    {{ $t("New Selling Report") }}
                </h1>
            </div>
            <button type="button" :aria-label="$t('Close')" class="rounded-lg p-2 text-slate-400 transition hover:bg-slate-100 hover:text-slate-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-primary/40 dark:text-neutral-400 dark:hover:bg-neutral-700 dark:hover:text-white" @click="close">
                <i class="ri-close-large-line text-xl"></i>
            </button>
        </div>
        <form @submit.prevent.stop="submitForm">
            <div class="mt-4">
                <InputLabel class="text-slate-700 dark:text-neutral-200" for="name" :value="$t('Name')" />

                <TextInput
                    variant="admin"
                    id="name"
                    type="text"
                    v-model="form['name']"
                    :required="true"
                    :placeholder="$t('selling_report.placeholder')"
                />

                <InputError class="mt-2" :message="form.errors.name" />
            </div>

            <div class="mt-4 w-full">
                <InputLabel class="text-slate-700 dark:text-neutral-200" for="start_date" :value="$t('From : ')" />

                <DatePicker
                    v-model="form.start_date"
                    id="start_date"
                    variant="admin"
                    :required="true"
                />

                <InputError class="mt-2" :message="form.errors.start_date" />
            </div>
            <div class="mt-4 w-full">
                <InputLabel class="text-slate-700 dark:text-neutral-200" for="end_date" :value="$t('To : ')" />

                <DatePicker
                    v-model="form.end_date"
                    id="end_date"
                    variant="admin"
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
                class="mt-5 w-full cursor-pointer rounded-xl border border-teal-700 bg-teal-700 px-4 py-3 font-semibold text-white shadow-sm transition hover:-translate-y-0.5 hover:bg-teal-800 hover:shadow-md focus:outline-none focus-visible:ring-2 focus-visible:ring-teal-500/40 disabled:cursor-not-allowed disabled:opacity-50"
            >
                {{ $t("SellingReport.create") }}
            </button>
        </form>
    </Modal>
</template>
