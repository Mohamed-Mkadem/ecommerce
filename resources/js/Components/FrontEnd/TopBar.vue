<script setup>
import LanguageSwitcher from "../Admin/Header/LanguageSwitcher.vue";

const socialLinks = [
    {
        icon: "ri-facebook-fill",
        link: "https://www.facebook.com/",
        label: "Facebook",
    },
    {
        icon: "ri-instagram-line",
        link: "https://www.instagram.com/",
        label: "Instagram",
    },
    {
        icon: "ri-tiktok-line",
        link: "https://www.tiktok.com/",
        label: "TikTok",
    },
];
</script>

<template>
    <div class="bg-brown text-white font-bold py-3 px-4 shadow-md tracking-wide">
        <div class="flex items-center justify-between">


            <div class="flex items-center gap-2 ">
                <a v-for="s in socialLinks" :key="s.link" :href="s.link" target="_blank" rel="noopener noreferrer"
                    :aria-label="s.label"
                    class="w-9 h-9 rounded-lg bg-white/5 border border-white/10 flex items-center justify-center text-zinc-400 hover:text-gold/60 hover:border-gold/40 hover:bg-gold/10 transition-all duration-200">
                    <i :class="s.icon + ' text-lg'"></i>
                </a>
            </div>
            <LanguageSwitcher :is-in-front-end="true" />
        </div>
    </div>
</template>
