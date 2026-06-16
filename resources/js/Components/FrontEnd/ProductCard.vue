<script setup>
import { useCartStore } from "@/js/stores/Cart";
import { useToast } from "vue-toastification";
import { getToastOptions } from "@/js/Utils/toast";
import { trans } from "laravel-vue-i18n";
import { ref } from "vue";

const toast = useToast();
const cartStore = useCartStore();
const props = defineProps({
    product: {
        type: Object,
        required: true,
    },
    withRate: {
        type: Boolean,
        default: false,
    },
});
const loadingState = ref({});

function handleAddToCart(product) {
    loadingState.value[product.id] = true;

    cartStore.addToCart(product);
    toast.success(trans("Cart.added"), { ...getToastOptions(), timeout: 1000 });

    loadingState.value[product.id] = false;
}
</script>

<template>
    <div class="flex flex-col">
        <div>
            <Link
                :href="route('products.show', product)"
                class="block group relative overflow-hidden rounded-lg"
            >
                <img
                    :src="product.main_image_url"
                    :alt="product.name"
                    class="mx-auto transition-all duration-300"
                />
                <!-- Hover overlay -->
                <div
                    class="absolute inset-0 bg-sky-900 bg-opacity-80 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-300"
                >
                    <span class="text-white font-semibold text-lg">{{
                        $t("More Details")
                    }}</span>
                </div>
            </Link>
        </div>
        <div class="text-center">
            <Link
                :href="route('products.show', product)"
                class="text-xl font-bold mt-2 text-slate-700 text-ellipsis overflow-hidden line-clamp hover:text-sky-700 hover:underline transition-colors"
                :title="product.name"
            >
                {{ product.name }}
            </Link>
        </div>
        <div class="mt-auto">
            <div class="flex justify-center items-center gap-4 my-2">
                <span
                    v-if="
                        product.discount_type == 'percentage' &&
                        product.discount
                    "
                    class="bg-sky-900 font-semibold text-white px-4 text-center py-1 rounded-2xl"
                >
                    -{{ product.discount }} %
                </span>
                <span
                    v-if="product.discount_type == 'fixed' && product.discount"
                    class="text-primary text-xl line-through border-dashed"
                >
                    {{ product.discount }} {{ $t("currency") }}
                </span>
                <p class="font-semibold text-center text-meta-5 text-xl">
                    {{ `${product.price} ${$t("currency")}` }}
                </p>
            </div>
            <!-- <p
                v-if="withRate"
                class="font-semibold text-center text-meta-5 mb-3 text-xl"
            >
                <i class="ri-star-line text-gold"></i>
                {{
                    product.rate !== null && product.rate !== undefined
                        ? product.rate
                        : $t("N/A")
                }}
            </p> -->
            <button
                @click="handleAddToCart(product)"
                :class="[
                    'bg-sky-900 text-white hover:bg-opacity-90 px-4 py-3 text-center block font-semibold rounded w-full',
                    {
                        'opacity-50 cursor-not-allowed':
                            loadingState[product.id],
                    },
                ]"
                :disabled="loadingState[product.id]"
            >
                {{
                    loadingState[product.id]
                        ? $t("Cart.loading")
                        : $t("Cart.add")
                }}
            </button>
        </div>
    </div>
</template>

<style scoped>
.line-clamp {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    text-overflow: ellipsis;
}
</style>
