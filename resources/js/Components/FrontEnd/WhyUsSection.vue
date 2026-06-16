<script setup>
import { useLanguageStore } from "@/js/stores/Language";
import SectionTitle from "./SectionTitle.vue";
const languageStore = useLanguageStore();

const features = {
    en: [
        {
            icon: "ri-truck-line",
            title: "Shipping",
            description: "Fast delivery to your doorstep.",
        },
        {
            icon: "ri-hand-coin-line",
            title: "Cash on Delivery",
            description: "Pay only when you receive.",
        },
        {
            icon: "ri-shield-check-line",
            title: "Guaranteed Quality",
            description: "Premium pastries, always.",
        },
        {
            icon: "ri-customer-service-2-line",
            title: "Customer Support",
            description: "We're here to help.",
        },
    ],
    fr: [
        {
            icon: "ri-truck-line",
            title: "Livraison",
            description: "Livraison rapide à votre porte.",
        },
        {
            icon: "ri-hand-coin-line",
            title: "Paiement à la livraison",
            description: "Payez à la réception.",
        },
        {
            icon: "ri-shield-check-line",
            title: "Qualité garantie",
            description: "Pâtisseries premium garanties.",
        },
        {
            icon: "ri-customer-service-2-line",
            title: "Service client",
            description: "Nous sommes là pour vous aider.",
        },
    ],
    ar: [
        {
            icon: "ri-truck-line",
            title: "التوصيل",
            description: "توصيل سريع إلى باب منزلك.",
        },
        {
            icon: "ri-hand-coin-line",
            title: "الدفع عند الاستلام",
            description: "ادفع عند الاستلام فقط.",
        },
        {
            icon: "ri-shield-check-line",
            title: "الجودة مضمونة",
            description: "تذوّق قبل الشراء.",
        },
        {
            icon: "ri-customer-service-2-line",
            title: "دعم العملاء",
            description: "نحن هنا لمساعدتك.",
        },
    ],
};
</script>

<template>
    <section class="container-3xl px-4 py-12">
        <SectionTitle :text="$t('Why Choose Us?')" class="mb-8" color="blue" />

        <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-4 mt-12">
            <div
                v-for="(feature, index) in features[
                    languageStore.currentLocale
                ]"
                :key="index"
                class="flex flex-col items-center justify-center p-6 bg-zinc-100 shadow-lg rounded-lg hover:shadow-xl transition-shadow"
            >
                <i :class="feature.icon + ' text-4xl text-gold mb-4'"></i>
                <h3 class="text-lg font-bold text-sky-700 mb-2 text-center">
                    {{ feature.title }}
                </h3>
                <p class="text-neutral-700 text-center">
                    {{ feature.description }}
                </p>
            </div>
        </div>
    </section>
</template>
