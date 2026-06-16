<script setup>
import InputError from "@/js/Components/InputError.vue";
import InputLabel from "@/js/Components/InputLabel.vue";
import TextInput from "@/js/Components/TextInput.vue";
import { useForm } from "@inertiajs/vue3";
import { useToast } from "vue-toastification";
import { getToastOptions } from "@/js/Utils/toast";
import { ref } from "vue";
import { trans } from "laravel-vue-i18n";
const props = defineProps({
    id: {
        required: true,
    },
    type: {
        required: true,
    },
});
const toast = useToast();

const form = useForm({
    content: null,
    id: props.id,
    type: props.type,
});

const modalRef = ref(null);

function submitForm() {
    form.post(route("notes.store"), {
        onSuccess: () => {
            let message = trans("Note.created_successfully");
            toast.success(message, getToastOptions());
            modalRef.value.close();
            form.reset("content");
            form.reset("id");
            form.reset("type");
        },
    });
}
</script>

<template>
    <Modal
        v-slot="{ close }"
        ref="modalRef"
        panel-classes="bg-white  rounded overflow-y-auto max-h-70 md:max-h-100 mt-auto"
        :close-button="false"
    >
        <div class="flex justify-between items-center">
            <h1 class="text-xl text-primary font-bold">
                {{ $t("New Note") }}
            </h1>
            <button type="button" class="text-red-500 font-bold" @click="close">
                <i class="ri-close-large-line text-xl"></i>
            </button>
        </div>
        <form @submit.prevent="submitForm">
            <div class="md:flex md:flex-row gap-4">
                <div class="mt-4 w-full">
                    <InputLabel for="content" :value="$t('Note')" />

                    <textarea
                        class="mt-2 block w-full max-h-30 h-30 resize-none"
                        id="content"
                        type="text"
                        v-model="form.content"
                        :required="true"
                        :placeholder="$t('NewNote.placeholder')"
                    ></textarea>
                    <InputError class="mt-2" :message="form.errors.content" />
                </div>
            </div>

            <button
                :class="{
                    'opacity-25 cursor-not-allowed': form.processing,
                }"
                :disabled="form.processing"
                type="Submit"
                class="w-full cursor-pointer rounded-lg border border-primary bg-primary px-2 py-3 font-medium text-white transition hover:bg-opacity-90 mt-4"
            >
                {{ $t("Note.create") }}
            </button>
        </form>
    </Modal>
</template>
