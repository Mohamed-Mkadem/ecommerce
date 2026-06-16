<script setup>
import GuestLayout from "@/js/Layouts/GuestLayout.vue";
import InputError from "@/js/Components/InputError.vue";
import InputLabel from "@/js/Components/InputLabel.vue";
import TextInput from "@/js/Components/TextInput.vue";
import { Head, useForm } from "@inertiajs/vue3";
import { useLanguageStore } from "@/js/stores/Language";
import { onMounted } from "vue";
const languageStore = useLanguageStore();

onMounted(() => {
    if (languageStore.direction == "rtl") {
        document.documentElement.setAttribute("dir", "rtl");
    } else {
        document.documentElement.setAttribute("dir", "ltr");
    }
});
defineProps({
    status: {
        type: String,
    },
});

const form = useForm({
    email: "",
});

const submit = () => {
    form.post(route("password.email"));
};
</script>

<template>
    <GuestLayout>
        <Head :title="$t('forgot.title')" />

        <div class="mb-4 text-sm text-gray-600">
            {{ $t("forgot.message") }}
        </div>

        <div v-if="status" class="mb-4 text-sm font-medium text-green-600">
            {{ $t("forgot.sent") }}
        </div>

        <form @submit.prevent="submit">
            <div>
                <InputLabel for="email" :value="$t('Email')" />

                <TextInput
                    id="email"
                    type="email"
                    class="mt-1 block w-full"
                    v-model="form.email"
                    autofocus
                    :required="true"
                    autocomplete="username"
                />

                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <div class="mt-4 flex items-center justify-end">
                <input
                    :disabled="form.processing"
                    :class="{ 'opacity-25': form.processing }"
                    type="submit"
                    :value="$t('forgot.send')"
                    class="w-full cursor-pointer rounded-lg border border-primary bg-primary p-4 font-medium text-white transition hover:bg-opacity-90"
                />
            </div>
        </form>
    </GuestLayout>
</template>
