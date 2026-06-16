<script setup>
import NotFound from "@/js/Components/NotFound.vue";
import NotificationItem from "@/js/Components/NotificationItem.vue";
import PageHeader from "@/js/Components/Admin/PageHeader.vue";
import Paginator from "@/js/Components/Paginator.vue";
import { useForm, router } from "@inertiajs/vue3";
import { watch, ref } from "vue";
import EventBus from "@/js/EventBus";
import { trans } from "laravel-vue-i18n";
import Swal from "sweetalert2";
const props = defineProps({
    notifications: { type: Object },
});
const gettingNotifications = ref(false);
const form = useForm({
    status: "all",
});
watch(
    () => form.status,
    () => {
        gettingNotifications.value = true;
        form.get(route("notifications.index"), {
            preserveState: true,
            preserveScroll: true,
            onFinish: () => {
                gettingNotifications.value = false;
            },
        });
    },
);

const handleLink = (url, notification_id) => {
    router.get(url);

    EventBus.emit("Notification_Read", { notification_id: notification_id });
};
function markAllAsRead() {
    Swal.fire({
        title: trans("Dialog.title"),
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#6b7280",
        confirmButtonText: trans("Yes"),
        cancelButtonText: trans("Dialog.cancelButtonText"),
        width: 450,
    }).then((result) => {
        if (result.isConfirmed) {
            form.patch(route("notifications.readAll"), {
                onSuccess: () => {
                    Swal.fire({
                        title: trans("Done"),
                        text: trans("Notifications.allRead_successfully"),
                        icon: "success",
                        confirmButtonText: trans("OK"),
                    });
                    EventBus.emit("All_Notifications_Read");
                },
            });
        }
    });
}
</script>

<template>
    <Head :title="$t('Notifications')" />
    <PageHeader :page-title="$t('Notifications')">
        <button
            @click="markAllAsRead"
            class="bg-slate-800 text-white px-3 py-2 rounded-md hover:bg-opacity-75"
        >
            {{ $t("Mark all as read") }}
        </button>
    </PageHeader>
    <section class="max-w-screen-sm mx-auto">
        <div class="bg-white py-6 px-4">
            <form @submit.prevent="">
                <ul class="grid w-full gap-3 sm:grid-cols-3 mb-6">
                    <li class="w-full">
                        <input
                            type="radio"
                            id="all"
                            v-model="form.status"
                            value="all"
                            class="hidden peer"
                            required
                        />
                        <label
                            for="all"
                            class="w-full px-3 py-2 block text-center bg-slate-500 border border-neutral-200 rounded-lg cursor-pointer text-zinc-100 peer-checked:bg-slate-700 hover:bg-slate-600 text-lg font-medium"
                        >
                            {{ $t("All") }}
                        </label>
                    </li>
                    <li class="w-full">
                        <input
                            type="radio"
                            id="read"
                            v-model="form.status"
                            value="read"
                            class="hidden peer"
                            required
                        />
                        <label
                            for="read"
                            class="w-full px-3 py-2 block text-center bg-slate-500 border border-neutral-200 rounded-lg cursor-pointer text-zinc-100 peer-checked:bg-slate-700 hover:bg-slate-600 text-lg font-medium"
                        >
                            {{ $t("Read") }}
                        </label>
                    </li>
                    <li class="w-full">
                        <input
                            type="radio"
                            id="unread"
                            v-model="form.status"
                            value="unread"
                            class="hidden peer"
                            required
                        />
                        <label
                            for="unread"
                            class="w-full px-3 py-2 block text-center bg-slate-500 border border-neutral-200 rounded-lg cursor-pointer text-zinc-100 peer-checked:bg-slate-700 hover:bg-slate-600 text-lg font-medium"
                        >
                            {{ $t("Unread") }}
                        </label>
                    </li>
                </ul>
            </form>
            <div v-if="gettingNotifications" class="text-center">
                <p class="mb-2 font-semibold text-lg">
                    {{ $t("Getting Notifications") }}
                </p>
                <div role="status" class="flex justify-center">
                    <svg
                        aria-hidden="true"
                        class="w-8 h-8 text-gray-200 animate-spin fill-blue-600"
                        viewBox="0 0 100 101"
                        fill="none"
                        xmlns="http://www.w3.org/2000/svg"
                    >
                        <path
                            d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z"
                            fill="currentColor"
                        />
                        <path
                            d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z"
                            fill="currentFill"
                        />
                    </svg>
                    <span class="sr-only">{{ $t("Loading...") }}</span>
                </div>
            </div>
            <div v-if="!gettingNotifications">
                <ul v-if="notifications.data.length > 0">
                    <li
                        v-for="notification in notifications.data"
                        :key="notification.id"
                    >
                        <NotificationItem
                            :notification="notification"
                            @click="
                                handleLink(
                                    notification.data.url,
                                    notification.id,
                                )
                            "
                            class="mb-4 !border rounded bg-zinc-50"
                        />
                    </li>
                </ul>
                <NotFound v-else />
            </div>
        </div>
    </section>
    <div class="flex justify-center">
        <Paginator
            :links="notifications.links"
            :previous="notifications.prev_page_url"
            :next="notifications.next_page_url"
            class="mt-4"
        />
    </div>
</template>
