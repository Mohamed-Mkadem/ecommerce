<script setup>

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
    <div class="bg-brown text-white  py-3 px-4 lg:px-8 shadow-md tracking-wide">
        <div class="flex items-center justify-between">


            <div class="flex items-center gap-2 ">
                <a v-for="s in socialLinks" :key="s.link" :href="s.link" target="_blank" rel="noopener noreferrer"
                    :aria-label="s.label"
                    class="w-9 h-9 rounded-lg bg-white/5 border border-white/10 flex items-center justify-center text-white  hover:border-white hover:bg-zinc-400/20 transition-all duration-200">
                    <i :class="s.icon"></i>
                </a>
            </div>
            <a href="tel:56927726" class="flex items-center gap-2">
                <i class="ri-phone-line text-lg"></i>
                <span>56927726</span>
            </a>
        </div>
    </div>
</template>
