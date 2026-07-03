<script setup>
import { trans } from "laravel-vue-i18n";
import { useForm } from "@inertiajs/vue3";
import InputError from "@/js/Components/InputError.vue";
import InputLabel from "@/js/Components/InputLabel.vue";
import TextInput from "@/js/Components/TextInput.vue";
import DatePicker from "@/js/Components/DatePicker.vue";
import { ref, onMounted, watch } from "vue";
import { useToast } from "vue-toastification";
import { getToastOptions } from "@/js/Utils/toast";
import { useLocationSelector } from "@/js/Composables/useLocationSelector";
const locationSelector = useLocationSelector();
const toast = useToast();
const props = defineProps({
    order: {
        type: Object,
        required: true,
    },
    shippers: {
        type: Array,
        required: true,
    },
    states: {
        type: Array,
        required: true,
    },
});
const modalRef = ref(null);
onMounted(async () => {
    if (props.order.data.locality) {
        await locationSelector.getCities(props.order.data.state_id);
        await locationSelector.getLocalities(props.order.data.city.id);
    } else {
        await locationSelector.getCities(props.order.data.state_id);
    }
});
const form = useForm({
    shipper: props.order.data.shipper ? props.order.data.shipper.id : null,
    deliveryDate: props.order.data.delivery_date,
    name: props.order.data.client_name,
    address: props.order.data.address,
    phone: props.order.data.phone,
    phone2: props.order.data.phone2,
    state: props.order.data.state_id,
    city: props.order.data.city ? props.order.data.city.id : null,
    locality: props.order.data.locality ? props.order.data.locality.id : null,
    free_shipping: props.order.data.free_shipping,
});

function submitForm() {
    form.patch(route("orders.update", props.order.data), {
        onSuccess: () => {
            let message = trans("Order.updated_successfully");
            toast.success(message, getToastOptions());
            modalRef.value.close();
        },
    });
}

watch(
    () => form.state,
    (newState) => {
        if (!newState || !newState.default_shipper_id) form.shipper = null;

        const state = props.states.find((item) => item.id == newState);
        console.log(state);
        if (state && state.default_shipper_id) {
            form.shipper = state.default_shipper_id;
        }
    },
);
</script>

<template>
    <Modal
        v-slot="{ close }"
        ref="modalRef"
        max-width="md"
        panel-classes="bg-white  rounded overflow-y-auto max-h-70 md:max-h-[550px] mt-5"
    >
        <div class="flex justify-between items-center">
            <h1 class="text-xl text-primary font-bold">
                {{ $t("Order.edit") }}
            </h1>
            <button type="button" class="text-red-500 font-bold" @click="close">
                <i class="ri-close-large-line text-xl"></i>
            </button>
        </div>

        <form @submit.prevent="submitForm">
            <div class="mt-4">
                <InputLabel
                    for="full-name"
                    :value="$t('Full Name')"
                    class="text-neutral-500"
                />

                <TextInput
                    class="mt-1 block w-full"
                    id="full-name"
                    type="text"
                    v-model="form.name"
                    :required="true"
                    :placeholder="$t('Full Name')"
                />

                <InputError class="mt-2" :message="form.errors['name']" />
            </div>
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
                        v-for="locality in locationSelector.localities.value"
                        :key="locality.id"
                    >
                        {{ locality.name }}
                    </option>
                </select>

                <InputError class="mt-2" :message="form.errors.locality" />
            </div>

            <div class="mt-4">
                <InputLabel
                    for="address"
                    :value="$t('Address')"
                    class="text-neutral-500"
                />

                <TextInput
                    class="mt-1 block w-full"
                    id="address"
                    type="text"
                    v-model="form.address"
                    :required="true"
                    :placeholder="$t('Full Shipping Address')"
                />

                <InputError class="mt-2" :message="form.errors['address']" />
            </div>

            <div class="mt-4">
                <InputLabel
                    for="phone"
                    :value="$t('Phone Number')"
                    class="text-neutral-500"
                />

                <TextInput
                    class="mt-1 block w-full"
                    id="phone"
                    v-model="form.phone"
                    :required="false"
                    type="number"
                    :placeholder="$t('8 digits phone number')"
                />
                <InputError class="mt-2" :message="form.errors['phone']" />
            </div>
            <div class="mt-4">
                <InputLabel
                    for="phone"
                    :value="$t('phone2')"
                    class="text-neutral-500"
                />

                <TextInput
                    class="mt-1 block w-full"
                    id="phone"
                    v-model="form.phone2"
                    :required="false"
                    type="number"
                    :placeholder="$t('8 digits phone number')"
                />
                <InputError class="mt-2" :message="form.errors['phone2']" />
            </div>

            <div class="mt-4">
                <label for="delivery-date" class="text-neutral-500 font-medium">
                    {{ $t("Delivery_date") }}
                    <span class="ltr"> : {{ form.deliveryDate }}</span>
                </label>
                <DatePicker
                    class="mt-1 block w-full h-[42px]"
                    id="delivery-date"
                    v-model="form.deliveryDate"
                    :required="true"
                />

                <InputError
                    class="mt-2"
                    :message="form.errors['deliveryDate']"
                />
            </div>
            <div class="mt-4">
                <InputLabel
                    for="shipper_id"
                    :value="$t('Shipper')"
                    class="text-neutral-500"
                />

                <select
                    class="w-full mt-1 rounded-md focus:ring-primary focus:border-primary border-editor text-primary"
                    id="shipper_id"
                    v-model="form.shipper"
                    required
                >
                    <option :value="null" class="capitalize">
                        {{ $t("Choose a shipper") }}
                    </option>

                    <option
                        :value="shipper.id"
                        v-for="shipper in props.shippers"
                        :key="shipper.id"
                    >
                        {{ shipper.name }}
                    </option>
                </select>

                <InputError class="mt-2" :message="form.errors['shipper']" />
            </div>

            <div class="mt-4">
                <div class="flex gap-3 items-center">
                    <input
                        type="checkbox"
                        id="freeShipping-field"
                        v-model="form.free_shipping"
                    />
                    <label
                        for="freeShipping-field"
                        class="block font-medium cursor-pointer text-primary"
                    >
                        {{ $t("Offer Free Shipping") }}
                    </label>
                </div>
                <InputError
                    class="mt-2"
                    :message="form.errors['free_shipping']"
                />
            </div>

            <button
                :class="{
                    'opacity-25 cursor-not-allowed': form.processing,
                }"
                :disabled="form.processing"
                type="Submit"
                class="w-full cursor-pointer rounded-lg border border-primary bg-primary px-2 py-3 font-medium text-white transition hover:bg-opacity-90 mt-4"
            >
                {{ $t("Order.update") }}
            </button>
        </form>
    </Modal>
</template>
