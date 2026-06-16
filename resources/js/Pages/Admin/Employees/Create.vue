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
        panel-classes="bg-white rounded mx-auto md:max-w-lg max-h-100  overflow-y-auto"
    >
        <div class="flex justify-between items-center">
            <h1 class="text-xl text-primary font-bold">
                {{ $t("Employee.new") }}
            </h1>
            <button type="button" class="text-red-500 font-bold" @click="close">
                <i class="ri-close-large-line text-xl"></i>
            </button>
        </div>
        <form @submit.prevent="submitForm">
            <div class="mt-4">
                <InputLabel for="first_name" :value="$t('first_name')" />

                <TextInput
                    class="mt-2 block w-full"
                    id="first_name"
                    type="text"
                    v-model="form['first_name']"
                    :required="true"
                    :placeholder="$t('first_name')"
                />

                <InputError class="mt-2" :message="form.errors['first_name']" />
            </div>
            <div class="mt-4">
                <InputLabel for="last_name" :value="$t('last_name')" />

                <TextInput
                    class="mt-2 block w-full"
                    id="last_name"
                    type="text"
                    v-model="form['last_name']"
                    :required="true"
                    :placeholder="$t('last_name')"
                />

                <InputError class="mt-2" :message="form.errors['last_name']" />
            </div>
            <div class="mt-4">
                <InputLabel
                    class="text-slate-600"
                    for="employee-role"
                    :value="$t('role')"
                />

                <select
                    required
                    id="employee-role"
                    v-model="form.role"
                    class="w-full mt-2 rounded-md focus:ring-primary focus:border-primary border-editor text-primary"
                >
                    <option value="moderator">{{ $t("moderator") }}</option>
                    <option value="admin">{{ $t("admin") }}</option>
                </select>

                <InputError class="mt-2" :message="form.errors['role']" />
            </div>

            <div class="mt-4">
                <InputLabel for="email" :value="$t('Email')" />

                <TextInput
                    class="mt-2 block w-full"
                    id="email"
                    type="email"
                    v-model="form['email']"
                    :required="true"
                    :placeholder="$t('Email')"
                />

                <InputError class="mt-2" :message="form.errors['email']" />
            </div>
            <div class="mt-4">
                <InputLabel for="password" :value="$t('Password')" />

                <TextInput
                    class="mt-2 block w-full"
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
                class="w-full cursor-pointer rounded-lg border border-primary bg-primary px-2 py-3 font-medium text-white transition hover:bg-opacity-90 mt-4"
            >
                {{ $t("Employee.create") }}
            </button>
        </form>
    </Modal>
</template>
