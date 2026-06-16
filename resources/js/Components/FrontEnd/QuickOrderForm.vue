<script setup>
import InputError from "@/js/Components/InputError.vue";
import InputLabel from "@/js/Components/InputLabel.vue";
import TextInput from "@/js/Components/TextInput.vue";
import { useForm, Link, usePage } from "@inertiajs/vue3";
import { computed, ref, watch } from "vue";
import axios from "axios";
import { useLanguageStore } from "@/js/stores/Language";
import { trans } from "laravel-vue-i18n";
import OrderPlacedImage from "@/assets/images/done.png";
import { trackFacebookEvent } from "@/js/Utils/facebook";

const props = defineProps({
    product: {
        type: Object,
        required: true,
    },
    quantity: {
        type: [Number, String],
        required: true,
    },
    states: {
        type: Object,
        required: true,
    },
});

const page = usePage();
const languageStore = useLanguageStore();
const isOrderPlaced = ref(false);
const abandonedTimer = ref(null);

const form = useForm({
    name: "",
    state: props.states.data[0],
    address: "",
    phone: "",
    note: "",
    cart: null,
    terms_accepted: false,
    coupon: {},
    total: 0,
    free_shipping: false,
});

const getLocalizedStateName = (state_id) => {
    const state = props.states.data.find((item) => item.id == state_id);
    const translation = state?.translations?.find(
        (t) => t.locale === languageStore.currentLocale,
    );
    return translation?.name ?? state?.name ?? "";
};

const subtotal = computed(() => {
    const price = parseFloat(props.product.price) || 0;
    const qty = Number(props.quantity) || 1;
    return (price * qty).toFixed(3);
});

const shippingCost = computed(() => {
    if (props.product.free_shipping) {
        return "0.000";
    }
    return Number(form.state?.shipping_cost ?? 0).toFixed(3);
});

const total = computed(() => {
    return (
        parseFloat(subtotal.value) + parseFloat(shippingCost.value)
    ).toFixed(3);
});

watch(
    () => props.product.free_shipping,
    (value) => {
        form.free_shipping = !!value;
    },
    { immediate: true },
);

function saveAbandonedOrder() {
    const normalizedPhone = String(form.phone ?? "").replace(/\D/g, "");

    if (!/^[234579]\d{7}$/.test(normalizedPhone)) {
        return;
    }

    clearTimeout(abandonedTimer.value);

    abandonedTimer.value = setTimeout(() => {
        axios
            .post(route("FE.orders.abandoned"), {
                phone: normalizedPhone,
                name: form.name || null,
                address: form.address || null,
                note: form.note || null,
                state: form.state
                    ? {
                          id: form.state.id,
                          shipping_cost: form.state.shipping_cost,
                      }
                    : null,
                cart: [buildCartItem()],
                total: Number(total.value) || 0,
                free_shipping: !!props.product.free_shipping,
            })
            .catch(() => {});
    }, 500);
}

watch(
    [
        () => form.phone,
        () => form.name,
        () => form.address,
        () => form.note,
        () => form.state?.id,
    ],
    saveAbandonedOrder,
);

function buildCartItem() {
    return {
        ...props.product,
        quantity: Number(props.quantity) || 1,
    };
}

function resetForm() {
    form.name = "";
    form.state = props.states.data[0];
    form.address = "";
    form.phone = "";
    form.note = "";
    form.cart = null;
    form.terms_accepted = false;
    form.coupon = {};
    form.total = 0;
    form.free_shipping = !!props.product.free_shipping;
}

function submitForm() {
    form.total = total.value;
    form.cart = [buildCartItem()];
    form.free_shipping = !!props.product.free_shipping;

    form.user_data = {
        phone: form.phone,
        name: form.name,
        address: form.address,
        state:
            form.state?.name ||
            form.state?.translations?.find(
                (t) => t.locale === languageStore.currentLocale,
            )?.name,
    };

    form.post(route("FE.orders.place"), {
        preserveScroll: true,
        onSuccess: () => {
            isOrderPlaced.value = true;
            resetForm();

            const conversionData = page.props.flash?.order_conversion_data;
            if (conversionData && window.gtag) {
                window.gtag("event", "conversion", {
                    send_to: "AW-17884200609/T4nVCOOPuOcbEKH97M9C",
                    value: conversionData.value,
                    currency: conversionData.currency,
                    transaction_id: conversionData.transaction_id,
                });
            }

            trackFacebookEvent(
                "Purchase",
                {
                    content_ids: [props.product.id?.toString()],
                    content_type: "product",
                    currency: "TND",
                    value: Number(total.value),
                    num_items: Number(props.quantity) || 1,
                },
                { eventID: `purchase-${Date.now()}` },
            );
        },
    });
}
</script>

