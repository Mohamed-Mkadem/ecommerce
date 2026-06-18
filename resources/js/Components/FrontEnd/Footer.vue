<script setup>
import AppLogo from "../AppLogo.vue";

const socialLinks = [
    {
        icon: "ri-facebook-fill",
        link: "https://www.facebook.com/Boughanmipatisserie",
        label: "Facebook",
    },
    {
        icon: "ri-instagram-line",
        link: "https://www.instagram.com/boughanmipatisserie",
        label: "Instagram",
    },
    {
        icon: "ri-tiktok-line",
        link: "https://www.tiktok.com/@ptisserie.boughan",
        label: "TikTok",
    },
];

const phoneLinks = [
    { text: "+216 29 144 980", link: "tel:+21629144980" },
    { text: "+216 29 144 054", link: "tel:+21629144054" },
];

const infoLinks = [
    { text: "Nav.about", link: "FE.about" },
    { text: "Nav.contact", link: "FE.contact" },
    { text: "Nav.privacy", link: "FE.privacy" },
    { text: "Nav.terms", link: "FE.terms" },
];
</script>

<template>
    <footer class="bg-primary text-zinc-300">
        <!-- Gold accent top line -->
        <div
            class="h-px bg-gradient-to-r from-transparent via-gold to-transparent"
        ></div>

        <div
            class="max-w-screen-xl mx-auto px-6 pt-14 pb-10 grid gap-12 lg:grid-cols-[1.4fr_1fr_1fr]"
        >
            <!-- Brand column -->
            <div class="flex flex-col items-center lg:items-start gap-4">
                <AppLogo class="w-[160px]" color="white" />
                <p
                    class="text-sm text-zinc-400 leading-relaxed text-center lg:text-start max-w-xs"
                >
                    {{ $t("Footer.description") }}
                </p>
                <!-- Social icons -->
                <div class="flex items-center gap-2 mt-1">
                    <a
                        v-for="s in socialLinks"
                        :key="s.link"
                        :href="s.link"
                        target="_blank"
                        rel="noopener noreferrer"
                        :aria-label="s.label"
                        class="w-9 h-9 rounded-lg bg-white/5 border border-white/10 flex items-center justify-center text-zinc-400 hover:text-gold hover:border-gold/40 hover:bg-gold/10 transition-all duration-200"
                    >
                        <i :class="s.icon + ' text-lg'"></i>
                    </a>
                </div>
            </div>

            <!-- Contact column -->
            <div class="text-center lg:text-start">
                <h3
                    class="text-xs font-bold tracking-[0.2em] uppercase text-gold mb-6"
                >
                    {{ $t("Contact") }}
                </h3>
                <ul class="space-y-4">
                    <li
                        class="flex items-center justify-center lg:justify-start gap-3"
                    >
                        <i
                            class="ri-mail-line text-gold text-base flex-shrink-0"
                        ></i>
                        <a
                            href="mailto:contact@boughanmipatisserie.com"
                            class="text-sm text-zinc-400 hover:text-gold transition-colors break-all"
                        >
                            contact@boughanmipatisserie.com
                        </a>
                    </li>
                    <li
                        v-for="p in phoneLinks"
                        :key="p.link"
                        class="flex items-center justify-center lg:justify-start gap-3"
                    >
                        <i
                            class="ri-phone-line text-gold text-base flex-shrink-0"
                        ></i>
                        <a
                            :href="p.link"
                            dir="ltr"
                            class="text-sm text-zinc-400 hover:text-gold transition-colors"
                        >
                            {{ p.text }}
                        </a>
                    </li>
                    <li
                        class="flex items-start justify-center lg:justify-start gap-3"
                    >
                        <i
                            class="ri-map-pin-line text-gold text-base flex-shrink-0 mt-0.5"
                        ></i>
                        <span class="text-sm text-zinc-400"
                            >Omar Khalfa Street, Rafaha – Ariana</span
                        >
                    </li>
                </ul>
            </div>

            <!-- Info links column -->
            <div class="text-center lg:text-start">
                <h3
                    class="text-xs font-bold tracking-[0.2em] uppercase text-gold mb-6"
                >
                    {{ $t("Information") }}
                </h3>
                <ul class="space-y-3.5">
                    <li v-for="(link, index) in infoLinks" :key="index">
                        <Link
                            :href="route(link.link)"
                            class="inline-flex items-center gap-2 text-sm text-zinc-400 hover:text-gold transition-colors group"
                        >
                            <i
                                class="ri-arrow-right-s-line text-gold/30 group-hover:text-gold transition-colors flex-shrink-0"
                            ></i>
                            {{ $t(link.text) }}
                        </Link>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Bottom bar -->
        <div
            class="border-t border-white/10 py-5 text-center text-xs text-zinc-400"
        >
            <p v-html="$t('Copyrights')"></p>
        </div>
    </footer>
</template>
