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
        <header>
            <h2 class="text-lg font-medium text-gray-900">
                {{ $t("profile.header") }}
            </h2>

            <p class="mt-1 text-sm text-gray-600">
                {{ $t("profile.subtitle") }}
            </p>
        </header>

        <form @submit.prevent="submit" class="mt-6 space-y-6">
            <div>
                <InputLabel for="first_name" :value="$t('First Name')" />

                <TextInput
                    id="first_name"
                    type="text"
                    class="mt-1 block w-full"
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
                <InputLabel for="last_name" :value="$t('Last Name')" />

                <TextInput
                    id="last_name"
                    type="text"
                    class="mt-1 block w-full"
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
                <InputLabel for="email" :value="$t('Email')" />

                <TextInput
                    id="email"
                    type="email"
                    class="mt-1 block w-full"
                    v-model="form.email"
                    required
                    autocomplete="email"
                />

                <InputError class="mt-2" :message="$page.props.errors.email" />
            </div>

            <div>
                <InputLabel for="avatar" :value="$t('Avatar')" />

                <input
                    @input="handleFileChange"
                    type="file"
                    id="avatar"
                    class="w-full mt-1 block text-gray-400 text-sm bg-white border file:cursor-pointer cursor-pointer file:border-0 file:py-3 file:px-4 ltr:file:mr-4 rtl:file:ml-4 file:bg-gray-100 file:hover:bg-gray-200 file:text-gray-500 rounded"
                />
                <p class="text-xs text-gray-400 mt-2">
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
                    class="w-full sm:w-1/4 cursor-pointer rounded-lg border border-primary bg-primary py-2 px-4 font-medium text-white transition hover:bg-opacity-90"
                />

                <Transition
                    enter-active-class="transition ease-in-out"
                    enter-from-class="opacity-0"
                    leave-active-class="transition ease-in-out"
                    leave-to-class="opacity-0"
                >
                    <p
                        v-if="recentlySuccessfulRequest"
                        class="text-sm text-gray-600"
                    >
                        {{ $t("form.saved") }}
                    </p>
                </Transition>
            </div>
        </form>
    </section>
</template>
