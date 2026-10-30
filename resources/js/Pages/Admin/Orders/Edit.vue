<script setup>
import { trans } from "laravel-vue-i18n";
import { useForm } from "@inertiajs/vue3";
import InputError from "@/js/Components/InputError.vue";
import InputLabel from "@/js/Components/InputLabel.vue";
import TextInput from "@/js/Components/TextInput.vue";
import SelectInput from "@/js/Components/SelectInput.vue";
import DatePicker from "@/js/Components/DatePicker.vue";
import { computed, ref, onMounted, watch } from "vue";
import { useToast } from "vue-toastification";
import { getToastOptions } from "@/js/Utils/toast";
import { useLocationSelector } from "@/js/Composables/useLocationSelector";
import { useAppearanceStore } from "@/js/stores/appearance";
const locationSelector = useLocationSelector();
const appearanceStore = useAppearanceStore();
const modalPanelClasses = computed(() => [
    "mt-5 max-h-[70vh] overflow-hidden rounded-2xl border shadow-xl md:max-h-[550px]",
    appearanceStore.isDark
        ? "border-neutral-700 bg-neutral-800 shadow-black/50"
        : "border-slate-200 bg-white shadow-slate-900/10",
].join(" "));
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
        if (state && state.default_shipper_id) {
            form.shipper = state.default_shipper_id;
        }
    },
);
</script>

