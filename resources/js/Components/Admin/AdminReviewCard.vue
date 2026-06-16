<script setup>
const props = defineProps({
    review: { type: Object },
});
</script>

<template>
    <div
        class="rounded-md bg-white p-4 relative hover:bg-yellow-50 transition-colors"
    >
        <div class="flex items-center justify-between gap-2 flex-wrap">
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
            <p>{{ review.date }}</p>
        </div>
        <div class="my-4 flex items-center gap-3">
            <img :src="review.product.main_image_url" class="w-25" />
            <h2 class="text-primary text-xl">
                {{ review.product.name }}
            </h2>
        </div>
        <p v-if="review.comment" class="text-primary line-clamp">
            {{ review.comment }}
        </p>
        <ModalLink
            :href="route('reviews.show', review)"
            class="absolute inset-0"
            :close-button="false"
        >
        </ModalLink>
    </div>
</template>

<style scoped>
.line-clamp {
    display: -webkit-box;
    -webkit-line-clamp: 3;
    line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
    text-overflow: ellipsis;
}
</style>
