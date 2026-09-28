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



    <!-- ───────────────────────── PRODUCTS ───────────────────────── -->
    <section id="products" class="py-20 px-4 md:px-12 max-w-screen-3xl mx-auto">
        <!-- Section header -->
        <div class="text-center mb-14">
            <p class="text-gold font-bold text-xs tracking-[0.2em] uppercase mb-2">
                {{ $t("Our Offers") }}
            </p>
            <h2 class="text-3xl md:text-4xl font-extrabold text-sky-900">
                {{ $t("Best sellers") }}
            </h2>
            <div class="mt-4 mx-auto w-14 h-1 rounded-full bg-gold"></div>
        </div>

        <!-- Cards grid -->
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6 md:gap-8">
            <WrapperCard v-for="wrapper in props.wrappers" :key="wrapper.id" :wrapper="wrapper" />
        </div>

        <!-- "See more" CTA -->
        <div class="mt-14 flex justify-center">
            <Link :href="route('FE.shop')"
                class="inline-flex items-center gap-2 rounded-xl border-2 border-primary bg-white px-8 py-3.5 text-sm font-bold text-primary shadow-sm transition-all duration-200 hover:bg-primary hover:text-white hover:shadow-md">
            {{ $t("See More") }}
            <i class="ri-arrow-right-line text-base"></i>
            </Link>
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
