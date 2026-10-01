<script setup>
import { useCartStore } from "@/js/stores/Cart";
import { useLanguageStore } from "@/js/stores/Language";
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
                coupon_code: form.coupon.id || null,
            })
            .catch(() => { });
    }, 500);
}

watch([() => form.phone], saveAbandonedOrder);

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

    <main class="min-h-[60vh] bg-lightCream px-5 py-10 sm:px-8 md:px-12 md:py-14" v-if="!isOrderPlaced">
        <div class="mx-auto max-w-screen-xl">
            <div v-if="cartStore.count">
                <header class="mb-8 border-b border-line pb-6 sm:mb-10 sm:pb-8">
                    <p class="mb-2 text-[10px] font-semibold uppercase tracking-[0.2em] text-cOrangeDark">{{
                        $t("Checkout") }}</p>
                    <h1 class="font-serif text-3xl leading-tight tracking-tight text-brown sm:text-4xl">{{
                        $t("Checkout") }}</h1>
                </header>

                <div class="grid items-start gap-5 lg:grid-cols-[minmax(0,1fr)_minmax(340px,0.85fr)] lg:gap-8">
                    <div class="order-last rounded-md border border-line bg-white p-4 sm:p-6 lg:order-first">
                        <h2 class="font-serif text-xl text-brown sm:text-2xl">
                            {{ $t("Shipping Information") }}
                        </h2>
                        <form @submit.prevent="submitForm">
                            <div class="mt-4">
                                <InputLabel for="full-name" :value="$t('Full Name')" class="text-brown/75" />

                                <TextInput
                                    class="mt-1 block w-full rounded-sm border-line bg-offwhite text-sm text-brown shadow-none focus:border-corange focus:ring-corange/20"
                                    id="full-name" type="text" v-model="form.name" :placeholder="$t('Full Name')" />

                                <InputError class="mt-2" :message="form.errors['name']" />
                            </div>
                            <div class="mt-4 grid gap-4 sm:grid-cols-2">
                                <div class="w-full">
                                    <InputLabel for="phone" class="text-brown/75">
                                        <span>{{ $t("Phone Number") }}</span>
                                        <span class="text-cOrangeDark">*</span>
                                    </InputLabel>

                                    <TextInput
                                        class="mt-1 block w-full rounded-sm border-line bg-offwhite text-sm text-brown shadow-none focus:border-corange focus:ring-corange/20"
                                        id="phone" v-model="form.phone" :required="true" type="number" :placeholder="$t('8 digits phone number')
                                            " />
                                    <InputError class="mt-2" :message="form.errors['phone']" />
                                </div>
                                <div class="w-full">
                                    <InputLabel for="state_id" :value="$t('State')" class="text-brown/75" />

                                    <select
                                        class="mt-1 w-full rounded-sm border-line bg-offwhite px-3 py-2.5 text-sm text-brown shadow-none focus:border-corange focus:outline-none focus:ring-corange/20"
                                        id="state_id" v-model="form.state">
                                        <option :value="state" v-for="state in props.states[
                                            'data'
                                        ]" :key="state.id">
                                            {{
                                                getLocalizedStateName(state.id)
                                            }}
                                        </option>
                                    </select>
                                    <InputError class="mt-2" :message="form.errors['state']" />
                                </div>
                            </div>
                            <div class="mt-4">
                                <InputLabel for="address" :value="$t('Address')" class="text-brown/75" />

                                <TextInput
                                    class="mt-1 block w-full rounded-sm border-line bg-offwhite text-sm text-brown shadow-none focus:border-corange focus:ring-corange/20"
                                    id="address" type="text" v-model="form.address"
                                    :placeholder="$t('Full Shipping Address')" />

                                <InputError class="mt-2" :message="form.errors['address']" />
                            </div>
                            <div class="mt-4">
                                <InputLabel for="note" :value="`${$t('Note')} (${$t('optional.f')})`"
                                    class="text-brown/75" />

                                <textarea id="note"
                                    class="mt-1 block h-24 w-full resize-none rounded-sm border-line bg-offwhite text-sm text-brown shadow-none focus:border-corange focus:ring-corange/20"
                                    v-model="form.note" :placeholder="$t('note_placeholder')">
                                </textarea>

                                <InputError class="mt-2" :message="form.errors['note']" />
                            </div>

                            <input :class="{
                                'opacity-25 cursor-not-allowed':
                                    form.processing,
                            }" :disabled="form.processing" type="Submit" :value="$t('Place Order')"
                                class="mt-4 w-full cursor-pointer rounded-sm bg-corange px-4 py-3 font-semibold text-white transition-colors hover:bg-cOrangeDark focus:outline-none focus-visible:ring-2 focus-visible:ring-cOrangeDark focus-visible:ring-offset-2" />
                        </form>
                    </div>
                    <div
                        class="order-first rounded-md border border-line bg-white p-4 sm:p-6 lg:order-last lg:sticky lg:top-6">
                        <h2 class="font-serif text-xl text-brown sm:text-2xl">
                            {{ $t("Order Summary") }}
                        </h2>
                        <ul class="mt-4 flex flex-col divide-y divide-line" :class="{
                            'max-h-96 overflow-y-auto pe-1':
                                cartStore.count > 4,
                        }">
                            <template v-for="(product, index) in cartStore.cart" :key="index">
                                <li class="py-3">
                                    <div class="flex min-w-0 gap-3">
                                        <div
                                            class="relative h-16 w-16 flex-shrink-0 overflow-hidden rounded-sm bg-offwhite sm:h-[76px] sm:w-[76px]">
                                            <img class="h-full w-full object-cover" :src="product.wrapper_main_image_url ||
                                                product.main_image_url
                                                " :alt="product.name" />
                                            <span
                                                class="absolute bottom-1 end-1 rounded-sm bg-brown/90 px-1.5 py-0.5 text-[10px] font-semibold text-white">×
                                                {{ product.quantity }}</span>
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <h3 class="line-clamp-2 font-serif text-sm leading-snug text-brown">
                                                {{
                                                    cartStore.getLocalizedName(
                                                        product.id,
                                                        languageStore.currentLocale,
                                                    )
                                                }}
                                            </h3>
                                            <p class="mt-1 text-xs font-semibold text-cOrangeDark">
                                                {{
                                                    product.price +
                                                    ` ${$t("Product.currency")}`
                                                }}
                                            </p>
                                        </div>
                                    </div>
                                    <InputError class="mt-1" :message="form.errors[`cart.${index}.id`]
                                        " />
                                    <InputError class="mt-1" :message="form.errors[
                                        `cart.${index}.quantity`
                                    ]
                                        " />
                                    <InputError class="mt-1" :message="form.errors[`cart.${index}.price`]
                                        " />
                                </li>
                            </template>
                        </ul>

                        <div>
                            <InputError class="mt-2" :message="form.errors['cart']" />
                        </div>

                        <div>
                            <div class="mb-8">
                                <form @submit.prevent="handleCheckCouponCode" class="mt-4 flex-1">
                                    <InputLabel for="code" :value="$t('CouponCode.label')" class="text-brown/75" />
                                    <div class="grid grid-cols-[minmax(0,1fr)_auto] items-center gap-2">
                                        <TextInput
                                            class="mt-1 block w-full rounded-sm border-line bg-offwhite text-sm text-brown shadow-none focus:border-corange focus:ring-corange/20"
                                            id="code" type="text" v-model="codeModel" :required="true"
                                            :placeholder="$t('Coupon Code')" />
                                        <button type="submit"
                                            class="mt-1 inline-flex h-10 items-center justify-center rounded-sm bg-brown px-4 text-xs font-semibold text-white transition-colors hover:bg-cOrangeDark focus:outline-none focus-visible:ring-2 focus-visible:ring-cOrangeDark"
                                            :class="{
                                                'cursor-not-allowed opacity-50':
                                                    couponCodeIsProcessing,
                                            }" :disabled="couponCodeIsProcessing">
                                            {{ $t("Apply") }}
                                        </button>
                                    </div>
                                </form>
                                <InputError class="mt-2" :message="form.errors['coupon']" />
                                <InputError class="mt-2" :message="form.errors['coupon.id']" />
                                <div v-if="form.coupon.code"
                                    class="mt-3 flex flex-wrap items-center gap-3 rounded-sm bg-lightCream px-3 py-2 text-xs">
                                    <p>
                                        {{
                                            `'${form.coupon.code}' ${$t("applied")}`
                                        }}
                                    </p>
                                    <button type="button"
                                        class="font-medium text-cOrangeDark underline hover:text-brown"
                                        @click="form.coupon = {}">
                                        {{ $t("Remove") }}
                                    </button>
                                </div>
                            </div>

                            <div class="rounded-sm bg-lightCream p-4">
                                <div class="flex flex-wrap justify-between items-center gap-4 mb-1">
                                    <p class="text-xs text-brown/70">
                                        {{ $t("Sub Total") }}
                                    </p>
                                    <p class="text-xs font-medium text-brown">
                                        {{
                                            `${cartStore.total} ${$t("currency")}`
                                        }}
                                    </p>
                                </div>
                                <div class="flex flex-wrap justify-between items-center gap-4 mb-1">
                                    <p class="text-xs text-brown/70">
                                        {{
                                            `${$t("Shipping Cost")} (${getLocalizedStateName(form.state["id"])})`
                                        }}
                                    </p>
                                    <p class="text-xs font-medium text-brown">
                                        {{
                                            `${shippingCost} ${$t("currency")}`
                                        }}
                                        <span v-if="hasAnyFreeShipping"
                                            class="inline-block text-[10px] font-normal text-cOrangeDark ">
                                            ({{ $t("Offer Free Shipping") }})
                                        </span>
                                    </p>
                                </div>
                                <div class="flex flex-wrap justify-between items-center gap-4 mb-1">
                                    <p class="text-xs text-brown/70">
                                        {{ $t("Discount") }}
                                    </p>
                                    <p class="text-xs font-medium text-brown">
                                        {{ `${form.coupon.value ?? 0} %` }}
                                    </p>
                                </div>
                                <div
                                    class="mt-3 flex flex-wrap items-center justify-between gap-4 border-t border-line pt-3">
                                    <p class="text-sm font-semibold text-brown">
                                        {{ `${$t("Total")} : ` }}
                                    </p>
                                    <p class="text-xl font-semibold text-cOrangeDark">
                                        {{ `${total} ${$t("currency")} ` }}
                                    </p>
                                </div>
                            </div>
                            <InputError class="mt-2" :message="form.errors['total']" />
                        </div>
                    </div>
                </div>
            </div>
            <div v-else
                class="mx-auto flex max-w-xl flex-col items-center rounded-md border border-line bg-white px-6 py-12 text-center sm:py-16">
                <i class="ri-shopping-bag-3-line text-4xl text-corange" aria-hidden="true"></i>
                <p class="mx-auto mt-4 max-w-md font-serif text-xl text-brown sm:text-2xl">
                    {{
                        $t(
                            "Your Shopping cart is empty, Add Some items to be able to place an order",
                        )
                    }}
                </p>
                <div class="mt-6 flex flex-wrap items-center justify-center gap-3">
                    <Link :href="route('FE.shop')"
                        class="inline-flex min-h-11 items-center gap-2 rounded-sm bg-corange px-5 py-3 text-sm font-semibold text-white transition-colors hover:bg-cOrangeDark">
                        {{ $t("Our Products") }}
                    </Link>
                </div>
            </div>
        </div>
    </main>
    <div v-else
        class="flex min-h-[60vh] flex-col items-center justify-center gap-4 bg-lightCream px-6 py-12 text-center">
        <div class="flex w-full max-w-xl flex-col items-center gap-5 rounded-md border border-line bg-white px-7 py-12">
            <img :src="OrderPlacedImage" class="w-32" alt="Order placed image" />
            <p class="font-serif text-2xl text-brown">{{ $t("Thank You!") }}</p>
            <p class="text-sm leading-6 text-brown/70">{{ $t("Order.placedMessage") }}</p>
        </div>
    </div>
</template>
