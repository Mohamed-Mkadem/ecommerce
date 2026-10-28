<script setup lang="ts">
import { useSidebarStore } from "@/js/stores/sidebar";
import { ref } from "vue";
const sidebarStore = useSidebarStore();

const props = defineProps(["items", "page"]);
const items = ref(props.items);

const handleItemClick = (index: number) => {
    const pageName =
        sidebarStore.selected === props.items[index].label
            ? ""
            : props.items[index].label;
    sidebarStore.selected = pageName;
};
</script>

<template>
    <ul class="mt-4 mb-5.5 flex flex-col gap-2.5 pl-6">
        <template v-for="(childItem, index) in items" :key="index">
            <li
                v-if="
                    !childItem.onlyAdmin ||
                    (childItem.onlyAdmin && $page.props.auth.user.role === 'admin')
                "
            >
                <Link
                    :href="childItem.route"
                    @click="handleItemClick(index)"
                    class="group relative flex items-center gap-2.5 rounded-lg px-3 py-2 text-sm font-medium text-bodydark2 duration-200 ease-in-out hover:bg-white/20 hover:text-white"
                    :class="{
                        '!text-white !font-semibold': childItem.component === $page.component,
                    }"
                >
                    {{ $t(childItem.label) }}
                </Link>
            </li>
        </template>
    </ul>
</template>
