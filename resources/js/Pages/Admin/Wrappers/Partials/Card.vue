<script setup>
defineProps({
    wrapper: {
        type: Object,
        required: true,
    },
});
</script>

<template>
    <div class="shadow-2 p-5 bg-white rounded-md flex flex-col h-full">
        <div class="flex items-center justify-between flex-wrap gap-2 mb-3">
            <p
                class="p-2 text-white text-sm rounded-md font-medium bg-violet-600 flex items-center gap-1.5"
            >
                <i class="ri-cake-3-line text-base"></i>
                <span>{{ wrapper.products_count }}</span>
            </p>
            <p
                class="p-2 text-white text-sm rounded-md font-medium"
                :class="{
                    'bg-red-500': !wrapper.is_active,
                    'bg-green-600': wrapper.is_active,
                }"
            >
                {{
                    wrapper.is_active
                        ? $t("Wrapper.active")
                        : $t("Wrapper.inactive")
                }}
            </p>
        </div>

        <img
            :src="wrapper.main_image_url"
            :alt="wrapper.title"
            class="rounded-sm mx-auto max-h-48 object-contain"
        />

        <div class="text-center mt-4 flex-1">
            <Link
                :href="route('wrappers.show', wrapper.slug)"
                class="text-xl text-primary font-semibold hover:text-meta-5 transition-colors line-clamp-2"
                :title="wrapper.title"
            >
                {{ wrapper.title }}
            </Link>

            <p class="text-xl font-semibold text-meta-1 mt-2">
                {{ wrapper.price }} {{ $t("Product.currency") }}
            </p>
        </div>

        <Link
            :href="route('wrappers.show', wrapper.slug)"
            class="mt-4 block text-center text-sm font-medium text-sky-700 hover:underline"
        >
            {{ $t("Wrapper.view_details") }}
        </Link>
    </div>
</template>

<style scoped>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>
