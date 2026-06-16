<script setup lang="ts">
const props = defineProps(["title", "customClasses"]);
</script>

<template>
    <div
        class="rounded-sm min-h-screen border-stroke bg-whiten shadow-default flex flex-wrap items-center justify-center"
    >
        <div
            class="w-full bg-white border-stroke md:w-1/2 lg:w-1/3 shadow-default rounded-xl"
        >
            <div class="w-full p-12">
                <h2
                    class="mb-9 text-xl font-bold text-slate-600 sm:text-title-xl2 text-center"
                >
                    {{ props.title }} <i class="ri-admin"></i>
                </h2>

                <slot></slot>
            </div>
        </div>
    </div>
    <!-- ====== Forms Section End -->
</template>
