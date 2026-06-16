<script setup>
import InputError from "@/js/Components/InputError.vue";
import InputLabel from "@/js/Components/InputLabel.vue";
import TextInput from "@/js/Components/TextInput.vue";
import { useForm } from "@inertiajs/vue3";
import { useToast } from "vue-toastification";
import { getToastOptions } from "@/js/Utils/toast";
import { ref } from "vue";
import { trans } from "laravel-vue-i18n";
const props = defineProps(["settings"]);
const toast = useToast();
const form = useForm({
    status: props.settings.is_visible,
    text: props.settings.text_content,
});

const modalRef = ref(null);
function updateSettings() {
    form.put(route("settings.update", props.settings), {
        onSuccess: () => {
            let message = trans("Banner.updated_successfully");
            toast.success(message, getToastOptions());
            modalRef.value.close();
        },
    });
}
</script>

<template>
    <Modal ref="modalRef" v-slot="{ close }">
        <div class="flex justify-between items-center">
            <h1 class="text-xl text-primary font-bold">
                {{ $t("Banner.edit") }}
            </h1>
            <button type="button" class="text-red-500 font-bold" @click="close">
                <i class="ri-close-large-line text-xl"></i>
            </button>
        </div>
        <form @submit.prevent="updateSettings">
            <div class="mt-4 w-full">
                <InputLabel
                    for="banner-status"
                    :value="trans('Banner.status')"
                />

                <select
                    class="w-full mt-1 rounded-md focus:ring-primary focus:border-primary border-editor text-primary"
                    v-model="form.status"
                    id="banner-status"
                    required
                >
                    <option value="1" :selected="form.status == 1">
                        {{ $t("Banner.visible") }}
                    </option>
                    <option value="0" :selected="form.status == 0">
                        {{ trans("Banner.hidden") }}
                    </option>
                </select>

                <InputError class="mt-2" :message="form.errors.status" />
            </div>
            <div class="mt-4">
                <InputLabel for="text" :value="trans('Banner.text')" />

                <TextInput
                    class="mt-1 block w-full"
                    id="text"
                    type="text"
                    v-model="form.text"
                    :required="true"
                />

                <InputError class="mt-2" :message="form.errors.text" />
            </div>
            <input
                :class="{
                    'opacity-25 cursor-not-allowed': form.processing,
                }"
                :disabled="form.processing"
                type="Submit"
                :value="trans('form.save')"
                class="w-full sm:w-1/4 cursor-pointer rounded-lg border border-primary bg-primary py-2 px-4 font-medium text-white transition hover:bg-opacity-90 mt-4"
            />
        </form>
    </Modal>
</template>
