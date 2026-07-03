<script setup>
import PageHeader from "@/js/Components/Admin/PageHeader.vue";
import { useCartStore } from "@/js/stores/Cart";
import ProductCard from "@/js/Components/FrontEnd/ProductCard.vue";
import { ref, computed, watch, nextTick } from "vue";
import TextInput from "@/js/Components/TextInput.vue";
import InputError from "@/js/Components/InputError.vue";
import InputLabel from "@/js/Components/InputLabel.vue";
import { useForm } from "@inertiajs/vue3";
import { useLanguageStore } from "@/js/stores/Language";
import { useToast } from "vue-toastification";
import { getToastOptions } from "@/js/Utils/toast";
import { trans } from "laravel-vue-i18n";
import DatePicker from "@/js/Components/DatePicker.vue";
import { useLocationSelector } from "@/js/Composables/useLocationSelector";
import axios from "axios";
const ignorePhoneWatcher = ref(false);
const toast = useToast();
const languageStore = useLanguageStore();
const locationSelector = useLocationSelector();
const cartStore = useCartStore();
const getLocalizedStateName = (state_id) => {
    if (!state_id) return "";
    let state = props.states.data.find((item) => item.id == state_id);
    let translation = state.translations.find(
        (state_translation) =>
            state_translation.locale == languageStore.currentLocale,
    );
    return translation.name;
};

const getShippingCost = (state_id) => {
    if (!state_id) return 0;
    if (form.free_shipping) return 0;
    let state = props.states.data.find((item) => item.id == state_id);
    return state.shipping_cost;
};
const props = defineProps({
    products: {
        type: Array,
        required: true,
    },
    states: {
        type: Array,
        required: true,
    },
    shippers: {
        type: Array,
        required: true,
    },
    couponCodes: {
        type: Array,
        required: true,
    },
});

const search = ref("");

const filteredProducts = computed(() => {
    return props.products.filter((product) =>
        product.name.toLowerCase().includes(search.value.toLowerCase()),
    );
});

const form = useForm({
    name: "",
    shipper: null,
    deliveryDate: null,
    state: null,
    city: null,
    locality: null,
    address: "",
    phone: "",
    phone2: "",
    note: "",
    status: "pending",
    cart: null,
    coupon: null,
    total: 0,
    shipping_cost: 0,
    client_id: null,
    nrp: false,
    free_shipping: false,
});
const resetForm = () => {
    form.name = "";
    form.state = null;
    form.city = null;
    form.locality = null;
    form.shipper = null;
    form.deliveryDate = null;
    form.address = "";
    form.phone = "";
    form.phone2 = "";
    form.note = "";
    form.status = "pending";
    form.cart = null;
    form.coupon = null;
    form.total = 0;
    form.shipping_cost = 0;
    form.client_id = null;
    form.nrp = false;
    form.free_shipping = false;
};
const getDiscountValue = (coupon_id) => {
    if (!coupon_id) return 0;
    let coupon = props.couponCodes.find((item) => item.id == coupon_id);
    return coupon.value;
};
const total = computed(() => {
    const discount = 1 - getDiscountValue(form.coupon) / 100;
    return (cartStore.total * discount + getShippingCost(form.state)).toFixed(
        3,
    );
});
function clearFlatpickrInputs() {
    const flatpickrInputs = document.querySelectorAll(".flatpickr-input");

    flatpickrInputs.forEach((input) => {
        if (input._flatpickr) {
            input._flatpickr.clear();
        }
    });
}
function submitForm() {
    form.total = total.value;
    form.cart = cartStore.cart;
    form.shipping_cost = getShippingCost(form.state);
    form.post(route("orders.store"), {
        preserveScroll: true,
        onSuccess: () => {
            let message = trans("Order.created_successfully");
            toast.success(message, getToastOptions());
            resetForm();
            clearFlatpickrInputs();
            cartStore.clearCart();
        },
    });
}

