<script setup>
import Header from "@/js/Components/FrontEnd/Header.vue";
import Footer from "@/js/Components/FrontEnd/Footer.vue";
import { onMounted, ref } from "vue";
import { router, Head } from "@inertiajs/vue3";
import { useLanguageStore } from "@/js/stores/Language";
import PhoneNumber from "@/js/Components/FrontEnd/PhoneNumber.vue";
import Loader from "@/js/Components/Loader.vue";
const languageStore = useLanguageStore();
const processing = ref(false);

onMounted(() => {
    document.documentElement.setAttribute("dir", languageStore.direction);
    document.body.setAttribute("class", "!bg-white ");

    // Google Analytics tag injection
    if (!window.gtag) {
        const scriptTag = document.createElement("script");
        scriptTag.async = true;
        scriptTag.src =
            "https://www.googletagmanager.com/gtag/js?id=G-S9JRM4LHNP";
        document.head.appendChild(scriptTag);

        window.dataLayer = window.dataLayer || [];
        window.gtag = function () {
            window.dataLayer.push(arguments);
        };
        window.gtag("js", new Date());
        window.gtag("config", "G-S9JRM4LHNP");
    }

    // Facebook Pixel injection
    if (!window.fbq) {
        !(function (f, b, e, v, n, t, s) {
            if (f.fbq) return;
            n = f.fbq = function () {
                n.callMethod
                    ? n.callMethod.apply(n, arguments)
                    : n.queue.push(arguments);
            };
            if (!f._fbq) f._fbq = n;
            n.push = n;
            n.loaded = !0;
            n.version = "2.0";
            n.queue = [];
            t = b.createElement(e);
            t.async = !0;
            t.src = v;
            s = b.getElementsByTagName(e)[0];
            s.parentNode.insertBefore(t, s);
        })(
            window,
            document,
            "script",
            "https://connect.facebook.net/en_US/fbevents.js",
        );
        fbq("init", "886990717720546");
        fbq("track", "PageView");
    }
});

router.on("start", () => (processing.value = true));
router.on("finish", () => (processing.value = false));
</script>

<template>
    <Head>
        <noscript>
            <img
                height="1"
                width="1"
                style="display: none"
                src="https://www.facebook.com/tr?id=2603076653427200&ev=PageView&noscript=1"
            />
        </noscript>
    </Head>
    <div>
        <PhoneNumber />
        <Header class="max-w-screen-3xl mx-auto"></Header>

        <main id="main-content">
            <slot></slot>
        </main>

        <Footer class="max-w-screen-3xl mx-auto" />
    </div>
    <Loader v-if="processing" />
</template>
