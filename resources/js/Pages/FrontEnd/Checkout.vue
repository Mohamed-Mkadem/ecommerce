<script setup>
import PageTitle from "@/js/Components/FrontEnd/PageTitle.vue";
import { useCartStore } from "@/js/stores/Cart";
import { useLanguageStore } from "@/js/stores/Language";
import PrimaryLink from "@/js/Components/PrimaryLink.vue";
import InputError from "@/js/Components/InputError.vue";
import InputLabel from "@/js/Components/InputLabel.vue";
import TextInput from "@/js/Components/TextInput.vue";
import { useForm, Link, usePage } from "@inertiajs/vue3";
import { computed, ref, onMounted, watch } from "vue";
import axios from "axios";
import { useToast } from "vue-toastification";
import { getToastOptions } from "@/js/Utils/toast";
import { trans } from "laravel-vue-i18n";
import OrderPlacedImage from "@/assets/images/done.png";
import { trackFacebookEvent } from "@/js/Utils/facebook";
const toast = useToast();
const page = usePage();
const languageStore = useLanguageStore();
const cartStore = useCartStore();
const couponCodeIsProcessing = ref(false);
const abandonedTimer = ref(null);
const props = defineProps({
    states: { type: Object },
});
const getLocalizedStateName = (state_id) => {
    let state = props.states.data.find((item) => item.id == state_id);
    let translation = state.translations.find(
        (state_translation) =>
            state_translation.locale == languageStore.currentLocale,
    );
    return translation.name;
};

const defaultState =
    props.states["data"].find((state) => state.id === 1) ||
    props.states["data"][0];
const form = useForm({
    name: "",
    state: defaultState,
    address: "",
    phone: "",
    note: "",
    cart: null,
    coupon: {},
    total: 0,
});
const resetForm = () => {
    form.name = "";
    form.state = defaultState;
    form.address = "";
    form.phone = "";
    form.note = "";
    form.cart = null;
    form.coupon = {};
    form.total = 0;
};

// Check if any item in cart has free_shipping
const hasAnyFreeShipping = computed(() => {
    return cartStore.cart.some((item) => item.free_shipping === true);
});

// Calculate shipping cost based on items in cart
const shippingCost = computed(() => {
    if (hasAnyFreeShipping.value) {
        return 0;
    }
    return form.state?.shipping_cost ?? 0;
});

const total = computed(() => {
    let couponValue = form.coupon.value ?? 0;
    const discount = 1 - couponValue / 100;
    return (cartStore.total * discount + shippingCost.value).toFixed(3);
});
const isOrderPlaced = ref(false);

function saveAbandonedOrder() {
    const normalizedPhone = String(form.phone ?? "").replace(/\D/g, "");

    if (!/^[234579]\d{7}$/.test(normalizedPhone) || !cartStore.count) {
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
                cart: cartStore.cart,
                total: Number(total.value) || 0,
                free_shipping: hasAnyFreeShipping.value,
            })
            .catch(() => {});
    }, 500);
}

watch(
    [
        () => form.phone,
    ],
    saveAbandonedOrder,
);

function submitForm() {
    form.total = total.value;
    form.cart = cartStore.cart;

    // Add user data for better Facebook matching
    form.user_data = {
        phone: form.phone,
        name: form.name,
        address: form.address,
        state:
            form.state.name ||
            form.state.translations?.find(
                (t) => t.locale === languageStore.currentLocale,
            )?.name,
    };

    form.post(route("FE.orders.place"), {
        onSuccess: () => {
            isOrderPlaced.value = true;
            resetForm();
            cartStore.clearCart();

            // Google Ads Conversion Tracking
            const conversionData = page.props.flash?.order_conversion_data;

            if (conversionData && window.gtag) {
                window.gtag("event", "conversion", {
                    send_to: "AW-17884200609/T4nVCOOPuOcbEKH97M9C",
                    value: conversionData.value,
                    currency: conversionData.currency,
                    transaction_id: conversionData.transaction_id,
                });
            }
        },
    });
}

const codeModel = ref("");
const handleCheckCouponCode = async () => {
    if (!codeModel.value) {
        toast.error(trans("Please enter a coupon code!"), {
            ...getToastOptions(),
            timeout: 1000,
        });
        return;
    }
    couponCodeIsProcessing.value = true;
    try {
        const response = await axios.post(route("FE.codes.getCode"), {
            code: codeModel.value,
        });

        if (response.data) {
            form.coupon.value = response.data.value;
            form.coupon.code = response.data.code;
            form.coupon.id = response.data.id;
            codeModel.value = "";
            toast.success(
                trans("Coupon code applied successfully!"),
                getToastOptions(),
            );
        } else {
            throw new Error(trans("Invalid coupon code"));
        }
    } catch (error) {
        if (error.response?.status === 422) {
            toast.error(
                trans("Invalid coupon code. Please try again."),
                getToastOptions(),
            );
        } else {
            toast.error(
                trans("Something went wrong. Please try again later."),
                getToastOptions(),
            );
        }
    } finally {
        couponCodeIsProcessing.value = false;
    }
};
// Fire InitiateCheckout via Meta Pixel when the checkout page loads
onMounted(() => {
    if (!cartStore.count) {
        return;
    }

    const contents = cartStore.cart.map((item) => ({
        id: item.id?.toString(),
        quantity: item.quantity ?? 1,
        item_price: Number(item.price) || 0,
    }));
    const contentIds = contents.map((item) => item.id);
    const numItems = contents.reduce(
        (acc, item) => acc + (Number(item.quantity) || 0),
        0,
    );

    trackFacebookEvent(
        "InitiateCheckout",
        {
            content_ids: contentIds,
            contents,
            content_type: "product",
            currency: "TND",
            value: Number(total.value),
            num_items: numItems,
            event_source_url: window.location.href,
        },
        {
            eventID: `initcheckout-${Date.now()}`,
        },
    );
});
</script>

