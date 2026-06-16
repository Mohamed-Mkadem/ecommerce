<script setup>
import InputError from "@/js/Components/InputError.vue";
import InputLabel from "@/js/Components/InputLabel.vue";
import TextInput from "@/js/Components/TextInput.vue";
import { useForm } from "@inertiajs/vue3";
import { useToast } from "vue-toastification";
import { getToastOptions } from "@/js/Utils/toast";
import { ref } from "vue";
import { trans } from "laravel-vue-i18n";

const toast = useToast();

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
    <Modal v-slot="{ close }" max-width="md" ref="modalRef">
        <div class="flex justify-between items-center">
            <h1 class="text-xl text-primary font-bold">
                {{ $t("Shipper.new") }}
            </h1>
            <button type="button" class="text-red-500 font-bold" @click="close">
                <i class="ri-close-large-line text-xl"></i>
            </button>
        </div>
        <form @submit.prevent="submitForm">
            <div class="mt-4">
                <InputLabel for="name" :value="$t('Name')" />

                <TextInput
                    class="mt-2 block w-full"
                    id="name"
                    type="text"
                    v-model="form['name']"
                    :required="true"
                    :placeholder="$t('Shipper.placeholder')"
                />

                <InputError class="mt-2" :message="form.errors['name']" />
            </div>
            <button
                :class="{
                    'opacity-25 cursor-not-allowed': form.processing,
                }"
                :disabled="form.processing"
                type="Submit"
                class="w-full cursor-pointer rounded-lg border border-primary bg-primary px-2 py-3 font-medium text-white transition hover:bg-opacity-90 mt-4"
            >
                {{ $t("Shipper.create") }}
            </button>
        </form>
    </Modal>
</template>
