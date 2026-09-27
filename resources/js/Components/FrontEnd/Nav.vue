<script setup>
import NavContent from "./NavContent";
</script>

<template>
    <nav class="hidden slg:block">
        <ul class="flex justify-center items-center  lg:gap-4 ">
            <li v-for="(navLink, index) in NavContent" :key="index">
                <Link
                    class="text-brown  font-semibold transition-all duration-300 tracking-wide px-4 py-3 rounded-md hover:border-b hover:text-corange border-graydark"
                    :href="navLink.route" :class="{
                        'bg-corange !text-white shadow-md':
                            $page.component.startsWith(
                                `FrontEnd/${navLink.component}`,
                            ),
                    }">
                {{ $t(navLink.label) }}
                </Link>
            </li>
        </ul>
    </nav>
</template>
