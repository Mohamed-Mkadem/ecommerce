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
import { useAppearanceStore } from "@/js/stores/appearance";

const toast = useToast();
const appearanceStore = useAppearanceStore();
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
                <h1 class="text-lg font-semibold text-slate-800 dark:text-neutral-100">{{ $t("New Order Report") }}</h1>
            </div>
            <button type="button" :aria-label="$t('Close')" class="rounded-lg p-2 text-slate-400 transition hover:bg-slate-100 hover:text-slate-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-primary/40 dark:text-neutral-400 dark:hover:bg-neutral-700 dark:hover:text-white" @click="close">
                <i class="ri-close-large-line text-xl" aria-hidden="true"></i>
            </button>
        </div>
        <p class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm leading-6 text-slate-600 dark:border-neutral-700 dark:bg-neutral-700/60 dark:text-neutral-300">
            {{ $t("OrderReport.queued_notice") }}
        </p>
        <form @submit.prevent.stop="submitForm">
            <div class="mt-4">
                <InputLabel class="text-slate-700 dark:text-neutral-200" for="name" :value="$t('Name')" />
                <TextInput id="name" v-model="form.name" variant="admin" type="text" required
                    :placeholder="$t('OrderReport.name_placeholder')" />
                <InputError class="mt-2" :message="form.errors.name" />
            </div>
            <div class="mt-4">
                <InputLabel class="text-slate-700 dark:text-neutral-200" for="start_date" :value="$t('Start date')" />
                <DatePicker id="start_date" v-model="form.start_date" variant="admin" :required="true" />
                <InputError class="mt-2" :message="form.errors.start_date" />
            </div>
            <div class="mt-4">
                <InputLabel class="text-slate-700 dark:text-neutral-200" for="end_date" :value="$t('End date')" />
                <DatePicker id="end_date" v-model="form.end_date" variant="admin" :required="true" />
                <InputError class="mt-2" :message="form.errors.end_date" />
            </div>
            <button type="submit" :disabled="form.processing"
                class="mt-5 w-full rounded-xl border border-primary bg-primary px-4 py-3 font-semibold text-white shadow-sm transition hover:-translate-y-0.5 hover:bg-primary/90 hover:shadow-md focus:outline-none focus-visible:ring-2 focus-visible:ring-primary/40 disabled:cursor-not-allowed disabled:opacity-50">
                {{ $t("OrderReport.create") }}
            </button>
        </form>
    </Modal>
</template>
