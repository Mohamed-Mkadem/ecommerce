<script setup>
import InputError from "@/js/Components/InputError.vue";
import InputLabel from "@/js/Components/InputLabel.vue";
import SelectInput from "@/js/Components/SelectInput.vue";
import { useForm } from "@inertiajs/vue3";
import { useToast } from "vue-toastification";
import { getToastOptions } from "@/js/Utils/toast";

import { trans } from "laravel-vue-i18n";
import { useAppearanceStore } from "@/js/stores/appearance";

const toast = useToast();
const appearanceStore = useAppearanceStore();
const props = defineProps(["code"]);
const form = useForm({
    status: props.code.status,
});
import { ref } from "vue";

const modalRef = ref(null);

function submitForm() {
    form.patch(route("coupons.update", props.code), {
        onSuccess: () => {
            let message = trans("Code.updated_successfully");
            toast.success(message, getToastOptions());
            modalRef.value.close();

            form.reset("status");
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
            ? 'dark max-h-115 overflow-hidden rounded-2xl border border-neutral-700 bg-neutral-800 text-neutral-100 shadow-2xl'
            : 'max-h-115 overflow-hidden rounded-2xl border border-slate-200 bg-white text-slate-800 shadow-2xl'"
    >
        <div class="max-h-115 overflow-y-auto p-5 sm:p-6">
            <div class="mb-5 flex items-center justify-between gap-4 border-b border-slate-100 pb-4 dark:border-neutral-700">
                <div class="flex items-center gap-3">
                    <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-teal-50 text-lg text-teal-700 dark:bg-neutral-700 dark:text-teal-300"><i class="ri-edit-2-line" aria-hidden="true"></i></span>
                    <h1 class="text-lg font-semibold tracking-tight text-slate-800 dark:text-neutral-100">{{ $t("Edit Coupon Code") }}</h1>
                </div>
                <button type="button" :aria-label="$t('Close')" class="rounded-lg p-2 text-slate-400 transition hover:bg-slate-100 hover:text-slate-700 dark:text-neutral-400 dark:hover:bg-neutral-700 dark:hover:text-white" @click="close">
                    <i class="ri-close-large-line text-xl"></i>
                </button>
            </div>
            <form @submit.prevent="submitForm" class="space-y-4">
                <div>
                    <InputLabel class="text-slate-700 dark:text-neutral-200" for="status" :value="$t('Status')" />
                    <SelectInput variant="admin" required v-model="form.status" id="status">
                        <option value="active" class="capitalize">{{ $t("active") }}</option>
                        <option value="inactive" class="capitalize">{{ $t("inactive") }}</option>
                    </SelectInput>
                    <InputError class="mt-2" :message="form.errors.status" />
                </div>
                <button :class="{ 'opacity-60 !cursor-not-allowed': form.processing }" :disabled="form.processing" type="submit" class="inline-flex w-full cursor-pointer items-center justify-center gap-2 rounded-xl border border-teal-700 bg-teal-700 px-4 py-3 font-semibold text-white transition hover:border-teal-800 hover:bg-teal-800">
                    <i class="ri-save-3-line" aria-hidden="true"></i>
                    {{ $t("form.save") }}
                </button>
            </form>
        </div>
    </Modal>
</template>
