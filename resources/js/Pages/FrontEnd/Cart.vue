<script setup>
import { useCartStore } from "@/js/stores/Cart";
import { useLanguageStore } from "@/js/stores/Language";
import emptyCart from "@/assets/images/empty-cart.png";
const languageStore = useLanguageStore();
const cartStore = useCartStore();
</script>

<template>

    <Head :title="$t('Cart')" />
    <main class="min-h-[60vh] bg-lightCream px-5 py-10 sm:px-8 md:px-12 md:py-14">
        <div class="mx-auto max-w-screen-xl">
            <template v-if="cartStore.count">
                <header class="mb-8 border-b border-line pb-6 sm:mb-10 sm:pb-8">
                    <p class="mb-2 text-[10px] font-semibold uppercase tracking-[0.2em] text-cOrangeDark">
                        {{ $t("Cart") }}
                    </p>
                    <h1 class="font-serif text-3xl leading-tight tracking-tight text-brown sm:text-4xl">
                        {{ $t("Shopping cart") }}
                    </h1>
                </header>

                <div class="grid items-start gap-6 lg:grid-cols-[minmax(0,1fr)_320px] lg:gap-8">
                    <div class="space-y-3">
                        <article v-for="(product, index) in cartStore.cart" :key="index"
                            class="grid grid-cols-[72px_minmax(0,1fr)] items-center gap-3 rounded-md border border-line bg-white p-3 sm:grid-cols-[100px_minmax(0,1fr)_auto] sm:gap-5 sm:p-4">
                            <img :src="product.wrapper_main_image_url"
                                :alt="cartStore.getLocalizedName(product.id, languageStore.currentLocale)"
                                class="aspect-square w-[72px] rounded-sm object-cover sm:w-[100px]" />

                            <div class="min-w-0 self-stretch py-1">
                                <h2 class="line-clamp-2 font-serif text-base leading-snug text-brown sm:text-lg">
                                    {{ cartStore.getLocalizedName(product.id, languageStore.currentLocale) }}
                                </h2>
                                <p class="mt-1 text-xs font-medium text-brown/65">
                                    {{ `${product.price} ${$t("currency")}` }}
                                </p>
                                <button type="button" @click="cartStore.removeFromCart(product.id)"
                                    :aria-label="`${$t('Remove')} ${cartStore.getLocalizedName(product.id, languageStore.currentLocale)}`"
                                    class="mt-3 inline-flex items-center gap-1.5 text-xs font-medium text-cOrangeDark transition-colors hover:text-brown">
                                    <i class="ri-delete-bin-line" aria-hidden="true"></i>
                                    {{ $t("Remove") }}
                                </button>
                            </div>

                            <div
                                class="col-span-2 flex w-full items-center justify-between gap-3 border-t border-line pt-3 sm:col-span-1 sm:w-auto sm:flex-col sm:items-end sm:justify-center sm:border-0 sm:pt-0">
                                <div class="flex items-center gap-1.5">
                                    <button type="button" @click="cartStore.decreaseQuantity(product.id)"
                                        :aria-label="$t('Quantity') + ' -'"
                                        class="flex h-8 w-8 items-center justify-center rounded-sm border border-line bg-offwhite text-brown transition-colors hover:border-corange hover:text-cOrangeDark focus:outline-none focus-visible:ring-2 focus-visible:ring-corange">
                                        <i class="ri-subtract-line" aria-hidden="true"></i>
                                    </button>
                                    <input type="text"
                                        class="h-8 w-12 rounded-sm border border-line bg-white p-0 text-center text-sm font-semibold text-brown"
                                        :value="product.quantity" readonly />
                                    <button type="button" @click="cartStore.increaseQuantity(product.id)"
                                        :aria-label="$t('Quantity') + ' +'"
                                        class="flex h-8 w-8 items-center justify-center rounded-sm border border-line bg-offwhite text-brown transition-colors hover:border-corange hover:text-cOrangeDark focus:outline-none focus-visible:ring-2 focus-visible:ring-corange">
                                        <i class="ri-add-line" aria-hidden="true"></i>
                                    </button>
                                </div>
                                <p class="shrink-0 text-sm font-semibold text-cOrangeDark sm:text-base">
                                    {{ `${cartStore.productSubTotal(product.id)} ${$t("currency")}` }}
                                </p>
                            </div>
                        </article>
                    </div>

                    <aside class="rounded-md border border-line bg-white p-5 sm:p-6 lg:sticky lg:top-6">
                        <h2 class="font-serif text-xl text-brown">{{ $t("Total") }}</h2>
                        <div class="mt-4 flex items-center justify-between gap-3 border-t border-line pt-4">
                            <span class="text-sm text-brown/70">{{ $t("Total") }}</span>
                            <span class="text-xl font-semibold text-cOrangeDark">{{ `${cartStore.total}
                                ${$t("currency")}` }}</span>
                        </div>
                        <Link :href="route('FE.checkout')"
                            class="mt-5 inline-flex min-h-11 w-full items-center justify-center gap-2 rounded-sm bg-corange px-5 py-3 text-sm font-semibold text-white transition-colors hover:bg-cOrangeDark focus:outline-none focus-visible:ring-2 focus-visible:ring-cOrangeDark focus-visible:ring-offset-2">
                        <i class="ri-lock-line" aria-hidden="true"></i>
                        {{ $t("Proceed to checkout") }}
                        </Link>
                    </aside>
                </div>
            </template>

            <section v-else
                class="mx-auto flex max-w-xl flex-col items-center rounded-md border border-line bg-white px-6 py-12 text-center sm:py-16">
                <img :src="emptyCart" alt="" class="mb-5 w-40 sm:w-48" />
                <h1 class="font-serif text-2xl text-brown sm:text-3xl">{{ $t("Shopping cart") }}</h1>
                <p class="mt-3 max-w-sm text-sm leading-6 text-brown/70">
                    {{ $t("Your Shopping cart is empty, Continue Shopping") }}
                </p>
                <Link :href="route('FE.shop')"
                    class="mt-6 inline-flex min-h-11 items-center gap-2 rounded-sm bg-corange px-5 py-3 text-sm font-semibold text-white transition-colors hover:bg-cOrangeDark focus:outline-none focus-visible:ring-2 focus-visible:ring-cOrangeDark focus-visible:ring-offset-2">
                {{ $t("Our Products") }}
                <i class="ri-arrow-right-line rtl:rotate-180" aria-hidden="true"></i>
                </Link>
            </section>
        </div>
    </main>
</template>
