<script setup>
import DefaultAuthCard from "@/js/Components/Auths/DefaultAuthCard.vue";
import InputError from "@/js/Components/InputError.vue";
import InputGroup from "@/js/Components/Auths/InputGroup.vue";
import { Head, useForm } from "@inertiajs/vue3";
import { useLanguageStore } from "@/js/stores/Language";
import { onMounted } from "vue";

const languageStore = useLanguageStore();

onMounted(() => {
    document.documentElement.setAttribute(
        "dir",
        languageStore.direction === "rtl" ? "rtl" : "ltr",
    );
});

const props = defineProps({
    email: {
        type: String,
        required: true,
    },
    token: {
        type: String,
        required: true,
    },
});

const form = useForm({
    token: props.token,
    email: props.email,
    password: "",
    password_confirmation: "",
});

const submit = () => {
    form.post(route("password.store"), {
        onFinish: () => form.reset("password", "password_confirmation"),
    });
};
</script>

<template>
    <Head :title="$t('reset.title')" />

    <DefaultAuthCard :title="$t('reset.title')" icon="ri-lock-password-line">
        <form class="space-y-4" @submit.prevent="submit">
            <div>
                <InputGroup
                    id="email"
                    v-model="form.email"
                    :label="$t('Email')"
                    type="email"
                    autocomplete="username"
                    :required="true"
                >
                    <i class="ri-mail-line text-lg" aria-hidden="true"></i>
                </InputGroup>
                <InputError class="mt-1" :message="form.errors.email" />
            </div>

            <div>
                <InputGroup
                    id="password"
                    v-model="form.password"
                    :label="$t('Password')"
                    type="password"
                    autocomplete="new-password"
                    :required="true"
                >
                    <i class="ri-lock-line text-lg" aria-hidden="true"></i>
                </InputGroup>
                <InputError class="mt-1" :message="form.errors.password" />
            </div>

            <div>
                <InputGroup
                    id="password_confirmation"
                    v-model="form.password_confirmation"
                    :label="$t('Confirm Password')"
                    type="password"
                    autocomplete="new-password"
                    :required="true"
                >
                    <i class="ri-lock-password-line text-lg" aria-hidden="true"></i>
                </InputGroup>
                <InputError
                    class="mt-1"
                    :message="form.errors.password_confirmation"
                />
            </div>

            <InputError class="pt-1" :message="form.errors.token" />

            <div class="pt-2">
                <button
                    type="submit"
                    :disabled="form.processing"
                    class="flex w-full items-center justify-center gap-2 rounded-xl border border-teal-700 bg-teal-700 px-4 py-3.5 font-semibold text-white shadow-sm transition hover:border-teal-800 hover:bg-teal-800 focus:outline-none focus:ring-4 focus:ring-teal-700/20 disabled:cursor-not-allowed disabled:opacity-60"
                >
                    <i class="ri-lock-password-line" aria-hidden="true"></i>
                    {{ $t("Reset Password") }}
                </button>
            </div>
        </form>

        <div class="mt-6 border-t border-slate-100 pt-5 text-center">
            <Link
                :href="route('login')"
                class="inline-flex items-center gap-2 text-sm font-medium text-slate-500 transition hover:text-teal-700 hover:underline"
            >
                <i class="ri-arrow-left-line" aria-hidden="true"></i>
                {{ $t("Login") }}
            </Link>
        </div>
    </DefaultAuthCard>
</template>
