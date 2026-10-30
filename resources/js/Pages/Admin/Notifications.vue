<script setup>
import NotFound from "@/js/Components/NotFound.vue";
import NotificationItem from "@/js/Components/NotificationItem.vue";
import PageHeader from "@/js/Components/Admin/PageHeader.vue";
import Paginator from "@/js/Components/Paginator.vue";
import ResultsCounter from "@/js/Components/Admin/ResultsCounter.vue";
import { useForm, router, usePage } from "@inertiajs/vue3";
import { watch, ref } from "vue";
import EventBus from "@/js/EventBus";
import { trans } from "laravel-vue-i18n";
import Swal from "sweetalert2";
const props = defineProps({
    notifications: { type: Object },
});
const isAdmin = usePage().props.auth.user.role === "admin";
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

function cleanNotifications() {
    Swal.fire({
        title: trans("Dialog.title"),
        text: trans("Cleanup.notifications.confirm"),
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#d33",
        cancelButtonColor: "#6b7280",
        confirmButtonText: trans("Cleanup.confirm"),
        cancelButtonText: trans("Dialog.cancelButtonText"),
        width: 450,
    }).then((result) => {
        if (result.isConfirmed) {
            router.delete(route("notifications.clean"), {
                onSuccess: () => {
                    Swal.fire({
                        title: trans("Done"),
                        text: trans("Cleanup.notifications.success"),
                        icon: "success",
                        confirmButtonText: trans("OK"),
                    });
                },
            });
        }
    });
}
</script>

<template>
    <Head :title="$t('Notifications')" />
    <PageHeader :page-title="$t('Notifications')">
        <div class="flex flex-wrap gap-2">
            <button
                v-if="isAdmin"
                @click="cleanNotifications"
                class="inline-flex items-center justify-center gap-2 rounded-xl border border-red-600 bg-red-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-red-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-red-500/50"
            >
                <i class="ri-delete-bin-line" aria-hidden="true"></i>
                {{ $t("Cleanup.notifications.button") }}
            </button>
            <button
                @click="markAllAsRead"
                class="inline-flex items-center justify-center gap-2 rounded-xl border border-teal-700 bg-teal-700 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:border-teal-800 hover:bg-teal-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-teal-500/40"
            >
                <i class="ri-mail-check-line" aria-hidden="true"></i>
                {{ $t("Mark all as read") }}
            </button>
        </div>
    </PageHeader>

    <section
        class="mx-auto max-w-3xl rounded-2xl border border-slate-200 bg-white p-4 shadow-sm dark:border-neutral-700 dark:bg-neutral-800 dark:shadow-black/20 sm:p-6"
    >
        <ResultsCounter
            :count="notifications.total"
            label="Notifications"
            icon="ri-notification-3-line"
        />
        <form @submit.prevent="" class="mb-5">
            <fieldset>
                <legend
                    class="mb-3 text-sm font-semibold text-slate-700 dark:text-neutral-200"
                >
                    {{ $t("Filter.title") }}
                </legend>
                <ul class="grid w-full gap-2 sm:grid-cols-3">
                    <li>
                        <input
                            type="radio"
                            id="all"
                            v-model="form.status"
                            value="all"
                            class="peer sr-only"
                            required
                        />
                        <label
                            for="all"
                            class="block cursor-pointer rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-center text-sm font-semibold text-slate-600 transition hover:bg-slate-100 peer-checked:border-teal-700 peer-checked:bg-teal-700 peer-checked:text-white dark:border-neutral-700 dark:bg-neutral-900/40 dark:text-neutral-300 dark:hover:bg-neutral-700 dark:peer-checked:border-teal-700 dark:peer-checked:bg-teal-700 dark:peer-checked:text-white"
                            >{{ $t("All") }}</label
                        >
                    </li>
                    <li>
                        <input
                            type="radio"
                            id="read"
                            v-model="form.status"
                            value="read"
                            class="peer sr-only"
                            required
                        />
                        <label
                            for="read"
                            class="block cursor-pointer rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-center text-sm font-semibold text-slate-600 transition hover:bg-slate-100 peer-checked:border-teal-700 peer-checked:bg-teal-700 peer-checked:text-white dark:border-neutral-700 dark:bg-neutral-900/40 dark:text-neutral-300 dark:hover:bg-neutral-700 dark:peer-checked:border-teal-700 dark:peer-checked:bg-teal-700 dark:peer-checked:text-white"
                            >{{ $t("Read") }}</label
                        >
                    </li>
                    <li>
                        <input
                            type="radio"
                            id="unread"
                            v-model="form.status"
                            value="unread"
                            class="peer sr-only"
                            required
                        />
                        <label
                            for="unread"
                            class="block cursor-pointer rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-center text-sm font-semibold text-slate-600 transition hover:bg-slate-100 peer-checked:border-teal-700 peer-checked:bg-teal-700 peer-checked:text-white dark:border-neutral-700 dark:bg-neutral-900/40 dark:text-neutral-300 dark:hover:bg-neutral-700 dark:peer-checked:border-teal-700 dark:peer-checked:bg-teal-700 dark:peer-checked:text-white"
                            >{{ $t("Unread") }}</label
                        >
                    </li>
                </ul>
            </fieldset>
        </form>

        <div
            v-if="gettingNotifications"
            class="flex flex-col items-center justify-center gap-3 py-10 text-center text-slate-600 dark:text-neutral-300"
            role="status"
        >
            <span
                class="h-8 w-8 animate-spin rounded-full border-4 border-slate-200 border-t-teal-700 dark:border-neutral-600 dark:border-t-teal-400"
                aria-hidden="true"
            ></span>
            <p class="font-medium">{{ $t("Getting Notifications") }}</p>
            <span class="sr-only">{{ $t("Loading...") }}</span>
        </div>

        <ul v-else-if="notifications.data.length > 0" class="space-y-3">
            <li
                v-for="notification in notifications.data"
                :key="notification.id"
            >
                <NotificationItem
                    :notification="notification"
                    @click="handleLink(notification.data.url, notification.id)"
                />
            </li>
        </ul>
        <NotFound v-else />
    </section>

    <Paginator
        variant="admin"
        :links="notifications.links"
        :previous="notifications.prev_page_url"
        :next="notifications.next_page_url"
        class="mt-4 justify-center"
    />
</template>
