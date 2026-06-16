<script setup>
const props = defineProps({
    review: { type: Object },
});
</script>

<template>
    <div class="border-b-2 border-slate-200 last:border-none py-4">
        <div class="flex items-center mb-3">
            <img
                class="w-10 h-10 me-4 rounded-full"
                :src="`${$page.props.base_url}storage/avatars/default.jpg`"
                alt=""
            />
            <div class="font-medium">
                <p class="text-base">
                    {{ review.client_name }}
                </p>
                <time class="block text-xs text-graydark">{{
                    review.date
                }}</time>
            </div>
        </div>

        <div class="flex items-center gap-3 text-gray">
            <i
                class="ri-star-fill"
                v-for="index in 5"
                :key="index"
                :class="{
                    'text-yellow-500': index <= review.stars,
                    'text-slate-400': index > review.stars,
                }"
            />
        </div>

        <div class="mt-2 text-primary" v-if="review.comment">
            {{ review.comment }}
        </div>
    </div>
</template>