<template>
    <Head :title="$t('Checkout')" />

    <section class="py-12 relative" v-if="!isOrderPlaced">
        <div class="w-full max-w-7xl px-4 md:px-5 lg-6 mx-auto">
            <div v-if="cartStore.count">
                <PageTitle class="mb-8">{{ $t("Checkout") }}</PageTitle>

                <div class="py-8 grid md:grid-cols-2 gap-8">
                    <div class="order-last md:order-first">
                        <h2 class="text-primary-800 text-2xl font-semibold">
                            {{ $t("Shipping Information") }}
                        </h2>
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
                                    :placeholder="$t('Full Name')"
                                />

                                <InputError
                                    class="mt-2"
                                    :message="form.errors['name']"
                                />
                            </div>
                            <div
                                class="flex gap-4 flex-col sm:flex-row sm:justify-between sm:items-start mt-4"
                            >
                                <div class="w-full">
                                    <InputLabel
                                        for="phone"
                                        class="text-neutral-500"
                                    >
                                        <span>{{ $t("Phone Number") }}</span>
                                        <span class="text-red-500">*</span>
                                    </InputLabel>

                                    <TextInput
                                        class="mt-1 block w-full"
                                        id="phone"
                                        v-model="form.phone"
                                        :required="true"
                                        type="number"
                                        :placeholder="
                                            $t('8 digits phone number')
                                        "
                                    />
                                    <InputError
                                        class="mt-2"
                                        :message="form.errors['phone']"
                                    />
                                </div>
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
                                    >
                                        <option
                                            :value="state"
                                            v-for="state in props.states[
                                                'data'
                                            ]"
                                            :key="state.id"
                                        >
                                            {{
                                                getLocalizedStateName(state.id)
                                            }}
                                        </option>
                                    </select>
                                    <InputError
                                        class="mt-2"
                                        :message="form.errors['state']"
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
                                    :placeholder="$t('Full Shipping Address')"
                                />

                                <InputError
                                    class="mt-2"
                                    :message="form.errors['address']"
                                />
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
                                    :placeholder="$t('note_placeholder')"
                                >
                                </textarea>

                                <InputError
                                    class="mt-2"
                                    :message="form.errors['note']"
                                />
                            </div>

                            <input
                                :class="{
                                    'opacity-25 cursor-not-allowed':
                                        form.processing,
                                }"
                                :disabled="form.processing"
                                type="Submit"
                                :value="$t('Place Order')"
                                class="w-full cursor-pointer rounded-lg border border-primary bg-primary py-2 px-4 font-medium text-white transition hover:bg-opacity-90 mt-4"
                            />
                        </form>
                    </div>
                    <div class="order-first md:order-last">
                        <h2 class="text-primary-800 text-2xl font-semibold">
                            {{ $t("Order Summary") }}
                        </h2>
                        <ul
                            class="mt-4 flex flex-col"
                            :class="{
                                'overflow-y-auto h-100 md:h-auto':
                                    cartStore.count > 4,
                            }"
                        >
                            <template
                                v-for="(product, index) in cartStore.cart"
                                :key="index"
                            >
                                <li class="border-b border-zinc-200 py-3">
                                    <div class="flex gap-3 mb-2">
                                        <div
                                            class="relative w-[80px] flex-shrink-0"
                                        >
                                            <img
                                                class="w-full"
                                                :src="product.wrapper_main_image_url || product.main_image_url"
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
                                            <p
                                                class="text-base font-bold text-sky-700"
                                            >
                                                {{
                                                    product.price +
                                                    ` ${$t("Product.currency")}`
                                                }}
                                            </p>
                                        </div>
                                    </div>
                                    <InputError
                                        class="mt-1"
                                        :message="
                                            form.errors[`cart.${index}.id`]
                                        "
                                    />
                                    <InputError
                                        class="mt-1"
                                        :message="
                                            form.errors[
                                                `cart.${index}.quantity`
                                            ]
                                        "
                                    />
                                    <InputError
                                        class="mt-1"
                                        :message="
                                            form.errors[`cart.${index}.price`]
                                        "
                                    />
                                </li>
                            </template>
                        </ul>

                        <div>
                            <InputError
                                class="mt-2"
                                :message="form.errors['cart']"
                            />
                        </div>

                        <div>
                            <div class="mb-8">
                                <form
                                    @submit.prevent="handleCheckCouponCode"
                                    class="mt-4 flex-1"
                                >
                                    <InputLabel
                                        for="code"
                                        :value="$t('CouponCode.label')"
                                        class="text-neutral-500"
                                    />
                                    <div
                                        class="grid grid-cols-[1fr,_100px] items-center gap-2"
                                    >
                                        <TextInput
                                            class="mt-1 block w-full"
                                            id="code"
                                            type="text"
                                            v-model="codeModel"
                                            :required="true"
                                            :placeholder="$t('Coupon Code')"
                                        />
                                        <button
                                            class="bg-meta-3 hover:bg-opacity-80 text-white h-[42px] mt-1 rounded-md"
                                            :class="{
                                                'bg-neutral-500 cursor-not-allowed':
                                                    couponCodeIsProcessing,
                                            }"
                                            :disabled="couponCodeIsProcessing"
                                        >
                                            {{ $t("Apply") }}
                                        </button>
                                    </div>
                                </form>
                                <InputError
                                    class="mt-2"
                                    :message="form.errors['coupon']"
                                />
                                <InputError
                                    class="mt-2"
                                    :message="form.errors['coupon.id']"
                                />
                                <div v-if="form.coupon.code" class="flex gap-4">
                                    <p>
                                        {{
                                            `'${form.coupon.code}' ${$t("applied")}`
                                        }}
                                    </p>
                                    <button
                                        class="text-sm text-red-500 underline"
                                        @click="form.coupon = {}"
                                    >
                                        {{ $t("Remove") }}
                                    </button>
                                </div>
                            </div>

                            <div class="bg-neutral-100 px-4 py-4 rounded-md">
                                <div
                                    class="flex flex-wrap justify-between items-center gap-4"
                                >
                                    <p class="sm:text-lg text-neutral-700">
                                        {{ `${$t("Sub Total")} : ` }}
                                    </p>
                                    <p class="sm:text-lg text-neutral-700">
                                        {{
                                            `${cartStore.total}  ${$t("currency")}`
                                        }}
                                    </p>
                                </div>
                                <div
                                    class="flex flex-wrap justify-between items-center gap-4"
                                >
                                    <p class="sm:text-lg text-neutral-700">
                                        {{
                                            `${$t("Shipping Cost")} (${getLocalizedStateName(form.state["id"])}) : `
                                        }}
                                    </p>
                                    <p class="sm:text-lg text-neutral-700">
                                        {{
                                            `${shippingCost}  ${$t("currency")}`
                                        }}
                                        <span
                                            v-if="hasAnyFreeShipping"
                                            class="text-sm font-normal text-sky-800 ms-2"
                                        >
                                            ({{ $t("Offer Free Shipping") }})
                                        </span>
                                    </p>
                                </div>
                                <div
                                    class="flex flex-wrap justify-between items-center gap-4"
                                >
                                    <p class="sm:text-lg text-neutral-700">
                                        {{ `${$t("Discount")} : ` }}
                                    </p>
                                    <p
                                        class="text-sm sm:text-lg text-neutral-700"
                                    >
                                        {{ `${form.coupon.value ?? 0} %` }}
                                    </p>
                                </div>
                                <div
                                    class="flex flex-wrap justify-between items-center gap-4"
                                >
                                    <p
                                        class="text-2xl font-semibold text-sky-900"
                                    >
                                        {{ `${$t("Total")} : ` }}
                                    </p>
                                    <p
                                        class="font-semibold text-2xl text-sky-900"
                                    >
                                        {{ `${total} ${$t("currency")} ` }}
                                    </p>
                                </div>
                            </div>
                            <InputError
                                class="mt-2"
                                :message="form.errors['total']"
                            />
                        </div>
                    </div>
                </div>
            </div>
            <div v-else class="text-center">
                <p class="mx-auto text-xl text-neutral-700">
                    {{
                        $t(
                            "Your Shopping cart is empty, Add Some items to be able to place an order",
                        )
                    }}
                </p>
                <div
                    class="flex flex-wrap justify-center gap-4 items-center mt-5"
                >
                    <PrimaryLink
                        href="FE.home"
                        theme="gold-primary"
                        :label="$t('Our Products')"
                    />
                    <PrimaryLink
                        href="FE.home"
                        theme="primary-white"
                        :label="$t('Our Offers')"
                    />
                </div>
            </div>
        </div>
    </section>
    <div
        v-else
        class="py-12 px-6 flex flex-col items-center justify-center gap-4"
    >
        <img
            :src="OrderPlacedImage"
            class="w-[128px]"
            alt="Order placed image"
        />
        <p class="text-lg text-primary font-semibold">{{ $t("Thank You!") }}</p>
        <p class="sm:w-1/2 text-center text-lg text-primary">
            {{ $t("Order.placedMessage") }}
        </p>
    </div>
</template>
