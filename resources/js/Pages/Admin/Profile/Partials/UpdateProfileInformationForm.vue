<script setup>
import InputError from "@/js/Components/InputError.vue";
import InputLabel from "@/js/Components/InputLabel.vue";
import TextInput from "@/js/Components/TextInput.vue";
import { usePage, router } from "@inertiajs/vue3";
import { reactive, ref } from "vue";
import { trans } from "laravel-vue-i18n";
defineProps({
    status: {
        type: String,
    },
});
const processing = ref(false);
const recentlySuccessfulRequest = ref(false);
const user = usePage().props.auth.user;

const form = reactive({
    first_name: user.first_name,
    last_name: user.last_name,
    email: user.email,
    avatar: null,
});

const submit = () => {
    processing.value = true;
    router.post(
        route("profile.update"),
        {
            _method: "PATCH",
            first_name: form.first_name,
            last_name: form.last_name,
            email: form.email,
            avatar: form.avatar,
        },
        {
            onFinish: () => {
                const fileInput = document.getElementById("avatar");
                if (fileInput) {
                    fileInput.value = "";
                }
                processing.value = false;
            },
            onSuccess: () => {
                recentlySuccessfulRequest.value = true;
                setTimeout(() => {
                    recentlySuccessfulRequest.value = false;
                }, 1000);
            },
        },
    );
};
const handleFileChange = (e) => {
    form.avatar = e.target.files[0];
};
</script>

<template>
    <section>
        <header class="border-b border-slate-100 pb-4 dark:border-neutral-700">
            <h2 class="text-lg font-semibold text-slate-800 dark:text-neutral-100">
                {{ $t("profile.header") }}
            </h2>

            <p class="mt-1 text-sm text-slate-500 dark:text-neutral-400">
                {{ $t("profile.subtitle") }}
            </p>
        </header>

        <form @submit.prevent="submit" class="mt-6 space-y-6">
            <div>
                <InputLabel class="text-slate-700 dark:text-neutral-200" for="first_name" :value="$t('First Name')" />

                <TextInput
                    id="first_name"
                    type="text"
                    class="mt-1 block w-full rounded-xl border-slate-200 bg-slate-50/50 shadow-sm focus:border-primary focus:ring-primary/20 dark:border-neutral-600 dark:bg-neutral-700 dark:text-neutral-100 dark:focus:border-neutral-400 dark:focus:ring-neutral-500/30"
                    v-model="form.first_name"
                    required
                    autocomplete="first_name"
                />

                <InputError
                    class="mt-2"
                    :message="$page.props.errors.first_name"
                />
            </div>
            <div>
                <InputLabel class="text-slate-700 dark:text-neutral-200" for="last_name" :value="$t('Last Name')" />

                <TextInput
                    id="last_name"
                    type="text"
                    class="mt-1 block w-full rounded-xl border-slate-200 bg-slate-50/50 shadow-sm focus:border-primary focus:ring-primary/20 dark:border-neutral-600 dark:bg-neutral-700 dark:text-neutral-100 dark:focus:border-neutral-400 dark:focus:ring-neutral-500/30"
                    v-model="form.last_name"
                    required
                    autocomplete="last_name"
                />

                <InputError
                    class="mt-2"
                    :message="$page.props.errors.last_name"
                />
            </div>

            <div>
                <InputLabel class="text-slate-700 dark:text-neutral-200" for="email" :value="$t('Email')" />

                <TextInput
                    id="email"
                    type="email"
                    class="mt-1 block w-full rounded-xl border-slate-200 bg-slate-50/50 shadow-sm focus:border-primary focus:ring-primary/20 dark:border-neutral-600 dark:bg-neutral-700 dark:text-neutral-100 dark:focus:border-neutral-400 dark:focus:ring-neutral-500/30"
                    v-model="form.email"
                    required
                    autocomplete="email"
                />

                <InputError class="mt-2" :message="$page.props.errors.email" />
            </div>

            <div>
                <InputLabel class="text-slate-700 dark:text-neutral-200" for="avatar" :value="$t('Avatar')" />

                <input
                    @input="handleFileChange"
                    type="file"
                    id="avatar"
                    class="mt-1 block w-full cursor-pointer rounded-xl border border-slate-200 bg-slate-50/50 text-sm text-slate-500 file:mr-4 file:cursor-pointer file:rounded-lg file:border-0 file:bg-slate-100 file:px-4 file:py-3 file:font-medium file:text-slate-600 file:transition-colors hover:file:bg-slate-200 rtl:file:mr-0 rtl:file:ml-4 dark:border-neutral-600 dark:bg-neutral-700 dark:text-neutral-300 dark:file:bg-neutral-600 dark:file:text-neutral-100 dark:hover:file:bg-neutral-500"
                />
                <p class="mt-2 text-xs text-slate-500 dark:text-neutral-400">
                    {{ $t("avatar.description") }}
                </p>

                <InputError class="mt-2" :message="$page.props.errors.avatar" />
            </div>

            <div class="flex items-center gap-4">
                <input
                    :class="{
                        'opacity-25 cursor-not-allowed': processing,
                    }"
                    :disabled="processing"
                    type="Submit"
                    :value="$t('form.save')"
                    class="w-full cursor-pointer rounded-xl border border-primary bg-primary px-5 py-2.5 font-medium text-white shadow-sm transition duration-200 hover:-translate-y-0.5 hover:bg-opacity-90 hover:shadow-md sm:w-1/4"
                />

                <Transition
                    enter-active-class="transition ease-in-out"
                    enter-from-class="opacity-0"
                    leave-active-class="transition ease-in-out"
                    leave-to-class="opacity-0"
                >
                    <p
                        v-if="recentlySuccessfulRequest"
                        class="text-sm text-slate-500 dark:text-neutral-400"
                    >
                        {{ $t("form.saved") }}
                    </p>
                </Transition>
            </div>
        </form>
    </section>
</template>
