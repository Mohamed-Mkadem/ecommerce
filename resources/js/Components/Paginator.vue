<script setup>
defineProps({
    links: Array,
    previous: String,
    next: String,
    variant: { type: String, default: "default" },
});
</script>

<template>
    <div
        :class="variant === 'admin'
            ? 'flex flex-wrap items-center justify-start gap-2'
            : 'flex flex-wrap gap-4'"
    >
        <component
            :is="previous != null ? 'Link' : 'span'"
            :href="previous"
            :preserve-scroll="true"
            v-html="$t('pagination.previous')"
            class="min-w-[35px] rounded-md px-3 py-2 text-center transition-colors"
            :class="{
                'rounded-xl border border-slate-200 bg-white text-slate-600 hover:bg-slate-50 dark:border-neutral-700 dark:bg-neutral-800 dark:text-neutral-200 dark:hover:bg-neutral-700': variant === 'admin' && previous != null,
                'rounded-xl border border-slate-200 bg-slate-50 text-slate-400 dark:border-neutral-700 dark:bg-neutral-800 dark:text-neutral-500': variant === 'admin' && previous == null,
                'text-white bg-primary': variant === 'default' && previous != null,
                'text-white bg-primary bg-opacity-25': variant === 'default' && previous == null,
                'text-white bg-brown hover:bg-cOrangeDark': variant === 'sweetia' && previous != null,
                'text-brown/40 bg-white border border-line cursor-not-allowed': variant === 'sweetia' && previous == null,
            }"
        />
        <template v-for="(link, index) in links" :key="index">
            <component
                v-if="!isNaN(link.label)"
                :is="link.url != null ? 'Link' : 'span'"
                :href="link.url ?? undefined"
                :preserve-scroll="true"
                class="min-w-[35px] rounded-md px-3 py-2 text-center transition-colors"
                :class="{
                    'min-w-10 rounded-xl border border-primary bg-primary font-semibold text-white shadow-sm': variant === 'admin' && link.active,
                    'min-w-10 rounded-xl border border-slate-200 bg-white font-medium text-slate-600 hover:bg-slate-50 dark:border-neutral-700 dark:bg-neutral-800 dark:text-neutral-200 dark:hover:bg-neutral-700': variant === 'admin' && !link.active,
                    'text-white bg-meta-5': variant === 'default' && link.active,
                    'text-white bg-slate-800': variant === 'default' && !link.active,
                    'text-white bg-corange': variant === 'sweetia' && link.active,
                    'text-brown bg-white border border-line hover:bg-[#f3e8da]': variant === 'sweetia' && !link.active,
                    'cursor-not-allowed opacity-60': link.url == null,
                }"
                :aria-current="link.active ? 'page' : undefined"
                >{{ link.label }}</component
            >
        </template>

        <component
            :is="next != null ? 'Link' : 'span'"
            :href="next"
            :preserve-scroll="true"
            v-html="$t('pagination.next')"
            class="min-w-[35px] rounded-md px-3 py-2 text-center transition-colors"
            :class="{
                'rounded-xl border border-slate-200 bg-white text-slate-600 hover:bg-slate-50 dark:border-neutral-700 dark:bg-neutral-800 dark:text-neutral-200 dark:hover:bg-neutral-700': variant === 'admin' && next != null,
                'rounded-xl border border-slate-200 bg-slate-50 text-slate-400 dark:border-neutral-700 dark:bg-neutral-800 dark:text-neutral-500': variant === 'admin' && next == null,
                'text-white bg-primary': variant === 'default' && next != null,
                'text-white bg-primary bg-opacity-25': variant === 'default' && next == null,
                'text-white bg-brown hover:bg-cOrangeDark': variant === 'sweetia' && next != null,
                'text-brown/40 bg-white border border-line cursor-not-allowed': variant === 'sweetia' && next == null,
            }"
        />
    </div>
</template>
