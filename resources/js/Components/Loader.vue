<script setup>
import blueLogo from "@/assets/images/logo-blue.webp";
</script>
<template>
    <div
        class="fixed inset-0 flex items-center justify-center bg-white bg-opacity-75 z-999999"
    >
        <div class="flex items-center gap-4 justify-center">
            <img :src="blueLogo" class="w-[70px]" id="first-logo" />
            <img :src="blueLogo" class="w-[70px]" id="second-logo" />
            <img :src="blueLogo" class="w-[70px]" id="third-logo" />
        </div>
    </div>
</template>

<style scoped>
@keyframes moveUp {
    0% {
        transform: translateY(0);
    }
    100% {
        transform: translateY(-10px);
    }
}

#first-logo {
    animation: moveUp 500ms ease-in-out alternate infinite;
}

#second-logo {
    animation: moveUp 500ms ease-in-out alternate infinite;
    animation-delay: 500ms;
}

#third-logo {
    animation: moveUp 500ms ease-in-out alternate infinite;
    animation-delay: 1s;
}
</style>
