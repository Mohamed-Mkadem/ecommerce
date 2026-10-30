<script setup>
import InputError from "@/js/Components/InputError.vue";
import InputLabel from "@/js/Components/InputLabel.vue";
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
    form.post(route("clients.import.store"), {
        onSuccess: () => {
            let message = trans("Clients.imported_successfully");
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
        padding-classes="p-0"
        :panel-classes="appearanceStore.isDark
            ? 'dark overflow-hidden rounded-2xl border border-neutral-700 bg-neutral-800 text-neutral-100 shadow-2xl'
            : 'overflow-hidden rounded-2xl border border-slate-200 bg-white text-slate-700 shadow-2xl'"
    >
        <div class="p-6 text-slate-700 dark:text-neutral-200">
            <div class="flex items-center justify-between border-b border-slate-100 pb-4 dark:border-neutral-700">
                <h1 class="text-xl font-semibold tracking-tight text-slate-800 dark:text-neutral-100">
                    {{ $t("Clients.import") }}
                </h1>
                <button type="button" class="inline-flex h-9 w-9 items-center justify-center rounded-lg text-slate-500 transition hover:bg-slate-100 hover:text-slate-800 dark:text-neutral-400 dark:hover:bg-neutral-700 dark:hover:text-white" @click="close" aria-label="Close">
                    <i class="ri-close-large-line text-xl"></i>
                </button>
            </div>
            <form @submit.prevent="submitForm">
                <div class="mt-5">
                    <InputLabel for="file" :value="$t('The excel file')" />
                    <a
                        href="/storage/downloadable/import_clients_template.xlsx"
                        target="_blank"
                        rel="noopener noreferrer"
                        download
                        class="mt-1 inline-flex items-center gap-1 text-sm font-medium text-teal-700 transition-colors hover:text-teal-800 hover:underline dark:text-teal-300 dark:hover:text-teal-200"
                    >
                        <i class="ri-download-2-line" aria-hidden="true"></i>
                        {{ $t("Download template") }}
                    </a>

                    <input
                        @input="handleFileChange"
                        type="file"
                        id="file"
                        class="mt-2 block w-full cursor-pointer rounded-xl border border-slate-200 bg-slate-50/50 text-sm text-slate-600 shadow-sm transition file:mr-4 file:cursor-pointer file:border-0 file:bg-slate-100 file:px-4 file:py-3 file:font-medium file:text-slate-700 hover:file:bg-slate-200 dark:border-neutral-600 dark:bg-neutral-700 dark:text-neutral-200 dark:file:bg-neutral-600 dark:file:text-neutral-100 dark:hover:file:bg-neutral-500"
                    />

                    <InputError class="mt-2" :message="form.errors.file" />
                </div>
                <div v-if="form.hasErrors" class="mt-3 rounded-xl bg-red-50 p-3 dark:bg-red-500/10">
                    <div v-for="(failure, index) in form.errors" :key="index">
                        <p class="mb-2 text-sm text-red-700 last:mb-0 dark:text-red-300" v-if="index != 'file'">
                            - {{ $t("Problem on row ") }} {{ failure }}
                        </p>
                    </div>
                </div>
                <button
                    :class="{ 'opacity-60 !cursor-not-allowed': form.processing }"
                    :disabled="form.processing"
                    type="Submit"
                    class="mt-6 inline-flex w-full cursor-pointer items-center justify-center gap-2 rounded-xl border border-teal-700 bg-teal-700 px-4 py-3 font-semibold text-white transition hover:border-teal-800 hover:bg-teal-800"
                >
                    <i class="ri-upload-2-line" aria-hidden="true"></i>
                    {{ $t("Clients.import") }}
                </button>
            </form>
        </div>
    </Modal>
</template>