<template>
    <Modal v-slot="{ close }" ref="modalRef" max-width="md" padding-classes="p-0" :panel-classes="modalPanelClasses">
        <div class="max-h-[70vh] overflow-y-auto p-5 md:max-h-[550px]" :class="{ dark: appearanceStore.isDark }">
        <div class="mb-5 flex items-center justify-between border-b border-slate-100 pb-4 dark:border-neutral-700">
            <h1 class="text-xl font-semibold tracking-tight text-slate-800 dark:text-neutral-100">
                {{ $t("Order.edit") }}
            </h1>
            <button type="button" aria-label="Close" class="rounded-lg p-2 text-slate-400 transition hover:bg-slate-100 hover:text-slate-700 dark:text-neutral-400 dark:hover:bg-neutral-700 dark:hover:text-neutral-100" @click="close">
                <i class="ri-close-large-line text-xl" aria-hidden="true"></i>
            </button>
        </div>

        <form @submit.prevent="submitForm">
            <div class="mt-4">
                <InputLabel for="full-name" :value="$t('Full Name')" class="text-slate-700 dark:text-neutral-200" />

                <TextInput variant="admin" class="mt-1 block w-full rounded-xl" id="full-name" type="text" v-model="form.name" :required="true"
                    :placeholder="$t('Full Name')" />

                <InputError class="mt-2" :message="form.errors['name']" />
            </div>
            <div class="mt-4 w-full">
                <InputLabel for="state_id" :value="$t('State')" class="text-slate-700 dark:text-neutral-200" />

                <SelectInput variant="admin" @change="locationSelector.getCities(form.state)" required v-model="form.state" id="state_id">
                    <option :value="null" class="capitalize">
                        {{ $t("Choose a state") }}
                    </option>

                    <option :value="state.id" v-for="state in states" :key="state.id">
                        {{ state.name }}
                    </option>
                </SelectInput>

                <InputError class="mt-2" :message="form.errors.state" />
            </div>
            <div class="mt-4 w-full">
                <InputLabel for="city_id" :value="$t('City')" class="text-slate-700 dark:text-neutral-200" />

                <SelectInput variant="admin" @change="locationSelector.getLocalities(form.city)" required v-model="form.city" id="city_id">
                    <option :value="null" class="capitalize">
                        {{ $t("Choose a city") }}
                    </option>

                    <option :value="city.id" v-for="city in locationSelector.cities.value" :key="city.id">
                        {{ city.name }}
                    </option>
                </SelectInput>

                <InputError class="mt-2" :message="form.errors.city" />
            </div>
            <div class="mt-4 w-full">
                <InputLabel for="locality_id" :value="$t('Locality')" class="text-slate-700 dark:text-neutral-200" />

                <SelectInput variant="admin" required v-model="form.locality" id="locality_id">
                    <option :value="null" class="capitalize">
                        {{ $t("Choose a locality") }}
                    </option>

                    <option :value="locality.id" v-for="locality in locationSelector.localities.value"
                        :key="locality.id">
                        {{ locality.name }}
                    </option>
                </SelectInput>

                <InputError class="mt-2" :message="form.errors.locality" />
            </div>

            <div class="mt-4">
                <InputLabel for="address" :value="$t('Address')" class="text-slate-700 dark:text-neutral-200" />

                <TextInput variant="admin" class="mt-1 block w-full rounded-xl" id="address" type="text" v-model="form.address" :required="true"
                    :placeholder="$t('Full Shipping Address')" />

                <InputError class="mt-2" :message="form.errors['address']" />
            </div>

            <div class="mt-4">
                <InputLabel for="phone" :value="$t('Phone Number')" class="text-slate-700 dark:text-neutral-200" />

                <TextInput variant="admin" class="mt-1 block w-full rounded-xl" id="phone" v-model="form.phone" :required="false" type="number"
                    :placeholder="$t('8 digits phone number')" />
                <InputError class="mt-2" :message="form.errors['phone']" />
            </div>
            <div class="mt-4">
                <InputLabel for="phone" :value="$t('phone2')" class="text-slate-700 dark:text-neutral-200" />

                <TextInput variant="admin" class="mt-1 block w-full rounded-xl" id="phone" v-model="form.phone2" :required="false" type="number"
                    :placeholder="$t('8 digits phone number')" />
                <InputError class="mt-2" :message="form.errors['phone2']" />
            </div>

            <div class="mt-4">
                <label for="delivery-date" class="font-medium text-slate-700 dark:text-neutral-200">
                    {{ $t("Delivery_date") }}
                    <span class="ltr"> : {{ form.deliveryDate }}</span>
                </label>
                <DatePicker class="mt-1 block w-full h-[42px]" id="delivery-date" v-model="form.deliveryDate"
                    :required="true" />

                <InputError class="mt-2" :message="form.errors['deliveryDate']" />
            </div>
            <div class="mt-4">
                <InputLabel for="shipper_id" :value="$t('Shipper')" class="text-slate-700 dark:text-neutral-200" />

                <SelectInput variant="admin" id="shipper_id" v-model="form.shipper" required>
                    <option :value="null" class="capitalize">
                        {{ $t("Choose a shipper") }}
                    </option>

                    <option :value="shipper.id" v-for="shipper in props.shippers" :key="shipper.id">
                        {{ shipper.name }}
                    </option>
                </SelectInput>

                <InputError class="mt-2" :message="form.errors['shipper']" />
            </div>

            <div class="mt-4">
                <div class="flex gap-3 items-center">
                    <input type="checkbox" id="freeShipping-field" v-model="form.free_shipping" class="accent-primary" />
                    <label for="freeShipping-field" class="block cursor-pointer font-medium text-slate-700 dark:text-neutral-200">
                        {{ $t("Offer Free Shipping") }}
                    </label>
                </div>
                <InputError class="mt-2" :message="form.errors['free_shipping']" />
            </div>

            <button :class="{
                'opacity-25 cursor-not-allowed': form.processing,
            }" :disabled="form.processing" type="Submit"
                class="mt-5 w-full cursor-pointer rounded-xl border border-teal-700 bg-teal-700 px-4 py-3 font-semibold text-white shadow-sm transition hover:border-teal-800 hover:bg-teal-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-teal-500 focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-60 dark:focus-visible:ring-offset-neutral-800">
                {{ $t("Order.update") }}
            </button>
        </form>
        </div>
    </Modal>
</template>
