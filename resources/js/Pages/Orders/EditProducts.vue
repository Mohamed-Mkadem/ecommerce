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
const toast = useToast();
const languageStore = useLanguageStore();
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
    let state = props.states.data.find((item) => item.id == state_id);
    return state.shipping_cost;
};
const props = defineProps({
    products: {
        type: Array,
        required: true,
    },
    order: {
        type: Object,
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
    cart: null,
    total: 0,
    order_id: props.order.id,
});
const resetForm = () => {
    form.cart = null;
    form.total = 0;
    form.order_id = props.order.id;
};

const total = computed(() => {
    return cartStore.total;
});

function submitForm() {
    form.total = total.value;
    form.cart = cartStore.cart;
    form.post(route("orders.updateProducts"), {
        preserveScroll: true,
        onSuccess: () => {
            let message = trans("Order.updated_successfully");
            toast.success(message, getToastOptions());
            resetForm();
            cartStore.clearCart();
        },
    });
}
</script>

<template>
    <Head :title="$t('Edit Order')" />
    <PageHeader :page-title="$t('Edit Order')" />

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

    <div>
        <div class="bg-white mt-8 px-4 py-4 rounded-md">
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
        <InputError class="mt-4" :message="form.errors['cart']" />
        <form @submit.prevent="submitForm">
            <input
                :class="{
                    'opacity-25 cursor-not-allowed': form.processing,
                }"
                :disabled="form.processing"
                type="Submit"
                :value="$t('Update Order')"
                class="max-w-max cursor-pointer rounded-lg border border-primary bg-primary py-2 px-4 font-medium text-white transition hover:bg-opacity-90 mt-4"
            />
        </form>
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
