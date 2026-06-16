<script setup>
import { ref, computed } from "vue";
import { onClickOutside } from "@vueuse/core";
import { useCartStore } from "@/js/stores/Cart";
import { useLanguageStore } from "@/js/stores/Language";
import { router } from "@inertiajs/vue3";
const languageStore = useLanguageStore();
const cartStore = useCartStore();
const target = ref(null);
const dropdownOpen = ref(false);

onClickOutside(target, () => {
    dropdownOpen.value = false;
});

function toggleDropdown() {
    dropdownOpen.value = !dropdownOpen.value;
}

function handleLink(href) {
    router.get(route(href));
    dropdownOpen.value = false;
}
</script>

<template>
    <div class="relative" ref="target">
        <button
            class="relative flex h-8.5 w-8.5 items-center justify-center rounded-full cursor-pointer text-slate-700 hover:text-sky-800 transition-colors duration-300"
            @click="toggleDropdown"
        >
            <span
                class="absolute -top-3 -left-1.5 z-1 h-6 w-6 text-white rounded-full bg-meta-5 flex justify-center items-center"
            >
                {{ cartStore.count }}
            </span>

            <i class="ri-shopping-bag-line text-3xl sm:block"></i>
        </button>

        <!-- Dropdown Start -->
        <div
            v-show="dropdownOpen"
            class="absolute w-[95vw] ltr:right-[-33vw] ltr:min-[375px]:right-[-28vw] rtl:left-[-32vw] rtl:min-[375px]:left-[-28vw] rtl:min-[575px]:left-[-15vw] rtl:xsm:left-[-20vw] xsm:w-[80vw] xsm:ltr:right-[-15vw] mt-2.5 flex flex-col rounded-xl bg-slate-800 shadow-default z-9999 sm:w-80 sm:ltr:right-0 sm:rtl:left-0"
            :class="{
                'py-4 px-5': !cartStore.count,
            }"
        >
            <div v-if="cartStore.count">
                <div class="px-4.5 py-3">
                    <h5 class="text-xl font-medium text-gray">
                        {{ `${$t("Cart")} : ${cartStore.count}` }}
                    </h5>
                </div>

                <ul class="flex h-50 flex-col overflow-y-auto">
                    <template
                        v-for="(product, index) in cartStore.cart"
                        :key="index"
                    >
                        <li class="p-3 flex gap-3">
                            <div class="relative w-[80px] flex-shrink-0">
                                <img
                                    class="w-full"
                                    :src="product.main_image_url"
                                    :alt="product.name"
                                />
                                <div
                                    class="absolute bottom-0 left-0 bg-gray-800 text-white text-xs font-bold flex items-center justify-center w-10 h-7 rounded-sm bg-sky-800"
                                >
                                    x {{ product.quantity }}
                                </div>
                            </div>
                            <div>
                                <Link
                                    class="text-gray hover:text-slate-200 text-ellipsis overflow-hidden line-clamp"
                                >
                                    {{
                                        cartStore.getLocalizedName(
                                            product.id,
                                            languageStore.currentLocale,
                                        )
                                    }}
                                </Link>
                                <p class="text-xl font-bold text-gray">
                                    {{
                                        product.price +
                                        ` ${$t("Product.currency")}`
                                    }}
                                </p>
                            </div>
                            <button
                                @click="cartStore.removeFromCart(product.id)"
                                class="bg-red-600 text-white rounded-sm w-[20px] text-sm ms-auto self-start flex-shrink-0"
                            >
                                <i class="ri-delete-bin-fill"></i>
                            </button>
                        </li>
                    </template>
                </ul>
                <div class="px-4 py-3 border-t border-graydark">
                    <!-- Total Section -->
                    <div class="flex justify-between items-center mb-3">
                        <span class="text-gray font-medium">{{
                            $t("Total")
                        }}</span>
                        <span class="text-lg font-bold text-gray">{{
                            cartStore.total + ` ${$t("Product.currency")}`
                        }}</span>
                    </div>

                    <!-- Links Section -->
                    <div class="flex gap-2">
                        <button
                            @click="handleLink('FE.cart')"
                            class="flex-1 text-center py-2 font-medium rounded-lg transition bg-body text-gray hover:bg-opacity-90"
                        >
                            {{ $t("Cart") }}
                        </button>
                        <button
                            @click="handleLink('FE.checkout')"
                            class="flex-1 text-center py-2 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition"
                        >
                            {{ $t("Checkout") }}
                        </button>
                    </div>
                </div>
            </div>
            <p v-else class="text-neutral-200 text-center">
                {{ $t("The cart is empty") }}
            </p>
        </div>
        <!-- Dropdown End -->
    </div>
</template>

<style scoped>
.line-clamp {
    display: -webkit-box; /* Enable multi-line truncation */
    -webkit-line-clamp: 2; /* Set the maximum number of lines */
    line-clamp: 2; /* Set the maximum number of lines */
    -webkit-box-orient: vertical; /* Required for -webkit-line-clamp to work */
    overflow: hidden; /* Hide overflowed content */
    text-overflow: ellipsis; /* Add ellipsis for overflowed text */
}
</style>
