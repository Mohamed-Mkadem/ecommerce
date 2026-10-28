import { useStorage } from "@vueuse/core";
import { defineStore } from "pinia";
import { ref } from "vue";

export const useSidebarStore = defineStore("sidebar", () => {
    const isSidebarOpen = ref(false);
    const isCollapsed = useStorage("admin-sidebar-collapsed", false);
    const selected = useStorage("selected", ref("eCommerce"));
    const page = useStorage("page", ref("Dashboard"));

    function toggleSidebar() {
        isSidebarOpen.value = !isSidebarOpen.value;
    }

    function toggleCollapsed() {
        isCollapsed.value = !isCollapsed.value;
    }

    return { isSidebarOpen, isCollapsed, toggleCollapsed, toggleSidebar, selected, page };
});
