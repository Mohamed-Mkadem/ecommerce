<script setup>
import InputError from "@/js/Components/InputError.vue";
import InputLabel from "@/js/Components/InputLabel.vue";
import TextInput from "@/js/Components/TextInput.vue";
import { useForm } from "@inertiajs/vue3";
import { useToast } from "vue-toastification";
import { getToastOptions } from "@/js/Utils/toast";
import { ref } from "vue";
import { trans } from "laravel-vue-i18n";
import { useAppearanceStore } from "@/js/stores/appearance";

const toast = useToast();
const appearanceStore = useAppearanceStore();

const form = useForm({
    name: null,
});

const modalRef = ref(null);

function submitForm() {
    form.post(route("shippers.store"), {
        onSuccess: () => {
            let message = trans("Shipper.created_successfully");
            toast.success(message, getToastOptions());
            modalRef.value.close();
            form.reset("name");
        },
    });
}
</script>

<template>
    <Modal
        v-slot="{ close }"
        max-width="md"
        ref="modalRef"
        position="center"
        padding-classes="p-0"
        :panel-classes="appearanceStore.isDark
            ? 'dark max-h-70 overflow-hidden rounded-2xl border border-neutral-700 bg-neutral-800 text-neutral-100 shadow-2xl'
            : 'max-h-70 overflow-hidden rounded-2xl border border-slate-200 bg-white text-slate-800 shadow-2xl'"
    >
        <div class="max-h-70 overflow-y-auto p-5 sm:p-6">
            <div class="mb-5 flex items-center justify-between gap-4 border-b border-slate-100 pb-4 dark:border-neutral-700">
                <div class="flex items-center gap-3">
                    <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-teal-50 text-lg text-teal-700 dark:bg-neutral-700 dark:text-teal-300"><i class="ri-truck-line" aria-hidden="true"></i></span>
                    <h1 class="text-lg font-semibold tracking-tight text-slate-800 dark:text-neutral-100">{{ $t("Shipper.new") }}</h1>
                </div>
                <button type="button" :aria-label="$t('Close')" class="rounded-lg p-2 text-slate-400 transition hover:bg-slate-100 hover:text-slate-700 dark:text-neutral-400 dark:hover:bg-neutral-700 dark:hover:text-white" @click="close">
                    <i class="ri-close-large-line text-xl"></i>
                </button>
            </div>
            <form @submit.prevent="submitForm" class="space-y-4">
                <div>
                    <InputLabel class="text-slate-700 dark:text-neutral-200" for="name" :value="$t('Name')" />
                    <TextInput variant="admin" id="name" type="text" v-model="form.name" :required="true" :placeholder="$t('Shipper.placeholder')" />
                    <InputError class="mt-2" :message="form.errors.name" />
                </div>
                <button :class="{ 'opacity-60 !cursor-not-allowed': form.processing }" :disabled="form.processing" type="submit" class="inline-flex w-full cursor-pointer items-center justify-center gap-2 rounded-xl border border-teal-700 bg-teal-700 px-4 py-3 font-semibold text-white transition hover:border-teal-800 hover:bg-teal-800">
                    <i class="ri-save-3-line" aria-hidden="true"></i>
                    {{ $t("Shipper.create") }}
                </button>
            </form>
        </div>
    </Modal>
</template>
