<script setup>
import { useCartStore } from "@/js/stores/Cart";
import { useToast } from "vue-toastification";
import { getToastOptions } from "@/js/Utils/toast";
import { trans } from "laravel-vue-i18n";
import { ref } from "vue";

const toast = useToast();
const cartStore = useCartStore();

const props = defineProps({
    wrapper: {
        type: Object,
        required: true,
    },
});

const loading = ref(false);

function handleAddToCart() {
    if (!props.wrapper.default_product) {
        return;
    }

    loading.value = true;
    cartStore.addToCart(props.wrapper.default_product);
    toast.success(trans("Cart.added"), { ...getToastOptions(), timeout: 1000 });
    loading.value = false;
}
</script>

<template>
    <div class="flex flex-col">
        <div>
            <Link
                :href="route('FE.wrapper', wrapper.slug)"
                class="block group relative overflow-hidden rounded-lg"
            >
                <img
                    :src="wrapper.main_image_url"
                    :alt="wrapper.title"
                    class="mx-auto transition-transform duration-500 group-hover:scale-110"
                />
                <div
                    class="absolute inset-0 bg-black/40 backdrop-blur-sm flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-300"
                >
                    <span class="inline-block bg-white/95 text-sky-900 font-bold text-sm tracking-wide px-6 py-2 rounded-full shadow-lg transform translate-y-4 group-hover:translate-y-0 transition-all duration-300">{{
                        $t("More Details")
                    }}</span>
                </div>
            </Link>
        </div>
        <div class="text-center">
            <Link
                :href="route('FE.wrapper', wrapper.slug)"
                class="text-xl font-bold mt-2 text-slate-700 text-ellipsis overflow-hidden line-clamp hover:text-sky-700 transition-colors"
                :title="wrapper.title"
            >
                {{ wrapper.title }}
            </Link>
        </div>
        <div class="mt-auto">
            <div class="flex justify-center items-center gap-4 my-2">
                <span
                    v-if="
                        wrapper.discount_type == 'percentage' &&
                        wrapper.discount
                    "
                    class="bg-sky-700 font-semibold text-white px-4 text-center py-1 rounded-2xl shadow-sm"
                >
                    -{{ wrapper.discount }} %
                </span>
                <span
                    v-if="wrapper.discount_type == 'fixed' && wrapper.discount"
                    class="text-primary text-xl line-through border-dashed"
                >
                    {{ wrapper.discount }} {{ $t("currency") }}
                </span>
                <p class="font-semibold text-center text-sky-900 text-2xl">
                    {{ `${wrapper.price} ${$t("currency")}` }}
                </p>
            </div>
            <button
                @click="handleAddToCart"
                :class="[
                    'bg-gradient-to-r from-sky-700 to-sky-900 hover:from-sky-600 hover:to-sky-800 text-white shadow-md hover:shadow-lg transform hover:-translate-y-0.5 px-4 py-3 text-center block font-semibold rounded-lg w-full transition-all duration-300',
                    {
                        'opacity-50 cursor-not-allowed hover:-translate-y-0': loading,
                    },
                ]"
                :disabled="loading"
            >
                {{ loading ? $t("Cart.loading") : $t("Cart.add") }}
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
