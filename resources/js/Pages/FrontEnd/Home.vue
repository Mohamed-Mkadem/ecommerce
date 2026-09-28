<script setup>
import WrapperCard from "@/js/Components/FrontEnd/WrapperCard.vue";
import HeroShowcase from "@/js/Components/FrontEnd/HeroShowcase.vue";
import { useLanguageStore } from "@/js/stores/Language";

const languageStore = useLanguageStore();

const props = defineProps({
    wrappers: Array,
});

function scrollToProducts() {
    document.getElementById("products")?.scrollIntoView({ behavior: "smooth" });
}

const trustFeatures = {
    en: [
        {
            icon: "ri-truck-line",
            title: "Fast Delivery",
            description: "Right to your doorstep",
        },
        {
            icon: "ri-hand-coin-line",
            title: "Cash on Delivery",
            description: "Pay when you receive",
        },
        {
            icon: "ri-shield-check-line",
            title: "Premium Quality",
            description: "Handcrafted with care",
        },
        {
            icon: "ri-customer-service-2-line",
            title: "Customer Support",
            description: "Always here for you",
        },
    ],
    fr: [
        {
            icon: "ri-truck-line",
            title: "Livraison rapide",
            description: "Jusqu'à votre porte",
        },
        {
            icon: "ri-hand-coin-line",
            title: "Paiement à la livraison",
            description: "Payez à la réception",
        },
        {
            icon: "ri-shield-check-line",
            title: "Qualité premium",
            description: "Fait avec soin",
        },
        {
            icon: "ri-customer-service-2-line",
            title: "Service client",
            description: "Toujours disponible",
        },
    ],
    ar: [
        {
            icon: "ri-truck-line",
            title: "توصيل سريع",
            description: "لباب منزلك مباشرة",
        },
        {
            icon: "ri-hand-coin-line",
            title: "الدفع عند الاستلام",
            description: "ادفع عند الاستلام",
        },
        {
            icon: "ri-shield-check-line",
            title: "جودة فاخرة",
            description: "مصنوع بعناية",
        },
        {
            icon: "ri-customer-service-2-line",
            title: "دعم العملاء",
            description: "نحن دائماً هنا",
        },
    ],
};
</script>

<template>

    <Head :title="`${$t('Nav.home')} `"></Head>

    <HeroShowcase />



    <section id="products" class="mx-auto max-w-screen-3xl bg-offwhite px-5 py-16 sm:px-8 md:px-12 md:py-20">
        <div class="mx-auto max-w-screen-xl">
            <div class="mb-8 flex flex-col gap-5 sm:mb-10 sm:flex-row sm:items-end sm:justify-between">
                <div class="max-w-lg">
                    <p class="mb-2 text-[10px] font-semibold uppercase tracking-[0.2em] text-cOrangeDark">
                        {{ $t("bestsellers.eyebrow") }}
                    </p>
                    <h2 class="font-serif text-3xl leading-tight tracking-tight text-brown sm:text-4xl">
                        {{ $t("bestsellers.title") }}
                    </h2>
                    <p class="mt-3 max-w-md text-xs leading-5 text-brown/75 sm:text-sm sm:leading-6">
                        {{ $t("bestsellers.description") }}
                    </p>
                </div>

                <Link :href="route('FE.shop')"
                    class="inline-flex w-fit items-center gap-2 border-b border-corange/50 pb-1 text-xs font-semibold text-cOrangeDark transition-colors hover:text-brown">
                {{ $t("bestsellers.view_all") }}
                <i class="ri-arrow-right-line rtl:rotate-180" aria-hidden="true"></i>
                </Link>
            </div>

            <div
                class="grid min-[600px]:grid-cols-2 gap-x-4 gap-y-8 sm:gap-x-6 sm:gap-y-10 lg:grid-cols-4 lg:gap-x-5 xl:gap-x-6">
                <WrapperCard v-for="wrapper in props.wrappers" :key="wrapper.id" :wrapper="wrapper" />
            </div>

            <div class="mt-10 flex justify-center sm:mt-12">
                <Link :href="route('FE.shop')"
                    class="inline-flex min-h-11 items-center gap-4 rounded-sm bg-corange px-5 py-3 text-xs font-semibold text-white transition hover:bg-cOrangeDark focus:outline-none focus-visible:ring-2 focus-visible:ring-cOrangeDark focus-visible:ring-offset-2">
                {{ $t("bestsellers.explore_all") }}
                <i class="ri-arrow-right-line rtl:rotate-180" aria-hidden="true"></i>
                </Link>
            </div>
        </div>
    </section>

    <!-- ─────────────────────────── CTA BANNER ────────────────────── -->
    <section class="px-4 md:px-12 pb-16">
        <div
            class="relative bg-gradient-to-br max-w-screen-3xl mx-auto from-sky-900 via-sky-800 to-primary rounded-2xl py-16 px-6 text-center text-white shadow-2xl overflow-hidden">
            <!-- Decorative blobs -->
            <div class="absolute -top-16 -left-16 w-56 h-56 rounded-full bg-white/5 pointer-events-none"></div>
            <div class="absolute -bottom-20 -right-10 w-72 h-72 rounded-full bg-white/5 pointer-events-none"></div>
            <div
                class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-96 h-32 bg-gold/5 rounded-full blur-3xl pointer-events-none">
            </div>

            <div class="relative z-10 max-w-xl mx-auto">
                <p class="text-gold text-xs font-bold tracking-[0.25em] uppercase mb-4">
                    {{ $t("Why Choose Us?") }}
                </p>
                <h2 class="text-2xl md:text-4xl font-extrabold mb-4 leading-tight" v-html="$t('Showcase.title')"></h2>
                <p class="text-white/65 mb-8 text-base leading-relaxed">
                    {{ $t("Footer.description") }}
                </p>
                <Link :href="route('FE.shop')"
                    class="inline-flex items-center gap-2 bg-gold hover:bg-yellow-400 text-primary font-bold px-9 py-4 rounded-xl shadow-lg transition-all duration-300 hover:shadow-xl hover:-translate-y-0.5">
                <i class="ri-shopping-bag-line"></i>
                {{ $t("Showcase.cta") }}
                </Link>
            </div>
        </div>
    </section>
</template>
