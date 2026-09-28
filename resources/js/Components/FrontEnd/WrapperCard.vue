<script setup>
import { ref } from "vue";
import { useCartStore } from "@/js/stores/Cart";
import { useToast } from "vue-toastification";
import { getToastOptions } from "@/js/Utils/toast";
import { trans } from "laravel-vue-i18n";

const cartStore = useCartStore();
const toast = useToast();
const loading = ref(false);

const props = defineProps({
    wrapper: {
        type: Object,
        required: true,
    },
});

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
    <article class="group min-w-0">
        <Link
            :href="route('FE.wrapper', wrapper.slug)"
            class="relative block aspect-square overflow-hidden bg-[#f3eee8]"
            :aria-label="`${$t('bestsellers.explore')} ${wrapper.title}`"
        >
            <img
                :src="wrapper.main_image_url"
                :alt="wrapper.title"
                loading="lazy"
                class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-[1.03]"
            />
        </Link>

        <div class="pt-3">
            <Link
                :href="route('FE.wrapper', wrapper.slug)"
                class="block truncate font-serif text-lg leading-tight text-brown transition-colors group-hover:text-cOrangeDark sm:text-xl"
                :title="wrapper.title"
            >
                {{ wrapper.title }}
            </Link>

            <div class="mt-2 flex flex-wrap items-center gap-x-2 text-sm">
                <span class="font-semibold text-cOrangeDark">
                    {{ wrapper.price }} {{ $t("Product.currency") }}
                </span>
            </div>

            <button
                type="button"
                @click="handleAddToCart"
                :class="[
                    'mt-3 inline-flex min-h-10 w-full items-center justify-center gap-2 rounded-sm bg-corange px-4 py-2.5 text-xs font-semibold text-white transition-all duration-300 hover:-translate-y-0.5 hover:bg-cOrangeDark focus:outline-none focus-visible:ring-2 focus-visible:ring-cOrangeDark focus-visible:ring-offset-2',
                    {
                        'cursor-not-allowed opacity-50 hover:translate-y-0': loading,
                    },
                ]"
                :disabled="loading || !wrapper.default_product"
            >
                {{ loading ? $t("Cart.loading") : $t("Cart.add") }}
            </button>
        </div>
    </article>
</template>