const foundClients = ref([]);
const selectedClient = ref(null);
const searchingClient = ref(false);
const showSearchingClient = computed(() => {
    return String(form.phone).length > 0 && searchingClient.value == true;
});
watch(
    () => form.phone,
    async (newPhone) => {
        if (ignorePhoneWatcher.value) return;
        foundClients.value = [];
        selectedClient.value = null;
        form.client_id = null;
        form.locality = null;
        form.city = null;
        form.state = null;
        form.address = "";
        form.name = "";
        form.note = "";

        searchingClient.value = true;
        if (String(newPhone).length === 8) {
            try {
                const { data } = await axios.get(
                    `/clients/${newPhone}/search`,
                    {
                        headers: {
                            Accept: "application/json",
                        },
                    },
                );
                if (Array.isArray(data) && data.length) {
                    foundClients.value = data;
                } else {
                    foundClients.value = [];
                }
            } finally {
                searchingClient.value = false;
            }
        }
    },
);
function populateFormWithClient(client) {
    if (!client) return;
    selectedClient.value = client;
    form.client_id = client.id;
    form.name = client.name;
    form.state = client.state_id;
    form.address = client.address;
    ignorePhoneWatcher.value = true;
    form.phone = client.phone;
    nextTick(() => {
        ignorePhoneWatcher.value = false;
    });
    form.phone2 = client.phone2;
    if (client.locality) {
        locationSelector.getCities(client.state_id).then(() => {
            form.city = client.city_id;
            locationSelector.getLocalities(client.city_id).then(() => {
                form.locality = client.locality_id;
            });
        });
    } else {
        locationSelector.getCities(client.state_id).then(() => {
            form.city = client.city_id;
        });
    }
}

const formatDate = (dateString) => {
    if (!dateString) return "";
    const date = new Date(dateString);
    return date.toLocaleString("fr-FR", { dateStyle: "short" });
};

watch(
    () => form.state,
    (newState) => {
        if (!newState) form.shipper = null;

        const state = props.states.data.find((item) => item.id == newState);

        if (state && state.default_shipper.id) {
            form.shipper = state.default_shipper.id;
        }
    },
);
</script>

