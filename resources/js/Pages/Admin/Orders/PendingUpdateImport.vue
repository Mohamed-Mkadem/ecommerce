<script setup>
import InputError from "@/js/Components/InputError.vue";
import InputLabel from "@/js/Components/InputLabel.vue";
import FileInput from "@/js/Components/FileInput.vue";
import { useForm } from "@inertiajs/vue3";
import { useToast } from "vue-toastification";
import { getToastOptions } from "@/js/Utils/toast";

import { trans } from "laravel-vue-i18n";
import { ref } from "vue";
import { useAppearanceStore } from "@/js/stores/appearance";
const toast = useToast();
const appearanceStore = useAppearanceStore();

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
    form.post(route("orders.pendingUpdate.import.store"), {
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
        :panel-classes="appearanceStore.isDark ? 'dark mt-4 max-h-[calc(100vh-2rem)] overflow-y-auto rounded-2xl border border-neutral-700 bg-neutral-800 text-neutral-100 shadow-2xl' : 'mt-4 max-h-[calc(100vh-2rem)] overflow-y-auto rounded-2xl border border-slate-200 bg-white text-slate-800 shadow-2xl'"
    >
        <div class="mb-5 flex items-start justify-between gap-4 border-b border-slate-200 pb-4 dark:border-neutral-700">
            <div class="flex items-center gap-3">
                <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-primary/10 text-xl text-primary dark:bg-neutral-700 dark:text-neutral-100">
                    <i class="ri-upload-cloud-2-line" aria-hidden="true"></i>
                </span>
                <div>
                    <h1 class="text-lg font-semibold text-slate-800 dark:text-neutral-100">{{ $t("Mark Orders as pending") }}</h1>
                    <p class="mt-0.5 text-sm text-slate-500 dark:text-neutral-400">{{ $t("The excel file") }}</p>
                </div>
            </div>
            <button type="button" :aria-label="$t('Close')" class="rounded-lg p-2 text-slate-400 transition hover:bg-slate-100 hover:text-slate-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-primary/40 dark:text-neutral-400 dark:hover:bg-neutral-700 dark:hover:text-white" @click="close">
                <i class="ri-close-large-line text-xl" aria-hidden="true"></i>
            </button>
        </div>
        <form @submit.prevent="submitForm">
            <div class="md:flex md:flex-row gap-4">
                <div class="mt-4 w-full">
                    <InputLabel for="file" :value="$t('The excel file')" />
                    <a
                        href="/storage/downloadable/import_pending_template.xlsx"
                        target="_blank"
                        rel="noopener noreferrer"
                        download
                        class="mt-1 inline-flex items-center gap-1 text-sm font-medium text-primary transition-colors hover:underline dark:text-neutral-300 dark:hover:text-white"
                    >
                        <i class="ri-download-2-line" aria-hidden="true"></i>
                        {{ $t("Download template") }}
                    </a>

                    <FileInput
                        required
                        @input="handleFileChange"
                        type="file"
                        id="file"
                        variant="admin"
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
                class="mt-5 w-full cursor-pointer rounded-xl border border-teal-700 bg-teal-700 px-4 py-3 font-semibold text-white shadow-sm transition duration-200 hover:-translate-y-0.5 hover:bg-opacity-90 hover:shadow-md focus:outline-none focus-visible:ring-2 focus-visible:ring-teal-500/40"
            >
                {{ $t("Orders.import") }}
            </button>
        </form>
    </Modal>
</template>
