<script setup>
import { computed } from "vue";

const props = defineProps({ status: Number });

const descriptions = {
    503: "error503",
    500: "error500",
    404: "error404",
    403: "error403",
};

const icons = {
    503: "ri-tools-line",
    500: "ri-server-line",
    404: "ri-search-eye-line",
    403: "ri-forbid-2-line",
};

const description = computed(() => descriptions[props.status] ?? "error500");
const icon = computed(() => icons[props.status] ?? "ri-error-warning-line");
</script>

<template>
    <Head :title="`${status} - ${$t('error.title')}`" />

    <main
        class="relative flex min-h-screen items-center justify-center overflow-hidden bg-slate-50 px-5 py-12"
    >
        <div
            class="pointer-events-none absolute -left-24 -top-28 h-96 w-96 rounded-full bg-gradient-to-br from-teal-300/60 via-teal-200/35 to-cyan-100/10 blur-3xl"
            style="background: radial-gradient(circle at top left, rgba(13, 148, 136, 0.32) 0%, rgba(20, 184, 166, 0.16) 38%, transparent 72%)"
        ></div>
        <div
            class="pointer-events-none absolute -bottom-32 -right-20 h-[28rem] w-[28rem] rounded-full bg-gradient-to-tl from-teal-300/55 via-cyan-200/30 to-teal-100/10 blur-3xl"
            style="background: radial-gradient(circle at bottom right, rgba(13, 148, 136, 0.3) 0%, rgba(34, 211, 238, 0.15) 38%, transparent 72%)"
        ></div>

        <section class="relative z-10 w-full max-w-2xl text-center">
            <div
                class="mx-auto mb-6 flex h-16 w-16 items-center justify-center rounded-2xl bg-teal-50 text-3xl text-teal-700"
            >
                <i :class="icon" aria-hidden="true"></i>
            </div>
            <p
                class="font-black tracking-[-0.08em] text-teal-800"
                style="font-size: clamp(9rem, 30vw, 20rem); line-height: 0.8"
            >
                {{ status }}
            </p>
            <h1
                class="mt-8 text-2xl font-bold tracking-tight text-slate-800 sm:text-3xl"
            >
                {{ $t("error.title") }}
            </h1>
            <p
                class="mx-auto mt-3 max-w-xl text-base leading-7 text-slate-500 sm:text-lg"
            >
                {{ $t(description) }}
            </p>
        </section>
    </main>
</template>
