<script setup>
import InputError from "@/js/Components/InputError.vue";
import InputLabel from "@/js/Components/InputLabel.vue";
import TextInput from "@/js/Components/TextInput.vue";
import { useForm } from "@inertiajs/vue3";
import { ref } from "vue";

const passwordInput = ref(null);
const currentPasswordInput = ref(null);

const form = useForm({
    current_password: "",
    password: "",
    password_confirmation: "",
});

const updatePassword = () => {
    form.put(route("password.update"), {
        preserveScroll: true,
        onSuccess: () => form.reset(),
        onError: () => {
            if (form.errors.password) {
                form.reset("password", "password_confirmation");
                passwordInput.value.focus();
            }
            if (form.errors.current_password) {
                form.reset("current_password");
                currentPasswordInput.value.focus();
            }
        },
    });
};
</script>

<template>
    <section>
        <header class="border-b border-slate-100 pb-4 dark:border-neutral-700">
            <h2 class="text-lg font-semibold text-slate-800 dark:text-neutral-100">
                {{ $t("password.header") }}
            </h2>

            <p class="mt-1 text-sm text-slate-500 dark:text-neutral-400">
                {{ $t("password.subtitle") }}
            </p>
        </header>

        <form @submit.prevent="updatePassword" class="mt-6 space-y-6">
            <div>
                <InputLabel
                    class="text-slate-700 dark:text-neutral-200"
                    for="current_password"
                    :value="$t('Current Password')"
                />

                <TextInput
                    variant="admin"
                    required
                    id="current_password"
                    ref="currentPasswordInput"
                    v-model="form.current_password"
                    type="password"
                    autocomplete="current-password"
                />

                <InputError
                    :message="form.errors.current_password"
                    class="mt-2"
                />
            </div>

            <div>
                <InputLabel class="text-slate-700 dark:text-neutral-200" for="password" :value="$t('New Password')" />

                <TextInput
                    variant="admin"
                    required
                    id="password"
                    ref="passwordInput"
                    v-model="form.password"
                    type="password"
                    autocomplete="new-password"
                />

                <InputError :message="form.errors.password" class="mt-2" />
            </div>

            <div>
                <InputLabel
                    class="text-slate-700 dark:text-neutral-200"
                    for="password_confirmation"
                    :value="$t('Confirm Password')"
                />

                <TextInput
                    variant="admin"
                    required
                    id="password_confirmation"
                    v-model="form.password_confirmation"
                    type="password"
                    autocomplete="new-password"
                />

                <InputError
                    :message="form.errors.password_confirmation"
                    class="mt-2"
                />
            </div>

            <div class="flex items-center gap-4">
                <input
                    :class="{
                        'opacity-25 cursor-not-allowed': form.processing,
                    }"
                    :disabled="form.processing"
                    type="Submit"
                    :value="$t('form.save')"
                    class="w-full cursor-pointer rounded-xl border border-teal-700 bg-teal-700 px-5 py-2.5 font-medium text-white shadow-sm transition duration-200 hover:-translate-y-0.5 hover:bg-opacity-90 hover:shadow-md sm:w-1/4"
                />

                <Transition
                    enter-active-class="transition ease-in-out"
                    enter-from-class="opacity-0"
                    leave-active-class="transition ease-in-out"
                    leave-to-class="opacity-0"
                >
                    <p
                        v-if="form.recentlySuccessful"
                        class="text-sm text-slate-500 dark:text-neutral-400"
                    >
                        {{ $t("form.saved") }}
                    </p>
                </Transition>
            </div>
        </form>
    </section>
</template>
