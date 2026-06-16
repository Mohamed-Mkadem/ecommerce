<script setup>
import { useLanguageStore } from "@/js/stores/Language";

const languageStore = useLanguageStore();

const info = {
    en: { address: "Omar Khalfa Street, Rafaha City – Ariana" },
    fr: { address: "Rue Omar Khalfa, Cité Rafaha – Ariana" },
    ar: { address: "نهج عمر خلفة، حي الرفاهة – أريانة" },
};
</script>

<template>
    <Head :title="$t('Contact us')" />

    <!-- Page banner -->
    <div
        class="bg-gradient-to-br from-sky-900 via-sky-800 to-primary text-white py-20 px-6 text-center"
    >
        <h1 class="text-4xl font-extrabold tracking-tight">
            {{ $t("Contact us") }}
        </h1>
        <p class="text-white/60 mt-3 text-base max-w-md mx-auto">
            {{ $t("contactPage.title") }}
        </p>
    </div>

    <div class="py-16 px-4 md:px-12 max-w-screen-xl mx-auto">
        <!-- Contact cards -->
        <div class="grid gap-5 md:grid-cols-3 mb-16">
            <!-- Phone -->
            <div
                class="bg-white rounded-2xl border border-neutral-100 shadow-md px-6 py-10 text-center hover:shadow-xl transition-shadow duration-300 group"
            >
                <div
                    class="w-14 h-14 rounded-2xl bg-sky-50 border border-sky-100 flex items-center justify-center mx-auto mb-5 group-hover:bg-sky-100 transition-colors"
                >
                    <i class="ri-phone-fill text-2xl text-sky-700"></i>
                </div>
                <h3
                    class="font-bold text-primary mb-4 text-xs tracking-widest uppercase"
                >
                    {{ $t("Phone Number") }}
                </h3>
                <a
                    href="tel:29144980"
                    class="block text-neutral-600 hover:text-sky-700 transition-colors font-semibold text-lg"
                    dir="ltr"
                    >29 144 980</a
                >
                <a
                    href="tel:29144054"
                    class="block text-neutral-600 hover:text-sky-700 transition-colors font-semibold text-lg"
                    dir="ltr"
                    >29 144 054</a
                >
            </div>

            <!-- Email -->
            <div
                class="bg-white rounded-2xl border border-neutral-100 shadow-md px-6 py-10 text-center hover:shadow-xl transition-shadow duration-300 group"
            >
                <div
                    class="w-14 h-14 rounded-2xl bg-sky-50 border border-sky-100 flex items-center justify-center mx-auto mb-5 group-hover:bg-sky-100 transition-colors"
                >
                    <i class="ri-mail-line text-2xl text-sky-700"></i>
                </div>
                <h3
                    class="font-bold text-primary mb-4 text-xs tracking-widest uppercase"
                >
                    {{ $t("Email") }}
                </h3>
                <a
                    dir="ltr"
                    href="mailto:contact@boughanmipatisserie.com"
                    class="block text-neutral-600 hover:text-sky-700 hover:underline transition-colors text-sm break-all"
                >
                    contact@boughanmipatisserie.com
                </a>
            </div>

            <!-- Address -->
            <div
                class="bg-white rounded-2xl border border-neutral-100 shadow-md px-6 py-10 text-center hover:shadow-xl transition-shadow duration-300 group"
            >
                <div
                    class="w-14 h-14 rounded-2xl bg-sky-50 border border-sky-100 flex items-center justify-center mx-auto mb-5 group-hover:bg-sky-100 transition-colors"
                >
                    <i class="ri-road-map-line text-2xl text-sky-700"></i>
                </div>
                <h3
                    class="font-bold text-primary mb-4 text-xs tracking-widest uppercase"
                >
                    {{ $t("Our Location") }}
                </h3>
                <p
                    class="text-neutral-600"
                    :dir="languageStore.currentLocale === 'ar' ? 'rtl' : 'ltr'"
                >
                    {{ info[languageStore.currentLocale].address }}
                </p>
            </div>
        </div>

        <!-- Map section -->
        <div>
            <div class="mb-6">
                <p
                    class="text-gold font-bold text-xs tracking-[0.2em] uppercase mb-2"
                >
                    {{ $t("Our Location") }}
                </p>
                <h2 class="text-2xl font-extrabold text-primary">
                    {{ $t("Our Location") }}
                </h2>
                <div class="mt-3 w-12 h-1 rounded-full bg-gold"></div>
            </div>
            <div
                class="rounded-2xl overflow-hidden shadow-lg border border-neutral-100"
            >
                <iframe
                    src="https://www.google.com/maps/embed?pb=!1m14!1m8!1m3!1d3192.576422902486!2d10.121168129410362!3d36.85261929107798!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x12fd3373530de9f5%3A0x7ef395cd30aa32d6!2sBoughanmi%20P%C3%A2tisserie!5e0!3m2!1sen!2stn!4v1741783476810!5m2!1sen!2stn"
                    width="100%"
                    height="450"
                    style="border: 0; display: block"
                    allowfullscreen=""
                    loading="lazy"
                    referrerpolicy="no-referrer-when-downgrade"
                ></iframe>
            </div>
        </div>
    </div>
</template>
