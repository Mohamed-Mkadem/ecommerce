<script setup>
import PageTitle from "@/js/Components/FrontEnd/PageTitle.vue";
import { router, useForm, usePage } from "@inertiajs/vue3";
import { useCartStore } from "@/js/stores/Cart";
import { useLanguageStore } from "@/js/stores/Language";
import { useToast } from "vue-toastification";
import { getToastOptions } from "@/js/Utils/toast";
import { trans } from "laravel-vue-i18n";
import { computed, ref, watch } from "vue";
import axios from "axios";
import NotFound from "@/js/Components/NotFound.vue";
import TextInput from "@/js/Components/TextInput.vue";
import InputError from "@/js/Components/InputError.vue";
import InputLabel from "@/js/Components/InputLabel.vue";
import Paginator from "@/js/Components/Paginator.vue";
import OrderPlacedImage from "@/assets/images/done.png";
import { trackFacebookEvent } from "@/js/Utils/facebook";

const props = defineProps({
    wrapper: {
        type: Object,
        required: true,
    },
    variants: {
        type: Array,
        required: true,
    },
    selected_product_id: {
        type: Number,
        required: true,
    },
    states: {
        type: Object,
        required: true,
    },
});

const toast = useToast();
const page = usePage();
const languageStore = useLanguageStore();
const cartStore = useCartStore();
const loadingState = ref(false);
const isOrderPlaced = ref(false);
const abandonedTimer = ref(null);

function findVariant(id) {
    return props.variants.find((v) => v.id === id) ?? props.variants[0];
}

const selectedVariant = ref(findVariant(props.selected_product_id));

const totalQuantity = ref(1);

const currentImage = ref(
    props.wrapper.media?.[0] ?? {
        original_url: props.wrapper.main_image_url,
    },
);

const form = useForm({
    name: "",
    state: props.states.data[0],
    address: "",
    phone: "",
    note: "",
    cart: null,
    coupon: {},
    total: 0,
    free_shipping: false,
});

function selectVariant(variant) {
    if (selectedVariant.value.id === variant.id) {
        return;
    }

    selectedVariant.value = variant;
    totalQuantity.value = 1;

    router.get(
        route("FE.wrapper", props.wrapper.slug),
        { variant: variant.id },
        {
            preserveState: true,
            preserveScroll: true,
            replace: true,
            only: ["selected_product_id"],
        },
    );
}

function changeImage(image) {
    currentImage.value = image;
}

const increaseQuantity = () => {
    const step = Number(selectedVariant.value.update_quantity ?? 1);
    totalQuantity.value = totalQuantity.value + step;
};

const decreaseQuantity = () => {
    const step = Number(selectedVariant.value.update_quantity ?? 1);
    const min = step;
    if (totalQuantity.value > min) {
        totalQuantity.value = totalQuantity.value - step;
    }
};

const getLocalizedStateName = (state_id) => {
    const state = props.states.data.find((item) => item.id == state_id);
    const translation = state?.translations?.find(
        (t) => t.locale === languageStore.currentLocale,
    );
    return translation?.name ?? state?.name ?? "";
};

const subtotal = computed(() => {
    const price = parseFloat(selectedVariant.value.price) || 0;
    const qty = Number(totalQuantity.value) || 1;
    return (price * qty).toFixed(3);
});

const shippingCost = computed(() => {
    if (selectedVariant.value.free_shipping) {
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
    () => selectedVariant.value.free_shipping,
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
                cart: [
                    {
                        ...selectedVariant.value,
                        quantity: Number(totalQuantity.value) || 1,
                    },
                ],
                total: Number(total.value) || 0,
                free_shipping: !!selectedVariant.value.free_shipping,
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
        () => totalQuantity.value,
    ],
    saveAbandonedOrder,
);

function handleAddToCart() {
    loadingState.value = true;
    cartStore.addToCart(selectedVariant.value, totalQuantity.value);
    toast.success(trans("Cart.added"), { ...getToastOptions(), timeout: 1000 });
    loadingState.value = false;
    totalQuantity.value = 1;
}

function resetForm() {
    form.name = "";
    form.state = props.states.data[0];
    form.address = "";
    form.phone = "";
    form.note = "";
    form.cart = null;
    form.coupon = {};
    form.total = 0;
    form.free_shipping = !!selectedVariant.value.free_shipping;
}

function submitForm() {
    form.total = total.value;
    form.cart = [
        {
            ...selectedVariant.value,
            quantity: Number(totalQuantity.value) || 1,
        },
    ];
    form.free_shipping = !!selectedVariant.value.free_shipping;

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
                    content_ids: [selectedVariant.value.id?.toString()],
                    content_type: "product",
                    currency: "TND",
                    value: Number(total.value),
                    num_items: Number(totalQuantity.value) || 1,
                },
                { eventID: `purchase-${Date.now()}` },
            );
        },
    });
}

