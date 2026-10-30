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
    <Head :title="$t('forgot.title')" />

    <DefaultAuthCard
        :title="$t('forgot.title')"
        :subtitle="$t('forgot.message')"
        icon="ri-mail-lock-line"
    >
        <div
            v-if="status"
            role="status"
            class="mb-5 flex items-start gap-3 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm leading-6 text-emerald-800"
        >
            <i
                class="ri-checkbox-circle-line mt-0.5 text-lg"
                aria-hidden="true"
            ></i>
            <span>{{ $t("forgot.sent") }}</span>
        </div>

        <form class="space-y-4" @submit.prevent="submit">
            <div>
                <InputGroup
                    id="email"
                    v-model="form.email"
                    :label="$t('Email')"
                    type="email"
                    :placeholder="$t('email.placeholder')"
                    autocomplete="username"
                    :required="true"
                >
                    <i class="ri-mail-line text-lg" aria-hidden="true"></i>
                </InputGroup>
                <InputError class="mt-1" :message="form.errors.email" />
            </div>

            <div class="pt-2">
                <button
                    type="submit"
                    :disabled="form.processing"
                    class="flex w-full items-center justify-center gap-2 rounded-xl border border-teal-700 bg-teal-700 px-4 py-3.5 font-semibold text-white shadow-sm transition hover:border-teal-800 hover:bg-teal-800 focus:outline-none focus:ring-4 focus:ring-teal-700/20 disabled:cursor-not-allowed disabled:opacity-60"
                >
                    <i class="ri-send-plane-line" aria-hidden="true"></i>
                    {{ $t("forgot.send") }}
                </button>
            </div>
        </form>

        <div class="mt-6 border-t border-slate-100 pt-5 text-center">
            <Link
                :href="route('login')"
                class="inline-flex items-center gap-2 text-sm font-medium text-slate-500 transition hover:text-teal-700 hover:underline"
            >
                <i class="ri-arrow-left-line mt-0" aria-hidden="true"></i>
                {{ $t("Login") }}
            </Link>
        </div>
    </DefaultAuthCard>
</template>
