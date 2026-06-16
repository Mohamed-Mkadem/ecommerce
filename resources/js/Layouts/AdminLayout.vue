<script setup>
import SideBar from "@/js/Components/Admin/Sidebar/SideBar.vue";
import Header from "@/js/Components/Admin/Header/Header.vue";
import { onMounted, ref, computed } from "vue";
import { useLanguageStore } from "@/js/stores/Language";
import { usePage, router } from "@inertiajs/vue3";
import sound from "@/assets/sounds/notification.wav";
import cashSound from "@/assets/sounds/cash_sound.mp3";
import EventBus from "@/js/EventBus";
import Loader from "@/js/Components/Loader.vue";
const processing = ref(false);

router.on("start", () => (processing.value = true));
router.on("finish", () => (processing.value = false));
const notificationSound = new Audio(sound);
const cashSoundEffect = new Audio(cashSound);
const languageStore = useLanguageStore();
const notifications = ref([]);

const getLatestNotifications = async () => {
    await axios
        .get(route("notifications.get", usePage().props.auth.user.id))
        .then((response) => {
            notifications.value = response.data;
        });
};
const notifying = computed(() => {
    return notifications.value.filter((item) => item.read_at == null).length;
});
onMounted(() => {
    document.documentElement.setAttribute("dir", languageStore.direction);

    document.body.setAttribute(
        "class",
        "relative z-1 bg-whiten  text-base font-normal text-body ",
    );

    Echo.private("users." + usePage().props.auth.user.id).notification(
        (notification) => {
            cashSoundEffect.play();
            getLatestNotifications();
        },
    );
    getLatestNotifications();
});

EventBus.on("Notification_Read", (e) => {
    handleLink(e.notification_id);
});
EventBus.on("All_Notifications_Read", () => {
    getLatestNotifications();
});
const handleLink = async (notification_id) => {
    try {
        const notification = notifications.value.find(
            (item) => item.id === notification_id,
        );
        if (notification && notification.read_at == null) {
            await axios.patch(
                route("notifications.markAsRead", { notification_id }),
            );
            notification.read_at = new Date().toISOString();
        }
    } catch (error) {
        console.error("Failed to mark notification as read:", error);
    }
};
</script>

<template>
    <!-- ===== Page Wrapper Start ===== -->
    <div class="flex h-screen overflow-hidden container-3xl">
        <!-- ===== Sidebar Start ===== -->
        <SideBar />
        <!-- ===== Sidebar End ===== -->

        <!-- ===== Content Area Start ===== -->
        <div
            class="relative flex flex-1 flex-col overflow-y-auto overflow-x-hidden"
            scroll-region
        >
            <!-- ===== Header Start ===== -->
            <Header
                :notifications="notifications"
                :notifying="notifying"
                @handle-link="handleLink"
            />
            <!-- ===== Header End ===== -->

            <!-- ===== Main Content Start ===== -->
            <main>
                <div class="mx-auto max-w-screen-2xl p-4 md:p-6 2xl:p-10">
                    <slot></slot>
                </div>
            </main>
            <!-- ===== Main Content End ===== -->
        </div>
    </div>
    <!-- ===== Page Wrapper End ===== -->
    <Loader v-if="processing" />
</template>