const displayDescription = computed(
    () => props.wrapper.description || selectedVariant.value.description,
);
</script>

<template>
    <Head :title="wrapper.title" />
    <div class="py-12 px-4 md:px-6 lg:px-12 max-w-screen-xl mx-auto">
        <div v-if="!isOrderPlaced">
            <div
                class="md:grid md:grid-cols-1 lg:grid-cols-2 md:gap-8 items-start relative py-8"
            >
                <!-- Left Column: Images -->
                <div
                    class="lg:sticky lg:top-8 ltr:lg:left-0 rtl:lg:right-0 lg:overflow-visible lg:max-h-screen"
                >
                    <div>
                        <img
                            :src="
                                currentImage?.original_url ??
                                wrapper.main_image_url
                            "
                            :alt="wrapper.title"
                            class="rounded-2xl mx-auto shadow-lg object-contain"
                        />
                    </div>
                    <div
                        v-if="wrapper.media?.length"
                        class="grid grid-cols-[repeat(4,_minmax(50px,_100px))] justify-between gap-2 md:gap-4 mt-4"
                    >
                        <div
                            v-for="(image, index) in wrapper.media"
                            :key="index"
                            class="rounded-lg transition-all"
                            :class="{
                                'ring-2 ring-primary ring-offset-2':
                                    currentImage?.id == image.id,
                            }"
                        >
                            <img
                                @click="changeImage(image)"
                                :src="image.original_url"
                                :alt="image.file_name"
                                class="w-full rounded-md cursor-pointer"
                            />
                        </div>
                    </div>
                </div>

                <!-- Right Column: Product Info and Form -->
                <div class="mt-8 lg:mt-0">
                    <!-- Title -->
                    <h1
                        class="text-3xl text-primary font-bold mb-4 tracking-tight"
                    >
                        {{ wrapper.title }}
                    </h1>

                    <div
                        class="rounded-xl bg-gradient-to-r from-sky-800 to-sky-900 text-white px-4 py-3 mb-6 shadow-md border-l-4 border-sky-400"
                    >
                        <p v-if="wrapper.caption" class="text-sm font-bold">
                            {{ wrapper.caption }}
                        </p>
                    </div>

                    <!-- Form Section -->
                    <form @submit.prevent="submitForm" class="space-y-4">
                        <!-- Full Name -->
                        <div>
                            <InputLabel
                                for="wrapper-name"
                                :value="$t('Full Name')"
                                class="text-neutral-500"
                            />
                            <TextInput
                                id="wrapper-name"
                                class="mt-1 block w-full"
                                v-model="form.name"
                                type="text"
                                :placeholder="$t('Full Name')"
                            />
                            <InputError
                                class="mt-2"
                                :message="form.errors['name']"
                            />
                        </div>

                        <!-- Phone Number -->
                        <div>
                            <InputLabel
                                for="wrapper-phone"
                                class="text-neutral-500"
                            >
                                <span>{{ $t("Phone Number") }}</span>
                                <span class="text-red-500">*</span>
                            </InputLabel>
                            <TextInput
                                id="wrapper-phone"
                                class="mt-1 block w-full"
                                v-model="form.phone"
                                required
                                type="number"
                                :placeholder="$t('8 digits phone number')"
                            />
                            <InputError
                                class="mt-2"
                                :message="form.errors['phone']"
                            />
                        </div>

                        <!-- State -->
                        <div>
                            <InputLabel
                                for="wrapper-state"
                                :value="$t('State')"
                                class="text-neutral-500"
                            />
                            <select
                                id="wrapper-state"
                                v-model="form.state"
                                class="w-full mt-1 rounded-lg shadow-sm focus:ring-2 focus:ring-primary focus:border-primary border-editor text-primary"
                            >
                                <option
                                    v-for="state in states.data"
                                    :key="state.id"
                                    :value="state"
                                >
                                    {{ getLocalizedStateName(state.id) }}
                                </option>
                            </select>
                            <InputError
                                class="mt-2"
                                :message="form.errors['state']"
                            />
                        </div>

                        <!-- Address -->
                        <div>
                            <InputLabel
                                for="wrapper-address"
                                :value="$t('Address')"
                                class="text-neutral-500"
                            />
                            <TextInput
                                id="wrapper-address"
                                class="mt-1 block w-full"
                                v-model="form.address"
                                type="text"
                                :placeholder="$t('Full Shipping Address')"
                            />
                            <InputError
                                class="mt-2"
                                :message="form.errors['address']"
                            />
                        </div>

                        <!-- Note -->
                        <div>
                            <InputLabel
                                for="wrapper-note"
                                :value="`${$t('Note')} (${$t('optional.f')})`"
                                class="text-neutral-500"
                            />
                            <textarea
                                id="wrapper-note"
                                v-model="form.note"
                                class="mt-1 block w-full text-primary rounded-lg border-editor shadow-sm focus:border-primary focus:ring-2 focus:ring-primary h-20 resize-none"
                                :placeholder="$t('note_placeholder')"
                            ></textarea>
                            <InputError
                                class="mt-2"
                                :message="form.errors['note']"
                            />
                        </div>

                        <!-- Variants Selection -->
                        <div class="mb-4">
                            <p
                                class="text-sm font-semibold text-slate-600 mb-3"
                            >
                                {{ $t("Wrapper.choose_variant") }}
                            </p>
                            <div class="space-y-2">
                                <button
                                    v-for="variant in variants"
                                    :key="variant.id"
                                    type="button"
                                    @click="selectVariant(variant)"
                                    class="w-full flex justify-between items-center gap-4 p-3 rounded-xl border-2 transition-all"
                                    :class="{
                                        'border-sky-800 bg-gradient-to-r from-sky-50 to-sky-100 shadow-md':
                                            selectedVariant.id === variant.id,
                                        'border-neutral-200 hover:border-sky-400 hover:shadow-sm':
                                            selectedVariant.id !== variant.id,
                                    }"
                                >
                                 <!-- Selected Badge -->
                                    <div
                                        v-if="selectedVariant.id === variant.id"
                                        class="flex-shrink-0 w-7 h-7 rounded-full bg-sky-900 flex items-center justify-center"
                                    >
                                        <i
                                            class="ri-check-line text-base text-white"
                                        ></i>
                                    </div>
                                    <!-- Variant Name and Price -->
                                    <div class="flex-1 text-end">
                                        <p
                                            class="font-semibold text-neutral-800"
                                            :class="{
                                                'text-sky-900':
                                                    selectedVariant.id ===
                                                    variant.id,
                                            }"
                                        >
                                            {{ variant.name }}
                                        </p>
                                        <p
                                            class="text-sm text-neutral-600"
                                            :class="{
                                                'text-sky-800':
                                                    selectedVariant.id ===
                                                    variant.id,
                                            }"
                                        >
                                            {{
                                                variant.price +
                                                ` ${$t("Product.currency")}`
                                            }}
                                        </p>
                                    </div>
                                   
                                  
                                   
                                </button>
                            </div>
                        </div>

                        <!-- Quantity Selector -->
                        <div
                            class="flex items-center gap-3 bg-white border border-neutral-200 shadow-sm p-3 rounded-xl"
                        >
                            <p class="text-sm font-semibold text-neutral-600">
                                {{ $t("Quantity") }}:
                            </p>
                            <button
                                type="button"
                                class="bg-neutral-100 border border-neutral-200 min-w-10 h-10 text-primary font-bold hover:bg-neutral-200 rounded-lg transition-colors"
                                @click="decreaseQuantity"
                            >
                                -
                            </button>

                            <TextInput
                                :required="false"
                                v-model="totalQuantity"
                                readonly
                                type="text"
                                class="h-10 text-center border border-neutral-300 font-semibold min-w-[50px] rounded"
                            />

                            <button
                                type="button"
                                class="bg-neutral-100 border border-neutral-200 min-w-10 h-10 text-primary font-bold hover:bg-neutral-200 rounded-lg transition-colors"
                                @click="increaseQuantity"
                            >
                                +
                            </button>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex gap-3">
                            <button
                                type="submit"
                                :disabled="form.processing"
                                :class="{
                                    'opacity-50 cursor-not-allowed':
                                        form.processing,
                                }"
                                class="flex-1 cursor-pointer rounded-xl border border-primary bg-primary py-3.5 px-4 font-semibold text-white shadow-md transition-all hover:shadow-lg hover:bg-opacity-90 active:scale-95"
                            >
                                {{
                                    form.processing
                                        ? $t("Cart.loading")
                                        : $t("Place Order")
                                }}
                            </button>
                            <button
                                type="button"
                                @click="handleAddToCart"
                                :class="[
                                    'flex-1 rounded-xl border-2 border-primary bg-white py-3.5 px-4 font-semibold text-primary shadow-sm transition-all hover:bg-primary hover:text-white hover:shadow-md active:scale-95',
                                    {
                                        'opacity-50 cursor-not-allowed':
                                            loadingState,
                                    },
                                ]"
                                :disabled="loadingState"
                            >
                                {{
                                    loadingState
                                        ? $t("Cart.loading")
                                        : $t("Cart.add")
                                }}
                            </button>
                        </div>
                    </form>

                    <!-- Total Calculator -->
                    <div
                        class="mt-6 bg-white px-5 py-5 rounded-2xl border border-neutral-200 shadow-lg"
                    >
                        <div
                            class="flex justify-between items-center gap-2 mb-3"
                        >
                            <p class="text-neutral-600">
                                {{ $t("Sub Total") }}:
                            </p>
                            <p class="text-neutral-700 font-semibold">
                                {{ subtotal }}
                                {{ $t("currency") }}
                            </p>
                        </div>
                        <div
                            class="flex justify-between items-center gap-2 mb-3"
                        >
                            <p class="text-neutral-600">
                                {{ $t("Shipping Cost") }}
                                <template v-if="form.state">
                                    ({{
                                        getLocalizedStateName(form.state.id)
                                    }}) </template
                                >:
                            </p>
                            <p class="text-neutral-700 font-semibold">
                                {{ shippingCost }}
                                {{ $t("currency") }}
                            </p>
                        </div>
                        <div
                            class="flex justify-between items-center gap-2 pt-3 border-t-2 border-neutral-200"
                        >
                            <p class="text-lg font-bold text-sky-900">
                                {{ $t("Total") }}:
                            </p>
                            <p class="text-lg font-bold text-sky-900">
                                {{ total }} {{ $t("currency") }}
                            </p>
                        </div>
                        <InputError
                            class="mt-2"
                            :message="form.errors['total']"
                        />
                    </div>
                </div>
            </div>

            <!-- Description -->
            <div v-if="displayDescription" class="mt-14">
                <h2 class="text-2xl text-primary font-bold mb-6">
                    {{ $t("Description") }}
                </h2>
                <div
                    v-html="displayDescription"
                    class="text-neutral-700 mt-6"
                    id="description-holder"
                ></div>
            </div>
        </div>

        <!-- Order Placed Success Screen -->
        <div v-else class="py-16 px-6 flex items-center justify-center">
            <div
                class="flex flex-col items-center gap-6 text-center bg-white rounded-2xl shadow-xl border border-neutral-100 max-w-md w-full py-14 px-8"
            >
                <img
                    :src="OrderPlacedImage"
                    class="w-36 drop-shadow-md"
                    alt="Order placed image"
                />
                <p class="text-2xl text-primary font-bold tracking-tight">
                    {{ $t("Thank You!") }}
                </p>
                <p class="text-neutral-600 text-base leading-relaxed">
                    {{ $t("Order.placedMessage") }}
                </p>
            </div>
        </div>
    </div>
</template>

<style scoped>
#description-holder h3 {
    font-weight: 600;
    margin-bottom: 10px;
}

#description-holder ul,
#description-holder ol {
    margin-block: 16px;
    list-style: inside disc;
}

#description-holder p {
    margin-block: 10px;
}

#description-holder ol li p,
#description-holder ul li p {
    display: inline;
}
</style>