<template>
    <Head :title="$t('New Order')" />
    <PageHeader :page-title="$t('New Order')" />

    <div class="grid gap-4 lg:gap-6 lg:grid-cols-2">
        <div class="bg-white rounded shadow-1 p-4">
            <div class="flex items-center justify-between gap-3 mt-4">
                <h2 class="text-sky-900 font-semibold text-2xl">
                    {{ $t("Cart") }}
                </h2>
                <p class="text-primary text-2xl font-bold">
                    {{ cartStore.total }} {{ $t("currency") }}
                </p>
            </div>

            <div v-if="cartStore.count">
                <div
                    class="grid grid-cols-1 :gap-3 border-b border-neutral-200 py-6"
                    v-for="(product, index) in cartStore.cart"
                    :key="index"
                >
                    <div
                        class="flex items-stretch flex-col min-[600px]:flex-row gap-3 min-[600px]:gap-1 w-full max-[600px]:justify-center max-[600px]:mb-4"
                    >
                        <div
                            class="img-box w-70px min-[600px]:w-[80px] flex-shrink-0"
                        >
                            <img
                                :src="product.main_image_url"
                                :alt="product.name"
                                class="xl:w-[80px] rounded-xl object-cover"
                            />
                        </div>
                        <div
                            class="pro-data w-full px-3 min-[600px]:flex-1 max-[600px]:text-center"
                        >
                            <div>
                                <Link
                                    class="font-semibold text-xl min-[550px]-leading-8 hover:underline text-primary hover:text-sky-600"
                                    :href="route('products.show', product)"
                                >
                                    {{ product.name }}
                                </Link>

                                <p
                                    class="font-medium text-lg min-[550px]-leading-8 text-sky-700"
                                >
                                    {{ `${product.price} ${$t("currency")}` }}
                                </p>
                            </div>
                        </div>
                        <div class="min-[600px]:justify-self-end">
                            <div
                                class="flex items-center w-max mx-auto justify-center"
                            >
                                <button
                                    @click="
                                        cartStore.decreaseQuantity(product.id)
                                    "
                                    class="w-10 h-10 p-2 border border-neutral-200 flex items-center justify-center shadow-sm shadow-transparent transition-colors duration-500 hover:shadow-neutral-200 hover:border-neutral-300 hover:bg-zinc-100"
                                >
                                    -
                                </button>
                                <input
                                    type="text"
                                    class="border-y border-neutral-200 outline-none text-neutral-900 font-semibold text-lg w-[80px] placeholder:text-neutral-900 p-2 h-10 text-center bg-transparent pointer-events-none"
                                    placeholder="1"
                                    :value="product.quantity"
                                    readonly
                                />
                                <button
                                    @click="
                                        cartStore.increaseQuantity(product.id)
                                    "
                                    class="w-10 h-10 p-2 border border-neutral-200 flex items-center justify-center shadow-sm shadow-transparent transition-colors duration-500 hover:shadow-neutral-200 hover:border-neutral-300 hover:bg-zinc-100"
                                >
                                    +
                                </button>
                            </div>
                            <button
                                @click="cartStore.removeFromCart(product.id)"
                                class="text-sm mx-auto mt-4 text-red-600 underline hover:text-red-500 block"
                            >
                                {{ $t("Remove") }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div v-else class="text-center text-lg text-neutral-500 py-6">
                {{ $t("No items in the cart") }}
            </div>
        </div>

        <div class="bg-white rounded shadow-1 p-4">
            <h2 class="text-sky-900 font-semibold text-2xl mt-4">
                {{ $t("Products") }}
            </h2>

            <div>
                <TextInput
                    id="search"
                    class="mt-2 block w-full"
                    :placeholder="$t('Search for products')"
                    v-model="search"
                    :required="true"
                    type="search"
                />
            </div>

            <div
                class="mt-4 grid gap-x-2 gap-y-4 lg:grid-cols-2 pb-8 grid-cols-[repeat(auto-fit,minmax(200px,1fr))] overflow-y-auto max-h-[600px]"
            >
                <ProductCard
                    v-for="product in filteredProducts"
                    :key="product.id"
                    :product="product"
                />
            </div>
        </div>
    </div>
    <div class="grid gap-4 lg:gap-6 lg:grid-cols-2 mt-8">
        <div class="bg-white rounded shadow-1 p-4 order-2 lg:order-1">
            <h2 class="text-sky-900 font-semibold text-2xl mt-4">
                {{ $t("Shipping Information") }}
            </h2>

            <form @submit.prevent="submitForm">
                <div
                    class="flex gap-4 flex-col sm:flex-row sm:justify-between sm:items-start mt-4"
                >
                    <div class="w-full">
                        <InputLabel
                            for="phone"
                            :value="$t('Phone Number')"
                            class="text-neutral-500"
                        />

                        <TextInput
                            class="mt-1 block w-full"
                            id="phone"
                            v-model="form.phone"
                            :required="true"
                            type="number"
                            :placeholder="$t('8 digits phone number')"
                        />
                        <InputError
                            class="mt-2"
                            :message="form.errors['phone']"
                        />
                    </div>
                    <div class="w-full">
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
                        <InputError
                            class="mt-2"
                            :message="form.errors['phone2']"
                        />
                    </div>
                </div>
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

                <div
                    class="flex gap-4 flex-col sm:flex-row sm:justify-between sm:items-start mt-4"
                >
                    <div class="w-full">
                        <InputLabel
                            for="state_id"
                            :value="$t('State')"
                            class="text-neutral-500"
                        />

                        <select
                            class="w-full mt-1 rounded-md focus:ring-primary focus:border-primary border-editor text-primary"
                            id="state_id"
                            v-model="form.state"
                            required
                            @change="locationSelector.getCities(form.state)"
                        >
                            <option :value="null">
                                {{ $t("State") }}
                            </option>
                            <option
                                :value="state.id"
                                v-for="state in props.states.data"
                                :key="state.id"
                            >
                                {{ state.name }}
                            </option>
                        </select>
                        <InputError
                            class="mt-2"
                            :message="form.errors['state']"
                        />
                    </div>
                </div>
                <div
                    class="flex gap-4 flex-col sm:flex-row sm:justify-between sm:items-start mt-4"
                >
                    <div class="w-full">
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
                    <div class="w-full">
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

                        <InputError
                            class="mt-2"
                            :message="form.errors.locality"
                        />
                    </div>
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

                    <InputError
                        class="mt-2"
                        :message="form.errors['address']"
                    />
                </div>

                <div
                    class="flex gap-4 flex-col sm:flex-row sm:justify-between sm:items-start mt-4"
                >
                    <div class="w-full">
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
                            <option :value="null">
                                {{ $t("Shipper") }}
                            </option>
                            <option
                                :value="shipper.id"
                                v-for="shipper in props.shippers"
                                :key="shipper.id"
                            >
                                {{ shipper.name }}
                            </option>
                        </select>

                        <InputError
                            class="mt-2"
                            :message="form.errors['shipper']"
                        />
                    </div>
                    <div class="w-full">
                        <InputLabel
                            for="coupon"
                            :value="`${$t('Coupon Code')} (${$t('optional.f')})`"
                            class="text-neutral-500"
                        />

                        <select
                            class="w-full mt-1 rounded-md focus:ring-primary focus:border-primary border-editor text-primary"
                            id="coupon"
                            v-model="form.coupon"
                            required
                        >
                            <option :value="null">
                                {{ $t("Coupon Code") }}
                            </option>
                            <option
                                :value="coupon.id"
                                v-for="coupon in props.couponCodes"
                                :key="coupon.id"
                            >
                                {{ coupon.code }}
                            </option>
                        </select>
                        <InputError
                            class="mt-2"
                            :message="form.errors['coupon']"
                        />
                    </div>
                </div>

                <div
                    class="mt-4 flex gap-4 flex-col sm:flex-row sm:justify-between sm:items-start"
                >
                    <div class="w-full">
                        <InputLabel
                            for="delivery-date"
                            :value="$t('Delivery_date')"
                            class="text-neutral-500"
                        />

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
                    <div class="w-full">
                        <InputLabel
                            for="status-input"
                            :value="$t('Status')"
                            class="text-neutral-500"
                        />

                        <select
                            class="w-full mt-1 rounded-md focus:ring-primary focus:border-primary border-editor text-primary"
                            id="status-input"
                            v-model="form.status"
                            required
                        >
                            <option value="pending">
                                {{ $t("pending") }}
                            </option>
                            <option value="confirmed">
                                {{ $t("confirmed") }}
                            </option>
                            <option value="canceled">
                                {{ $t("canceled") }}
                            </option>
                            <option value="shipped">
                                {{ $t("shipped") }}
                            </option>
                            <option value="delivered">
                                {{ $t("delivered") }}
                            </option>
                            <option value="returned">
                                {{ $t("returned") }}
                            </option>
                        </select>

                        <InputError
                            class="mt-2"
                            :message="form.errors['status']"
                        />
                    </div>
                </div>

                <div class="mt-4">
                    <InputLabel
                        for="note"
                        :value="`${$t('Note')} (${$t('optional.f')})`"
                        class="text-neutral-500"
                    />

                    <textarea
                        id="note"
                        class="mt-1 block w-full text-primary rounded-md border-editor shadow-sm focus:border-primary focus:ring-primary h-25 resize-none"
                        v-model="form.note"
                        :placeholder="$t('note_create_placeholder')"
                    >
                    </textarea>

                    <InputError class="mt-2" :message="form.errors['note']" />
                </div>
                <div class="mt-4">
                    <div class="flex gap-3 items-center">
                        <input
                            type="checkbox"
                            id="nrp-field"
                            v-model="form.nrp"
                        />
                        <label
                            for="nrp-field"
                            class="block font-medium cursor-pointer text-primary"
                        >
                            {{ $t("markAsNrp") }}
                        </label>
                    </div>
                    <InputError class="mt-2" :message="form.errors['nrp']" />
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

                <input
                    :class="{
                        'opacity-25 cursor-not-allowed': form.processing,
                    }"
                    :disabled="form.processing"
                    type="Submit"
                    :value="$t('Place Order')"
                    class="w-full cursor-pointer rounded-lg border border-primary bg-primary py-2 px-4 font-medium text-white transition hover:bg-opacity-90 mt-4"
                />
            </form>
        </div>

        <div class="bg-white rounded shadow-1 p-4 order-1 lg:order-2">
            <div
                v-if="showSearchingClient"
                class="text-sm text-neutral-500 mt-2"
            >
                {{ $t("Searching for clients...") }}
            </div>
            <div v-if="foundClients.length">
                <h2 class="text-sky-900 font-semibold text-2xl mt-4">
                    {{ $t("Found Clients") }}
                </h2>
                <div
                    v-for="client in foundClients"
                    :key="client.id"
                    class="py-4 text-meta-4 border-b last:border-b-0"
                    dir="auto"
                >
                    <Link
                        :href="route('clients.show', client.id)"
                        class="text-xl font-semibold mb-2 block text-primary hover:underline hover:text-blue-800"
                    >
                        {{ client.name }}
                    </Link>
                    <div class="flex text-sky-700 items-center gap-2">
                        <i class="ri-customer-service-2-fill text-lg"></i>
                        <span dir="ltr">
                            {{ client.phone }}
                            <span v-if="client.phone2"
                                >/ {{ client.phone2 }}</span
                            >
                        </span>
                    </div>
                    <div class="mb-1 text-sky-700 flex items-center gap-2">
                        <i class="ri-map-pin-line text-lg"></i>
                        <span v-if="client.locality">
                            {{
                                `${client.locality.name} / ${client.city.name} / ${client.state.name}`
                            }}
                        </span>
                        <span v-else>{{ client.state.name }}</span>
                    </div>
                    <div class="mb-1 text-sky-700 flex items-center gap-2">
                        <i class="ri-road-map-line text-lg"></i>
                        <span dir="auto">{{ client.address }}</span>
                    </div>
                    <div dir="auto">
                        <h4 class="text-primary font-semibold text-xl mb-1">
                            {{ $t("Latest Orders :") }}
                        </h4>
                        <p v-if="!client.orders || !client.orders.length">
                            {{ $t("No recent orders found") }}
                        </p>
                        <ul v-else>
                            <li
                                v-for="order in client.orders"
                                :key="order.id"
                                class="flex items-center justify-between flex-wrap text-lg text-sky-700 mb-3"
                            >
                                <div>
                                    #{{ order.id }} -
                                    {{ formatDate(order.created_at) }}
                                    <span
                                        v-if="order.deleted_at"
                                        class="text-red-500 text-lg"
                                    >
                                        <i class="ri-delete-bin-5-fill"></i>
                                    </span>
                                    <span> - {{ $t(order.status) }} </span>
                                </div>
                                <Link
                                    v-if="!order.deleted_at"
                                    :href="route('orders.show', order)"
                                    class="bg-slate-500 text-white text-xl rounded-md px-2 py-1 hover:bg-opacity-90"
                                >
                                    <i class="ri-eye-line"></i>
                                </Link>
                            </li>
                        </ul>
                    </div>
                    <div class="flex md:justify-end mt-2">
                        <button
                            @click="populateFormWithClient(client)"
                            class="bg-green-600 text-white px-4 py-2 mt-2 rounded-md"
                            :disabled="
                                selectedClient && selectedClient.id == client.id
                            "
                            :class="{
                                'bg-opacity-50 cursor-not-allowed':
                                    selectedClient &&
                                    selectedClient.id == client.id,
                            }"
                        >
                            {{ $t("Use Client Information") }}
                        </button>
                    </div>
                </div>
            </div>
            <h2 class="text-sky-900 font-semibold text-2xl mt-4">
                {{ $t("Order Summary") }}
            </h2>
            <ul class="flex flex-col" v-if="cartStore.count">
                <template
                    v-for="(product, index) in cartStore.cart"
                    :key="index"
                >
                    <li class="border-b border-zinc-200 py-3">
                        <div class="flex gap-3 mb-2">
                            <div class="relative w-[80px] flex-shrink-0">
                                <img
                                    class="w-full"
                                    :src="product.main_image_url"
                                    :alt="product.name"
                                />
                                <div
                                    class="absolute bottom-0 left-0 text-white text-xs font-bold flex items-center justify-center w-10 h-7 rounded-sm bg-sky-800"
                                >
                                    x {{ product.quantity }}
                                </div>
                            </div>
                            <div>
                                <h3
                                    class="text-neutral-700 text-ellipsis overflow-hidden line-clamp text-lg font-semibold"
                                >
                                    {{
                                        cartStore.getLocalizedName(
                                            product.id,
                                            languageStore.currentLocale,
                                        )
                                    }}
                                </h3>
                                <p class="text-base font-bold text-sky-700">
                                    {{
                                        product.price +
                                        ` ${$t("Product.currency")}`
                                    }}
                                </p>
                            </div>
                        </div>
                    </li>
                </template>
            </ul>
            <div v-else class="text-center text-lg text-neutral-500 py-6">
                {{ $t("No items in the cart") }}
            </div>

            <InputError
                class="mt-2 w-max mx-auto"
                :message="form.errors['cart']"
            />
        </div>
    </div>

    <div>
        <div class="bg-white mt-8 px-4 py-4 rounded-md">
            <div class="flex flex-wrap justify-between items-center gap-4">
                <p class="sm:text-lg text-neutral-700">
                    {{ `${$t("Sub Total")} : ` }}
                </p>
                <p class="sm:text-lg text-neutral-700">
                    {{ `${cartStore.total}  ${$t("currency")}` }}
                </p>
            </div>
            <div class="flex flex-wrap justify-between items-center gap-4">
                <p class="sm:text-lg text-neutral-700">
                    {{
                        `${$t("Shipping Cost")} ${getLocalizedStateName(form.state) == "" ? "" : "- " + getLocalizedStateName(form.state)} : `
                    }}
                </p>
                <p class="sm:text-lg text-neutral-700">
                    {{ `${getShippingCost(form.state)}  ${$t("currency")}` }}
                </p>
            </div>
            <div class="flex flex-wrap justify-between items-center gap-4">
                <p class="sm:text-lg text-neutral-700">
                    {{ `${$t("Discount")} : ` }}
                </p>
                <p class="text-sm sm:text-lg text-neutral-700">
                    {{ `${getDiscountValue(form.coupon)} %` }}
                </p>
            </div>
            <div class="flex flex-wrap justify-between items-center gap-4">
                <p class="text-2xl font-semibold text-sky-900">
                    {{ `${$t("Total")} : ` }}
                </p>
                <p class="font-semibold text-2xl text-sky-900">
                    {{ `${total} ${$t("currency")} ` }}
                </p>
            </div>
        </div>
        <InputError class="mt-2" :message="form.errors['total']" />
    </div>
</template>

<style scoped>
.line-clamp {
    display: -webkit-box;
    -webkit-line-clamp: 3;
    line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
    text-overflow: ellipsis;
}
</style>
