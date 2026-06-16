<script setup>
defineProps(["links", "previous", "next"]);
</script>

<template>
    <div class="flex gap-4 flex-wrap">
        <component
            :is="previous != null ? 'Link' : 'span'"
            :href="previous"
            :preserve-scroll="true"
            v-html="$t('pagination.previous')"
            class="text-white bg-primary px-3 py-2 rounded-md min-w-[35px] text-center"
            :class="{
                'bg-opacity-25': previous == null,
            }"
        />
        <Link
            :preserve-scroll="true"
            v-for="(link, index) in links"
            v-show="!isNaN(link.label)"
            :href="link.url"
            class="text-white px-3 py-2 rounded-md min-w-[35px] text-center"
            :class="{
                'bg-meta-5': link.active,
                'bg-slate-800': !link.active,
            }"
            >{{ link.label }}</Link
        >

        <component
            :is="next != null ? 'Link' : 'span'"
            :href="next"
            :preserve-scroll="true"
            v-html="$t('pagination.next')"
            class="text-white bg-primary px-3 py-2 rounded-md min-w-[35px] text-center"
            :class="{
                'bg-opacity-25': next == null,
            }"
        />
    </div>
</template>
