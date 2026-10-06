<script setup>
import { router, useForm, usePage } from "@inertiajs/vue3";
import { useCartStore } from "@/js/stores/Cart";
import { useLanguageStore } from "@/js/stores/Language";
import { useToast } from "vue-toastification";
import { getToastOptions } from "@/js/Utils/toast";
import { trans } from "laravel-vue-i18n";
import { computed, ref, watch } from "vue";
import axios from "axios";
import TextInput from "@/js/Components/TextInput.vue";
import InputError from "@/js/Components/InputError.vue";
import InputLabel from "@/js/Components/InputLabel.vue";
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
            .catch(() => { });
    }, 500);
}

watch(
    [
        () => form.phone,
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
    <main class="mx-auto max-w-screen-3xl bg-lightCream px-5 py-8 sm:px-8 md:px-12 md:py-12">
        <div v-if="!isOrderPlaced" class="mx-auto max-w-screen-xl">
            <Link :href="route('FE.shop')"
                class="mb-5 inline-flex items-center gap-2 text-xs font-medium text-brown/65 transition-colors hover:text-cOrangeDark">
            <i class="ri-arrow-left-line rtl:rotate-180" aria-hidden="true"></i>
            {{ $t("Nav.shop") }}
            </Link>

            <div class="grid items-start gap-8 lg:grid-cols-[minmax(0,1.05fr)_minmax(0,0.95fr)] lg:gap-12">
                <div class="lg:sticky lg:top-6">
                    <div class="overflow-hidden rounded-md border border-line bg-offwhite p-2 sm:p-3">
                        <img :src="currentImage?.original_url ?? wrapper.main_image_url" :alt="wrapper.title"
                            class="aspect-square w-full rounded-sm object-cover" />
                    </div>
                    <div v-if="wrapper.media?.length" class="mt-3 grid grid-cols-4 gap-2 sm:gap-3">
                        <button v-for="(image, index) in wrapper.media" :key="index" type="button"
                            :aria-label="image.file_name || wrapper.title" :aria-pressed="currentImage?.id == image.id"
                            @click="changeImage(image)"
                            class="overflow-hidden rounded-sm border bg-offwhite p-1 transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-corange"
                            :class="currentImage?.id == image.id ? 'border-corange' : 'border-line hover:border-corange/50'">
                            <img :src="image.original_url" :alt="image.file_name || wrapper.title"
                                class="aspect-square w-full rounded-sm object-cover" />
                        </button>
                    </div>
                </div>

                <div class="min-w-0">
                    <div class="mb-5">
                        <h1 class="font-serif text-3xl leading-tight tracking-tight text-brown sm:text-4xl">
                            {{ wrapper.title }}
                        </h1>
                    </div>

                    <form @submit.prevent="submitForm" class="mt-5 rounded-md border border-line bg-white p-4 sm:p-5">
                        <div class="mb-4">
                            <h2 class="font-serif text-xl text-brown">{{ $t("QuickOrder.title") }}</h2>
                            <p class="mt-1 text-xs leading-5 text-brown/65">{{ $t("QuickOrder.subtitle") }}</p>
                        </div>

                        <div class="grid gap-4 sm:grid-cols-2">
                            <div>
                                <InputLabel for="wrapper-name" :value="$t('Full Name')" class="text-brown/75" />
                                <TextInput id="wrapper-name"
                                    class="mt-1 block w-full rounded-sm border-line bg-offwhite text-sm text-brown shadow-none focus:border-corange focus:ring-corange/20"
                                    v-model="form.name" type="text" :placeholder="$t('Full Name')" />
                                <InputError class="mt-2" :message="form.errors['name']" />
                            </div>
                            <div>
                                <InputLabel for="wrapper-phone" class="text-brown/75">
                                    <span>{{ $t("Phone Number") }}</span>
                                    <span class="text-cOrangeDark">*</span>
                                </InputLabel>
                                <TextInput id="wrapper-phone"
                                    class="mt-1 block w-full rounded-sm border-line bg-offwhite text-sm text-brown shadow-none focus:border-corange focus:ring-corange/20"
                                    v-model="form.phone" required type="number"
                                    :placeholder="$t('8 digits phone number')" />
                                <InputError class="mt-2" :message="form.errors['phone']" />
                            </div>
                            <div>
                                <InputLabel for="wrapper-state" :value="$t('State')" class="text-brown/75" />
                                <select id="wrapper-state" v-model="form.state"
                                    class="mt-1 w-full rounded-sm border-line bg-offwhite px-3 py-2.5 text-sm text-brown shadow-none focus:border-corange focus:outline-none focus:ring-corange/20">
                                    <option v-for="state in states.data" :key="state.id" :value="state">{{
                                        getLocalizedStateName(state.id) }}</option>
                                </select>
                                <InputError class="mt-2" :message="form.errors['state']" />
                            </div>
                            <div>
                                <InputLabel for="wrapper-address" :value="$t('Address')" class="text-brown/75" />
                                <TextInput id="wrapper-address"
                                    class="mt-1 block w-full rounded-sm border-line bg-offwhite text-sm text-brown shadow-none focus:border-corange focus:ring-corange/20"
                                    v-model="form.address" type="text" :placeholder="$t('Full Shipping Address')" />
                                <InputError class="mt-2" :message="form.errors['address']" />
                            </div>
                            <div class="sm:col-span-2">
                                <InputLabel for="wrapper-note" :value="`${$t('Note')} (${$t('optional.f')})`"
                                    class="text-brown/75" />
                                <textarea id="wrapper-note" v-model="form.note"
                                    class="mt-1 block h-20 w-full resize-none rounded-sm border-line bg-offwhite text-sm text-brown shadow-none focus:border-corange focus:ring-corange/20"
                                    :placeholder="$t('note_placeholder')"></textarea>
                                <InputError class="mt-2" :message="form.errors['note']" />
                            </div>
                        </div>

                        <section class="mt-5 rounded-md border border-line bg-offwhite p-4 sm:p-5">
                            <h2 class="mb-3 text-xs font-semibold text-brown">
                                {{ $t("Wrapper.choose_variant") }}
                            </h2>
                            <div class="grid gap-2">
                                <button v-for="variant in variants" :key="variant.id" type="button"
                                    @click="selectVariant(variant)" :aria-pressed="selectedVariant.id === variant.id"
                                    class="flex min-h-[60px] w-full min-w-0 items-center justify-between gap-3 overflow-hidden rounded-sm border px-3 py-2.5 text-start transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-corange"
                                    :class="selectedVariant.id === variant.id ? 'border-corange bg-corange/5' : 'border-line bg-white hover:border-corange/50'">
                                    <span class="flex min-w-0 flex-1 items-center gap-2.5">
                                        <span
                                            class="flex h-5 w-5 shrink-0 items-center justify-center rounded-full border"
                                            :class="selectedVariant.id === variant.id ? 'border-corange bg-corange text-white' : 'border-brown/25 text-transparent'">
                                            <i class="ri-check-line text-xs" aria-hidden="true"></i>
                                        </span>
                                        <span class="min-w-0 flex-1">
                                            <span
                                                class="block whitespace-normal text-sm font-medium leading-snug text-brown [overflow-wrap:anywhere]">{{
                                                variant.name }}</span>
                                            <span v-if="variant.free_shipping"
                                                class="mt-0.5 block text-[10px] font-medium text-cOrangeDark">
                                                {{ $t("Free Shipping") }}
                                            </span>
                                        </span>
                                    </span>
                                    <span class="shrink-0 text-sm font-semibold text-cOrangeDark">
                                        {{ variant.price }} {{ $t("Product.currency") }}
                                    </span>
                                </button>
                            </div>

                            <div
                                class="mt-4 flex flex-wrap items-center justify-between gap-3 border-t border-line pt-4">
                                <span class="text-xs font-medium text-brown/75">{{ $t("Quantity") }}</span>
                                <div class="flex items-center gap-2">
                                    <button type="button" :aria-label="$t('Quantity') + ' -'" @click="decreaseQuantity"
                                        class="flex h-9 w-9 items-center justify-center rounded-sm border border-line bg-white text-brown transition-colors hover:border-corange hover:text-cOrangeDark focus:outline-none focus-visible:ring-2 focus-visible:ring-corange">
                                        <i class="ri-subtract-line" aria-hidden="true"></i>
                                    </button>
                                    <TextInput :required="false" v-model="totalQuantity" readonly type="text"
                                        class="h-9 w-14 rounded-sm border-line bg-white text-center text-sm font-semibold text-brown shadow-none" />
                                    <button type="button" :aria-label="$t('Quantity') + ' +'" @click="increaseQuantity"
                                        class="flex h-9 w-9 items-center justify-center rounded-sm border border-line bg-white text-brown transition-colors hover:border-corange hover:text-cOrangeDark focus:outline-none focus-visible:ring-2 focus-visible:ring-corange">
                                        <i class="ri-add-line" aria-hidden="true"></i>
                                    </button>
                                </div>
                            </div>
                        </section>

                        <div class="mt-5 rounded-sm bg-lightCream p-4">
                            <div class="flex items-center justify-between gap-3 text-xs text-brown/70">
                                <span>{{ $t("Sub Total") }}</span>
                                <span class="font-medium text-brown">{{ subtotal }} {{ $t("currency") }}</span>
                            </div>
                            <div class="mt-2 flex items-center justify-between gap-3 text-xs text-brown/70">
                                <span>
                                    {{ $t("Shipping Cost") }}
                                    <template v-if="form.state">({{ getLocalizedStateName(form.state.id) }})</template>
                                </span>
                                <span class="font-medium text-brown">{{ shippingCost }} {{ $t("currency") }}</span>
                            </div>
                            <div class="mt-3 flex items-center justify-between gap-3 border-t border-line pt-3">
                                <span class="text-sm font-semibold text-brown">{{ $t("Total") }}</span>
                                <span class="text-lg font-semibold text-cOrangeDark">{{ total }} {{ $t("currency")
                                    }}</span>
                            </div>
                            <InputError class="mt-2" :message="form.errors['total']" />
                        </div>

                        <div class="mt-4 grid gap-3 sm:grid-cols-2">
                            <button type="submit" :disabled="form.processing"
                                :class="{ 'cursor-not-allowed opacity-50': form.processing }"
                                class="inline-flex min-h-11 items-center justify-center gap-2 rounded-sm bg-corange px-4 py-3 text-sm font-semibold text-white transition-colors hover:bg-cOrangeDark focus:outline-none focus-visible:ring-2 focus-visible:ring-cOrangeDark focus-visible:ring-offset-2">
                                <i class="ri-flashlight-line" aria-hidden="true"></i>
                                {{ form.processing ? $t("Cart.loading") : $t("Place Order") }}
                            </button>
                            <button type="button" @click="handleAddToCart" :class="[
                                'inline-flex min-h-11 items-center justify-center gap-2 rounded-sm border border-corange bg-white px-4 py-3 text-sm font-semibold text-cOrangeDark transition-colors hover:bg-corange hover:text-white focus:outline-none focus-visible:ring-2 focus-visible:ring-cOrangeDark focus-visible:ring-offset-2',
                                { 'cursor-not-allowed opacity-50': loadingState },
                            ]" :disabled="loadingState">
                                <i class="ri-shopping-bag-line" aria-hidden="true"></i>
                                {{ loadingState ? $t("Cart.loading") : $t("Cart.add") }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <section v-if="displayDescription" class="mt-10 rounded-md border border-line bg-white p-5 sm:mt-14 sm:p-7">
                <h2 class="font-serif text-2xl text-brown">{{ $t("Description") }}</h2>
                <div v-html="displayDescription" class="prose mt-4 max-w-none text-brown/80" id="description-holder">
                </div>
            </section>
        </div>

        <div v-else class="mx-auto flex min-h-[60vh] max-w-screen-xl items-center justify-center py-12">
            <div
                class="flex w-full max-w-xl flex-col items-center gap-5 rounded-md border border-line bg-white px-7 py-12 text-center">
                <img :src="OrderPlacedImage" class="w-32" alt="Order placed image" />
                <p class="font-serif text-2xl text-brown">{{ $t("Thank You!") }}</p>
                <p class="text-sm leading-6 text-brown/70">{{ $t("Order.placedMessage") }}</p>
            </div>
        </div>
    </main>
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
