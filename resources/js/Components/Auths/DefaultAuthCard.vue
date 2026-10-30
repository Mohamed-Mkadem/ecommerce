<script setup lang="ts">
import AppLogo from "@/js/Components/AppLogo.vue";

const props = defineProps(["title", "customClasses", "subtitle", "icon"]);
</script>

<template>
    <main class="relative flex min-h-screen items-center justify-center overflow-hidden bg-slate-100 px-4 py-10 sm:px-6">
        <div class="pointer-events-none absolute -left-24 -top-28 h-80 w-80 rounded-full bg-teal-200/50 blur-3xl"></div>
        <div class="pointer-events-none absolute -bottom-32 -right-20 h-96 w-96 rounded-full bg-cyan-200/40 blur-3xl"></div>

        <section class="relative w-full max-w-md overflow-hidden rounded-3xl border border-white/80 bg-white shadow-xl shadow-slate-300/50">
            <div class="h-1.5 bg-gradient-to-r from-teal-700 via-teal-500 to-cyan-500"></div>
            <div class="px-6 py-8 sm:px-10 sm:py-10">
                <div class="mb-8 flex flex-col items-center text-center">
                    <AppLogo class="mb-8 w-40" />
                    <span class="mb-4 flex h-12 w-12 items-center justify-center rounded-2xl bg-teal-50 text-2xl text-teal-700">
                        <i :class="props.icon || 'ri-shield-user-line'" aria-hidden="true"></i>
                    </span>
                    <h1 class="text-2xl font-bold tracking-tight text-slate-800">
                        {{ props.title }}
                    </h1>
                    <p v-if="props.subtitle" class="mt-2 max-w-sm text-sm leading-6 text-slate-500">{{ props.subtitle }}</p>
                </div>

                <slot></slot>
            </div>
        </section>
    </main>
</template>
