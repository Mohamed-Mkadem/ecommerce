<script setup>
import PageTitle from "@/js/Components/FrontEnd/PageTitle.vue";
import { useCartStore } from "@/js/stores/Cart";
import { useLanguageStore } from "@/js/stores/Language";
import emptyCart from "@/assets/images/empty-cart.png";
import PrimaryLink from "@/js/Components/PrimaryLink.vue";
const languageStore = useLanguageStore();
const cartStore = useCartStore();
</script>

<template>
    <Head :title="$t('Cart')" />
    <section class="py-12 relative">
        <div class="w-full max-w-7xl px-4 md:px-5 lg-6 mx-auto">
            <div v-if="cartStore.count">
                <PageTitle class="mb-8 text-center">{{
                    $t("Shopping cart")
                }}</PageTitle>
                <div
                    class="grid grid-cols-1 lg:grid-cols-2 min-[550px]:gap-6 border-t border-neutral-200 py-6"
                    v-for="(product, index) in cartStore.cart"
                    :key="index"
                >
                    <div
                        class="flex items-center flex-col min-[550px]:flex-row gap-3 min-[550px]:gap-6 w-full max-xl:justify-center max-xl:max-w-xl max-xl:mx-auto max-[550px]:mb-5"
                    >
                        <div class="img-box">
                            <img
                                :src="product.main_image_url"
                                :alt="product.name"
                                class="xl:w-[140px] rounded-xl object-cover"
                            />
                        </div>
                        <div class="pro-data w-full px-3">
                            <div
                                class="max-[550px]:flex max-[550px]:justify-between items-start"
                            >
                                <div>
                                    <h2
                                        class="font-semibold text-xl min-[550px]-leading-8 text-primary"
                                    >
                                        {{
                                            cartStore.getLocalizedName(
                                                product.id,
                                                languageStore.currentLocale,
                                            )
                                        }}
                                    </h2>

                                    <p
                                        class="font-medium text-lg min-[550px]-leading-8 text-sky-700"
                                    >
                                        {{
                                            `${product.price} ${$t("currency")}`
                                        }}
                                    </p>
                                </div>
                                <button
                                    @click="
                                        cartStore.removeFromCart(product.id)
                                    "
                                    class="text-sm text-red-600 underline hover:text-red-500 block"
                                >
                                    {{ $t("Remove") }}
                                </button>
                            </div>
                        </div>
                    </div>
                    <div
                        class="flex items-center flex-col min-[550px]:flex-row min-[550px]:justify-between w-full max-xl:max-w-xl max-xl:mx-auto gap-2"
                    >
                        <div
                            class="flex items-center w-max lg:mx-auto justify-center"
                        >
                            <button
                                @click="cartStore.decreaseQuantity(product.id)"
                                readonly
                                class="group rounded-s-full px-6 py-[18px] border border-neutral-200 flex items-center justify-center shadow-sm shadow-transparent transition-all duration-500 hover:shadow-neutral-200 hover:border-neutral-300 hover:bg-zinc-100"
                            >
                                <svg
                                    class="stroke-neutral-900 transition-all duration-500 group-hover:stroke-black"
                                    xmlns="http://www.w3.org/2000/svg"
                                    width="22"
                                    height="22"
                                    viewBox="0 0 22 22"
                                    fill="none"
                                >
                                    <path
                                        d="M16.5 11H5.5"
                                        stroke=""
                                        stroke-width="1.6"
                                        stroke-linecap="round"
                                    />
                                    <path
                                        d="M16.5 11H5.5"
                                        stroke=""
                                        stroke-opacity="0.2"
                                        stroke-width="1.6"
                                        stroke-linecap="round"
                                    />
                                    <path
                                        d="M16.5 11H5.5"
                                        stroke=""
                                        stroke-opacity="0.2"
                                        stroke-width="1.6"
                                        stroke-linecap="round"
                                    />
                                </svg>
                            </button>
                            <input
                                type="text"
                                class="border-y border-neutral-200 outline-none text-neutral-900 font-semibold text-lg w-full max-w-[118px] min-w-[80px] placeholder:text-neutral-900 py-[15px] text-center bg-transparent pointer-events-none"
                                placeholder="1"
                                :value="product.quantity"
                                readonly
                            />
                            <button
                                @click="cartStore.increaseQuantity(product.id)"
                                class="group rounded-e-full px-6 py-[18px] border border-neutral-200 flex items-center justify-center shadow-sm shadow-transparent transition-all duration-500 hover:shadow-neutral-200 hover:border-neutral-300 hover:bg-zinc-100"
                            >
                                <svg
                                    class="stroke-neutral-900 transition-all duration-500 group-hover:stroke-black"
                                    xmlns="http://www.w3.org/2000/svg"
                                    width="22"
                                    height="22"
                                    viewBox="0 0 22 22"
                                    fill="none"
                                >
                                    <path
                                        d="M11 5.5V16.5M16.5 11H5.5"
                                        stroke=""
                                        stroke-width="1.6"
                                        stroke-linecap="round"
                                    />
                                    <path
                                        d="M11 5.5V16.5M16.5 11H5.5"
                                        stroke=""
                                        stroke-opacity="0.2"
                                        stroke-width="1.6"
                                        stroke-linecap="round"
                                    />
                                    <path
                                        d="M11 5.5V16.5M16.5 11H5.5"
                                        stroke=""
                                        stroke-opacity="0.2"
                                        stroke-width="1.6"
                                        stroke-linecap="round"
                                    />
                                </svg>
                            </button>
                        </div>
                        <p
                            class="text-sky-800 font-manrope font-bold text-2xl leading-9 w-full max-w-[176px] text-center"
                        >
                            {{
                                `${cartStore.productSubTotal(product.id)} ${$t("currency")}`
                            }}
                        </p>
                    </div>
                </div>

                <div
                    class="bg-neutral-100 rounded-xl p-6 w-full mb-8 max-lg:max-w-xl max-lg:mx-auto"
                >
                    <div
                        class="flex flex-wrap items-center justify-between w-full py-6"
                    >
                        <div class="flex items-center gap-4">
                            <p
                                class="font-manrope font-medium text-2xl leading-9 text-neutral-900"
                            >
                                {{ `${$t("Total")} : ` }}
                            </p>
                            <p
                                class="font-manrope font-semibold text-2xl leading-9 text-sky-800"
                            >
                                {{ `${cartStore.total} ${$t("currency")}` }}
                            </p>
                        </div>
                        <PrimaryLink
                            href="FE.checkout"
                            :label="$t('Proceed to checkout')"
                            class="bg-sky-800 text-white hover:bg-opacity-80"
                        />
                    </div>
                </div>
            </div>
            <div v-else class="text-center">
                <img :src="emptyCart" alt="" class="mx-auto my-5 w-50" />
                <p class="mx-auto text-xl text-neutral-700">
                    {{ $t("Your Shopping cart is empty, Continue Shopping") }}
                </p>
                <div
                    class="flex flex-wrap justify-center gap-4 items-center mt-5"
                >
                    <PrimaryLink
                        href="FE.shop"
                        theme="gold-primary"
                        :label="$t('Our Products')"
                    />
                    <PrimaryLink
                        href="FE.packs"
                        theme="primary-white"
                        :label="$t('Our Offers')"
                    />
                </div>
            </div>
        </div>
    </section>
</template>
