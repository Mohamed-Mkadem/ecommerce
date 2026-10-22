<script setup>
defineProps({
    links: Array,
    previous: String,
    next: String,
    variant: { type: String, default: "default" },
});
</script>

<template>
    <div class="flex gap-4 flex-wrap">
        <component
            :is="previous != null ? 'Link' : 'span'"
            :href="previous"
            :preserve-scroll="true"
            v-html="$t('pagination.previous')"
            class="px-3 py-2 rounded-md min-w-[35px] text-center transition-colors"
            :class="{
                'text-white bg-primary': variant !== 'sweetia' && previous != null,
                'text-white bg-primary bg-opacity-25': variant !== 'sweetia' && previous == null,
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
                class="px-3 py-2 rounded-md min-w-[35px] text-center transition-colors"
                :class="{
                    'text-white bg-meta-5': variant !== 'sweetia' && link.active,
                    'text-white bg-slate-800': variant !== 'sweetia' && !link.active,
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
            class="px-3 py-2 rounded-md min-w-[35px] text-center transition-colors"
            :class="{
                'text-white bg-primary': variant !== 'sweetia' && next != null,
                'text-white bg-primary bg-opacity-25': variant !== 'sweetia' && next == null,
                'text-white bg-brown hover:bg-cOrangeDark': variant === 'sweetia' && next != null,
                'text-brown/40 bg-white border border-line cursor-not-allowed': variant === 'sweetia' && next == null,
            }"
        />
    </div>
</template>
