<script setup>
import NavContent from "./NavContent";
</script>

<template>
    <nav class="hidden xmd:block py-4 mt-8 border-t border-graydark">
        <ul class="flex justify-center items-center gap-2 md:gap-4">
            <li v-for="(navLink, index) in NavContent" :key="index">
                <Link
                    class="text-black dark:text-white font-semibold transition-all duration-300 tracking-wide px-4 py-2 rounded-full hover:bg-sky-800 hover:text-sky-100"
                    :href="navLink.route"
                    :class="{
                        'bg-sky-800 !text-white shadow-md':
                            $page.component.startsWith(
                                `FrontEnd/${navLink.component}`,
                            ),
                    }"
                >
                    {{ $t(navLink.label) }}
                </Link>
            </li>
        </ul>
    </nav>
</template>