<template>
    <div v-if="!isOrderPlaced">
        <div
            class="mb-6 p-4 bg-white rounded-lg border border-neutral-200 text-sm"
        >
            <p class="font-semibold text-primary">{{ product.name }}</p>
            <p class="text-neutral-600 mt-1">
                {{ $t("QuickOrder.quantity") }}: {{ quantity }}
            </p>
            <p class="text-lg font-semibold text-meta-1 mt-2">
                {{ $t("Total") }}: {{ total }} {{ $t("currency") }}
                <span
                    v-if="product.free_shipping"
                    class="text-sm font-normal text-sky-800 ms-2"
                >
                    ({{ $t("Offer Free Shipping") }})
                </span>
            </p>
        </div>

        <form @submit.prevent="submitForm" class="grid md:grid-cols-2 gap-6">
            <div class="space-y-4">
                <div>
                    <InputLabel
                        for="quick-name"
                        :value="$t('Full Name')"
                        class="text-neutral-500"
                    />
                    <TextInput
                        id="quick-name"
                        class="mt-1 block w-full"
                        v-model="form.name"
                        required
                        :placeholder="$t('Full Name')"
                    />
                    <InputError class="mt-2" :message="form.errors.name" />
                </div>

                <div class="grid sm:grid-cols-2 gap-4">
                    <div>
                        <InputLabel
                            for="quick-phone"
                            :value="$t('Phone Number')"
                            class="text-neutral-500"
                        />
                        <TextInput
                            id="quick-phone"
                            class="mt-1 block w-full"
                            v-model="form.phone"
                            required
                            type="tel"
                            :placeholder="$t('8 digits phone number')"
                        />
                        <InputError class="mt-2" :message="form.errors.phone" />
                    </div>
                    <div>
                        <InputLabel
                            for="quick-state"
                            :value="$t('State')"
                            class="text-neutral-500"
                        />
                        <select
                            id="quick-state"
                            v-model="form.state"
                            required
                            class="w-full mt-1 rounded-md focus:ring-primary focus:border-primary border-editor text-primary"
                        >
                            <option
                                v-for="state in states.data"
                                :key="state.id"
                                :value="state"
                            >
                                {{ getLocalizedStateName(state.id) }}
                            </option>
                        </select>
                        <InputError class="mt-2" :message="form.errors.state" />
                    </div>
                </div>

                <div>
                    <InputLabel
                        for="quick-address"
                        :value="$t('Address')"
                        class="text-neutral-500"
                    />
                    <TextInput
                        id="quick-address"
                        class="mt-1 block w-full"
                        v-model="form.address"
                        required
                        :placeholder="$t('Full Shipping Address')"
                    />
                    <InputError class="mt-2" :message="form.errors.address" />
                </div>

                <div>
                    <InputLabel
                        for="quick-note"
                        :value="`${$t('Note')} (${$t('optional.f')})`"
                        class="text-neutral-500"
                    />
                    <textarea
                        id="quick-note"
                        v-model="form.note"
                        class="mt-1 block w-full text-primary rounded-md border-editor shadow-sm focus:border-primary focus:ring-primary h-24 resize-none"
                        :placeholder="$t('note_placeholder')"
                    ></textarea>
                    <InputError class="mt-2" :message="form.errors.note" />
                </div>
            </div>

            <div class="flex flex-col">
                <div
                    class="bg-white p-4 rounded-lg border border-neutral-200 mb-4"
                >
                    <div class="flex justify-between text-neutral-700 mb-2">
                        <span>{{ $t("Sub Total") }}</span>
                        <span>{{ subtotal }} {{ $t("currency") }}</span>
                    </div>
                    <div class="flex justify-between text-neutral-700 mb-2">
                        <span>
                            {{ $t("Shipping Cost") }}
                            <template v-if="form.state">
                                ({{ getLocalizedStateName(form.state.id) }})
                            </template>
                        </span>
                        <span>{{ shippingCost }} {{ $t("currency") }}</span>
                    </div>
                    <div
                        class="flex justify-between font-semibold text-sky-900 text-lg pt-2 border-t"
                    >
                        <span>{{ $t("Total") }}</span>
                        <span>{{ total }} {{ $t("currency") }}</span>
                    </div>
                    <InputError class="mt-2" :message="form.errors.total" />
                </div>

                <div class="mt-auto">
                    <div class="flex gap-3 items-start">
                        <input
                            type="checkbox"
                            id="quick-terms"
                            v-model="form.terms_accepted"
                            class="mt-1"
                        />
                        <label
                            for="quick-terms"
                            class="text-sm text-neutral-600 cursor-pointer"
                        >
                            {{ $t("Terms.accept") }}
                            <Link href="/terms" class="underline text-meta-5">
                                {{ $t("Nav.terms") }}
                            </Link>
                        </label>
                    </div>
                    <InputError
                        class="mt-2"
                        :message="form.errors.terms_accepted"
                    />

                    <button
                        type="submit"
                        :disabled="form.processing"
                        :class="{
                            'opacity-50 cursor-not-allowed': form.processing,
                        }"
                        class="w-full mt-4 cursor-pointer rounded-lg border border-primary bg-primary py-3 px-4 font-medium text-white transition hover:bg-opacity-90"
                    >
                        {{
                            form.processing
                                ? $t("Cart.loading")
                                : $t("QuickOrder.place")
                        }}
                    </button>
                </div>
            </div>
        </form>
    </div>

    <div
        v-else
        class="py-8 flex flex-col items-center justify-center gap-4 text-center"
    >
        <img :src="OrderPlacedImage" class="w-24" alt="Order placed" />
        <p class="text-lg text-primary font-semibold">{{ $t("Thank You!") }}</p>
        <p class="text-primary max-w-md">{{ $t("Order.placedMessage") }}</p>
    </div>
</template>
