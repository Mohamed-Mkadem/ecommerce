<script setup>
import InputError from "@/js/Components/InputError.vue";
import InputLabel from "@/js/Components/InputLabel.vue";
import { useForm } from "@inertiajs/vue3";
import { useToast } from "vue-toastification";
import { getToastOptions } from "@/js/Utils/toast";
import { ref } from "vue";
import { trans } from "laravel-vue-i18n";
const props = defineProps({
    employee: { type: Object },
});
const toast = useToast();

const form = useForm({
    role: props.employee.role,
    status: props.employee.status,
});

const modalRef = ref(null);

function submitForm() {
    form.patch(route("employees.update", props.employee), {
        onSuccess: () => {
            let message = trans("Employee.updated_successfully");
            toast.success(message, getToastOptions());
            modalRef.value.close();
            form.reset("role");
            form.reset("status");
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
                {{ $t("Employee.update") }}
            </h1>
            <button type="button" class="text-red-500 font-bold" @click="close">
                <i class="ri-close-large-line text-xl"></i>
            </button>
        </div>
        <form @submit.prevent="submitForm">
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
                <InputLabel
                    class="text-slate-600"
                    for="employee-status"
                    :value="$t('Status')"
                />

                <select
                    required
                    id="employee-status"
                    v-model="form.status"
                    class="w-full mt-2 rounded-md focus:ring-primary focus:border-primary border-editor text-primary"
                >
                    <option value="active">{{ $t("user.active") }}</option>
                    <option value="banned">{{ $t("user.banned") }}</option>
                </select>

                <InputError class="mt-2" :message="form.errors['status']" />
            </div>

            <button
                :class="{
                    'opacity-25 cursor-not-allowed': form.processing,
                }"
                :disabled="form.processing"
                type="Submit"
                class="w-full cursor-pointer rounded-lg border border-primary bg-primary px-2 py-3 font-medium text-white transition hover:bg-opacity-90 mt-4"
            >
                {{ $t("form.save") }}
            </button>
        </form>
    </Modal>
</template>
