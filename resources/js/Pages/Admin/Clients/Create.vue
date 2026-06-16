<script setup>
import InputError from "@/js/Components/InputError.vue";
import InputLabel from "@/js/Components/InputLabel.vue";
import TextInput from "@/js/Components/TextInput.vue";
import { useForm } from "@inertiajs/vue3";
import { useToast } from "vue-toastification";
import { getToastOptions } from "@/js/Utils/toast";
import { ref, computed } from "vue";
import { trans } from "laravel-vue-i18n";
import { useLocationSelector } from "@/js/Composables/useLocationSelector";
const props = defineProps(["states"]);
const toast = useToast();
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
        panel-classes="bg-white  rounded overflow-y-auto max-h-70 md:max-h-100 mt-auto md:w-[800px]"
    >
        <div class="flex justify-between items-center">
            <h1 class="text-xl text-primary font-bold">
                {{ $t("New Client") }}
            </h1>
            <button type="button" class="text-red-500 font-bold" @click="close">
                <i class="ri-close-large-line text-xl"></i>
            </button>
        </div>
        <form @submit.prevent="submitForm">
            <div class="md:flex md:flex-row gap-4">
                <div class="mt-4 w-full">
                    <InputLabel for="name" :value="$t('Name')" />

                    <TextInput
                        class="mt-2 block w-full"
                        id="name"
                        type="text"
                        v-model="form.name"
                        :required="true"
                        :placeholder="$t('ClientName.placeholder')"
                    />

                    <InputError class="mt-2" :message="form.errors.name" />
                </div>

                <div class="mt-4 w-full">
                    <InputLabel
                        for="phone-number"
                        :value="$t('Phone Number')"
                    />

                    <TextInput
                        dir="ltr"
                        class="mt-2 block w-full placeholder:rtl:text-end"
                        id="phone-number"
                        type="number"
                        v-model="form.phone"
                        :required="true"
                        :placeholder="$t('8 digits phone number')"
                    />

                    <InputError class="mt-2" :message="form.errors.phone" />
                </div>
                <div class="mt-4 w-full">
                    <InputLabel for="phone-number" :value="$t('phone2')" />

                    <TextInput
                        dir="ltr"
                        class="mt-2 block w-full placeholder:rtl:text-end"
                        id="phone2-number"
                        type="number"
                        v-model="form.phone2"
                        :required="false"
                        :placeholder="$t('8 digits phone number')"
                    />

                    <InputError class="mt-2" :message="form.errors.phone2" />
                </div>
            </div>
            <div class="md:flex md:flex-row gap-4">
                <div class="mt-4 w-full">
                    <InputLabel for="state_id" :value="$t('State')" />

                    <select
                        @change="locationSelector.getCities(form.state)"
                        required
                        v-model="form.state"
                        id="state_id"
                        class="w-full mt-1 rounded-md focus:ring-primary focus:border-primary border-editor text-primary"
                    >
                        <option :value="null" class="capitalize">
                            {{ $t("Choose a state") }}
                        </option>

                        <option
                            :value="state.id"
                            v-for="state in states"
                            :key="state.id"
                        >
                            {{ state.name }}
                        </option>
                    </select>

                    <InputError class="mt-2" :message="form.errors.state" />
                </div>
                <div class="mt-4 w-full">
                    <InputLabel for="city_id" :value="$t('City')" />

                    <select
                        @change="locationSelector.getLocalities(form.city)"
                        required
                        v-model="form.city"
                        id="city_id"
                        class="w-full mt-1 rounded-md focus:ring-primary focus:border-primary border-editor text-primary"
                    >
                        <option :value="null" class="capitalize">
                            {{ $t("Choose a city") }}
                        </option>

                        <option
                            :value="city.id"
                            v-for="city in locationSelector.cities.value"
                            :key="city.id"
                        >
                            {{ city.name }}
                        </option>
                    </select>

                    <InputError class="mt-2" :message="form.errors.city" />
                </div>
                <div class="mt-4 w-full">
                    <InputLabel for="locality_id" :value="$t('Locality')" />

                    <select
                        required
                        v-model="form.locality"
                        id="locality_id"
                        class="w-full mt-1 rounded-md focus:ring-primary focus:border-primary border-editor text-primary"
                    >
                        <option :value="null" class="capitalize">
                            {{ $t("Choose a locality") }}
                        </option>

                        <option
                            :value="locality.id"
                            v-for="locality in locationSelector.localities
                                .value"
                            :key="locality.id"
                        >
                            {{ locality.name }}
                        </option>
                    </select>

                    <InputError class="mt-2" :message="form.errors.locality" />
                </div>
            </div>
            <div class="md:flex md:flex-row gap-4">
                <div class="mt-4 w-full">
                    <InputLabel for="address" :value="$t('Address')" />

                    <TextInput
                        class="mt-2 block w-full"
                        id="address"
                        type="text"
                        v-model="form.address"
                        :required="true"
                        :placeholder="$t('Address.placeholder')"
                    />

                    <InputError class="mt-2" :message="form.errors.address" />
                </div>
            </div>
            <button
                :class="{
                    'opacity-25 cursor-not-allowed': form.processing,
                }"
                :disabled="form.processing"
                type="Submit"
                class="w-full cursor-pointer rounded-lg border border-primary bg-primary px-2 py-3 font-medium text-white transition hover:bg-opacity-90 mt-4"
            >
                {{ $t("Client.create") }}
            </button>
        </form>
    </Modal>
</template>
