<script setup>
import InputError from "@/js/Components/InputError.vue";
import InputLabel from "@/js/Components/InputLabel.vue";
import TextInput from "@/js/Components/TextInput.vue";
import SelectInput from "@/js/Components/SelectInput.vue";
import { useForm } from "@inertiajs/vue3";
import { useToast } from "vue-toastification";
import { getToastOptions } from "@/js/Utils/toast";
import { ref, computed } from "vue";
import { trans } from "laravel-vue-i18n";
import { useLocationSelector } from "@/js/Composables/useLocationSelector";
import { useAppearanceStore } from "@/js/stores/appearance";
const props = defineProps(["states"]);
const toast = useToast();
const appearanceStore = useAppearanceStore();
const locationSelector = useLocationSelector();
const form = useForm({
    name: null,
    phone: null,
    phone2: null,
    address: null,
    state: null,
    city: null,
    locality: null,
});

const modalRef = ref(null);

function submitForm() {
    form.post(route("clients.store"), {
        onSuccess: () => {
            let message = trans("Client.created_successfully");
            toast.success(message, getToastOptions());
            modalRef.value.close();
            form.reset("name");
            form.reset("address");
            form.reset("phone");
            form.reset("state");
        },
    });
}
</script>

<template>
    <Modal
        v-slot="{ close }"
        ref="modalRef"
        max-width="2xl"
        padding-classes="p-0"
        :panel-classes="appearanceStore.isDark
            ? 'dark w-[calc(100vw-2rem)] md:w-[800px] !max-w-[calc(100vw-2rem)] max-h-70 md:max-h-115 overflow-y-auto rounded-2xl border border-neutral-700 bg-neutral-800 text-neutral-100 shadow-2xl lg:ltr:-translate-x-[9.0625rem] lg:rtl:translate-x-[9.0625rem]'
            : 'w-[calc(100vw-2rem)] md:w-[800px] !max-w-[calc(100vw-2rem)] max-h-70 md:max-h-115 overflow-y-auto rounded-2xl border border-slate-200 bg-white text-slate-800 shadow-2xl lg:ltr:-translate-x-[9.0625rem] lg:rtl:translate-x-[9.0625rem]'"
    >
        <div class="p-5 sm:p-6">
            <div class="mb-5 flex items-center justify-between gap-4 border-b border-slate-100 pb-4 dark:border-neutral-700">
                <div class="flex items-center gap-3">
                    <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-teal-50 text-lg text-teal-700 dark:bg-neutral-700 dark:text-teal-300"><i class="ri-user-add-line" aria-hidden="true"></i></span>
                    <h1 class="text-lg font-semibold tracking-tight text-slate-800 dark:text-neutral-100">{{ $t("New Client") }}</h1>
                </div>
                <button type="button" :aria-label="$t('Close')" class="rounded-lg p-2 text-slate-400 transition hover:bg-slate-100 hover:text-slate-700 dark:text-neutral-400 dark:hover:bg-neutral-700 dark:hover:text-white" @click="close">
                    <i class="ri-close-large-line text-xl"></i>
                </button>
            </div>
            <form @submit.prevent="submitForm" class="space-y-4">
                <div class="grid gap-4 md:grid-cols-3">
                    <div>
                        <InputLabel class="text-slate-700 dark:text-neutral-200" for="name" :value="$t('Name')" />
                        <TextInput variant="admin" id="name" type="text" v-model="form.name" :required="true" :placeholder="$t('ClientName.placeholder')" />
                        <InputError class="mt-2" :message="form.errors.name" />
                    </div>
                    <div>
                        <InputLabel class="text-slate-700 dark:text-neutral-200" for="phone-number" :value="$t('Phone Number')" />
                        <TextInput variant="admin" dir="ltr" id="phone-number" type="number" v-model="form.phone" :required="true" :placeholder="$t('8 digits phone number')" />
                        <InputError class="mt-2" :message="form.errors.phone" />
                    </div>
                    <div>
                        <InputLabel class="text-slate-700 dark:text-neutral-200" for="phone2-number" :value="$t('phone2')" />
                        <TextInput variant="admin" dir="ltr" id="phone2-number" type="number" v-model="form.phone2" :required="false" :placeholder="$t('8 digits phone number')" />
                        <InputError class="mt-2" :message="form.errors.phone2" />
                    </div>
                </div>

                <div class="grid gap-4 md:grid-cols-3">
                    <div>
                        <InputLabel class="text-slate-700 dark:text-neutral-200" for="state_id" :value="$t('State')" />
                        <SelectInput variant="admin" @change="locationSelector.getCities(form.state)" required v-model="form.state" id="state_id">
                            <option :value="null">{{ $t("Choose a state") }}</option>
                            <option :value="state.id" v-for="state in states" :key="state.id">{{ state.name }}</option>
                        </SelectInput>
                        <InputError class="mt-2" :message="form.errors.state" />
                    </div>
                    <div>
                        <InputLabel class="text-slate-700 dark:text-neutral-200" for="city_id" :value="$t('City')" />
                        <SelectInput variant="admin" @change="locationSelector.getLocalities(form.city)" required v-model="form.city" id="city_id">
                            <option :value="null">{{ $t("Choose a city") }}</option>
                            <option :value="city.id" v-for="city in locationSelector.cities.value" :key="city.id">{{ city.name }}</option>
                        </SelectInput>
                        <InputError class="mt-2" :message="form.errors.city" />
                    </div>
                    <div>
                        <InputLabel class="text-slate-700 dark:text-neutral-200" for="locality_id" :value="$t('Locality')" />
                        <SelectInput variant="admin" required v-model="form.locality" id="locality_id">
                            <option :value="null">{{ $t("Choose a locality") }}</option>
                            <option :value="locality.id" v-for="locality in locationSelector.localities.value" :key="locality.id">{{ locality.name }}</option>
                        </SelectInput>
                        <InputError class="mt-2" :message="form.errors.locality" />
                    </div>
                </div>

                <div>
                    <InputLabel class="text-slate-700 dark:text-neutral-200" for="address" :value="$t('Address')" />
                    <TextInput variant="admin" id="address" type="text" v-model="form.address" :required="true" :placeholder="$t('Address.placeholder')" />
                    <InputError class="mt-2" :message="form.errors.address" />
                </div>

                <button :class="{ 'opacity-60 !cursor-not-allowed': form.processing }" :disabled="form.processing" type="submit" class="inline-flex w-full cursor-pointer items-center justify-center gap-2 rounded-xl border border-teal-700 bg-teal-700 px-4 py-3 font-semibold text-white transition hover:border-teal-800 hover:bg-teal-800">
                    <i class="ri-user-add-line" aria-hidden="true"></i>
                    {{ $t("Client.create") }}
                </button>
            </form>
        </div>
    </Modal>
</template>
