<script setup>
import InputError from "@/js/Components/InputError.vue";
import InputLabel from "@/js/Components/InputLabel.vue";
import TextInput from "@/js/Components/TextInput.vue";
import SelectInput from "@/js/Components/SelectInput.vue";
import { useForm } from "@inertiajs/vue3";
import { useToast } from "vue-toastification";
import { getToastOptions } from "@/js/Utils/toast";
import { ref } from "vue";
import { trans } from "laravel-vue-i18n";
import { useAppearanceStore } from "@/js/stores/appearance";

const toast = useToast();
const appearanceStore = useAppearanceStore();

const form = useForm({
    first_name: null,
    last_name: null,
    role: "moderator",
    email: null,
    password: null,
});

const modalRef = ref(null);

function submitForm() {
    form.post(route("employees.store"), {
        onSuccess: () => {
            let message = trans("Employee.created_successfully");
            toast.success(message, getToastOptions());
            modalRef.value.close();
            form.reset("role");
            form.reset("email");
            form.reset("first_name");
            form.reset("last_name");
            form.reset("password");
        },
    });
}
</script>

<template>
    <Modal
        v-slot="{ close }"
        ref="modalRef"
        max-width="lg"
        :panel-classes="appearanceStore.isDark
            ? 'dark max-h-[calc(100vh-2rem)] overflow-y-auto rounded-2xl border border-neutral-700 bg-neutral-800 text-neutral-100 shadow-2xl'
            : 'max-h-[calc(100vh-2rem)] overflow-y-auto rounded-2xl border border-slate-200 bg-white text-slate-800 shadow-2xl'"
    >
        <div class="mb-5 flex items-center justify-between gap-4 border-b border-slate-200 pb-4 dark:border-neutral-700">
            <div class="flex items-center gap-3">
                <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-primary/10 text-xl text-primary dark:bg-neutral-700 dark:text-neutral-100">
                    <i class="ri-user-add-line" aria-hidden="true"></i>
                </span>
                <h1 class="text-lg font-semibold text-slate-800 dark:text-neutral-100">
                    {{ $t("Employee.new") }}
                </h1>
            </div>
            <button type="button" :aria-label="$t('Close')" class="rounded-lg p-2 text-slate-400 transition hover:bg-slate-100 hover:text-slate-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-primary/40 dark:text-neutral-400 dark:hover:bg-neutral-700 dark:hover:text-white" @click="close">
                <i class="ri-close-large-line text-xl"></i>
            </button>
        </div>
        <form @submit.prevent="submitForm" class="space-y-4">
            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <InputLabel class="text-slate-700 dark:text-neutral-200" for="first_name" :value="$t('first_name')" />

                    <TextInput
                        variant="admin"
                        id="first_name"
                        type="text"
                        v-model="form['first_name']"
                        :required="true"
                        :placeholder="$t('first_name')"
                    />

                    <InputError class="mt-2" :message="form.errors['first_name']" />
                </div>
                <div>
                    <InputLabel class="text-slate-700 dark:text-neutral-200" for="last_name" :value="$t('last_name')" />

                    <TextInput
                        variant="admin"
                        id="last_name"
                        type="text"
                        v-model="form['last_name']"
                        :required="true"
                        :placeholder="$t('last_name')"
                    />

                    <InputError class="mt-2" :message="form.errors['last_name']" />
                </div>
            </div>
            <div class="mt-4">
                <InputLabel
                    class="text-slate-700 dark:text-neutral-200"
                    for="employee-role"
                    :value="$t('role')"
                />

                <SelectInput
                    variant="admin"
                    required
                    id="employee-role"
                    v-model="form.role"
                >
                    <option value="moderator">{{ $t("moderator") }}</option>
                    <option value="admin">{{ $t("admin") }}</option>
                </SelectInput>

                <InputError class="mt-2" :message="form.errors['role']" />
            </div>

            <div class="mt-4">
                <InputLabel class="text-slate-700 dark:text-neutral-200" for="email" :value="$t('Email')" />

                <TextInput
                    variant="admin"
                    id="email"
                    type="email"
                    v-model="form['email']"
                    :required="true"
                    :placeholder="$t('Email')"
                />

                <InputError class="mt-2" :message="form.errors['email']" />
            </div>
            <div class="mt-4">
                <InputLabel class="text-slate-700 dark:text-neutral-200" for="password" :value="$t('Password')" />

                <TextInput
                    variant="admin"
                    id="password"
                    type="text"
                    v-model="form['password']"
                    :required="true"
                    :placeholder="$t('Password')"
                />

                <InputError class="mt-2" :message="form.errors['password']" />
            </div>

            <button
                :class="{
                    'opacity-25 cursor-not-allowed': form.processing,
                }"
                :disabled="form.processing"
                type="Submit"
                class="mt-2 w-full cursor-pointer rounded-xl border border-teal-700 bg-teal-700 px-4 py-3 font-semibold text-white shadow-sm transition hover:-translate-y-0.5 hover:bg-teal-800 hover:shadow-md focus:outline-none focus-visible:ring-2 focus-visible:ring-teal-500/40 disabled:cursor-not-allowed disabled:opacity-50"
            >
                {{ $t("Employee.create") }}
            </button>
        </form>
    </Modal>
</template>
