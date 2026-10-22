<script setup>
import Header from "@/js/Components/FrontEnd/Header.vue";
import Footer from "@/js/Components/FrontEnd/Footer.vue";
import { onMounted, onUnmounted, ref } from "vue";
import { router, usePage } from "@inertiajs/vue3";
import { useLanguageStore } from "@/js/stores/Language";
import Loader from "@/js/Components/Loader.vue";
const languageStore = useLanguageStore();
const page = usePage();
const tracking = page.props.tracking || {};
const processing = ref(false);
let removeNavigateListener;
let lastTrackedUrl = null;

if (typeof window !== "undefined") {
    window.facebookTrackingEnabled = Boolean(
        tracking.facebook_enabled && tracking.facebook_pixel_id,
    );
}

function initializeGoogleAnalytics() {
    const measurementId = tracking.google_analytics_measurement_id;
    if (!measurementId || window.__sweetiaGa4Id === measurementId) return;

    window.dataLayer = window.dataLayer || [];
    window.gtag = window.gtag || function () {
        window.dataLayer.push(arguments);
    };

    if (!document.querySelector(`script[data-ga4-id="${measurementId}"]`)) {
        const scriptTag = document.createElement("script");
        scriptTag.async = true;
        scriptTag.dataset.ga4Id = measurementId;
        scriptTag.src = `https://www.googletagmanager.com/gtag/js?id=${encodeURIComponent(measurementId)}`;
        document.head.appendChild(scriptTag);
    }

    window.gtag("js", new Date());
    window.gtag("config", measurementId, { send_page_view: false });
    window.__sweetiaGa4Id = measurementId;
}

function initializeFacebookPixel() {
    const pixelId = tracking.facebook_pixel_id;
    if (!window.facebookTrackingEnabled || !pixelId || window.fbq) return;

    !(function (f, b, e, v, n, t, s) {
        if (f.fbq) return;
        n = f.fbq = function () {
            n.callMethod ? n.callMethod.apply(n, arguments) : n.queue.push(arguments);
        };
        if (!f._fbq) f._fbq = n;
        n.push = n;
        n.loaded = true;
        n.version = "2.0";
        n.queue = [];
        t = b.createElement(e);
        t.async = true;
        t.src = v;
        s = b.getElementsByTagName(e)[0];
        s.parentNode.insertBefore(t, s);
    })(window, document, "script", "https://connect.facebook.net/en_US/fbevents.js");

    window.fbq("init", pixelId);
}

function trackPageView(url = window.location.href) {
    const resolvedUrl = new URL(url, window.location.origin);
    if (lastTrackedUrl === resolvedUrl.href) return;
    lastTrackedUrl = resolvedUrl.href;

    if (window.gtag && tracking.google_analytics_measurement_id) {
        window.gtag("event", "page_view", {
            page_location: resolvedUrl.href,
            page_path: `${resolvedUrl.pathname}${resolvedUrl.search}`,
            page_title: document.title,
        });
    }

    if (window.facebookTrackingEnabled && window.fbq) {
        window.fbq("track", "PageView");
    }
}

onMounted(() => {
    document.documentElement.setAttribute("dir", languageStore.direction);
    document.body.setAttribute("class", "!bg-white ");

    initializeGoogleAnalytics();
    initializeFacebookPixel();
    trackPageView();

    removeNavigateListener = router.on("navigate", (event) => {
        trackPageView(event.detail.page.url);
    });
});

onUnmounted(() => removeNavigateListener?.());

router.on("start", () => (processing.value = true));
router.on("finish", () => (processing.value = false));
</script>

<template>
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
