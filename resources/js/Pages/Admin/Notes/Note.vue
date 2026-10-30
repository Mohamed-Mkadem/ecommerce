<script setup>
import Swal from "sweetalert2";
import { trans } from "laravel-vue-i18n";
import { router } from "@inertiajs/vue3";
const props = defineProps({
    note: {
        type: Object,
        required: true,
    },
});
function deleteNote(note) {
    Swal.fire({
        title: trans("Dialog.title"),
        text: trans("Dialog.warning"),
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#d33",
        cancelButtonColor: "#3085d6",
        confirmButtonText: trans("Dialog.confirmDeletionButtonText"),
        cancelButtonText: trans("Dialog.cancelButtonText"),
        width: 450,
    }).then((result) => {
        if (result.isConfirmed) {
            router.delete(route("notes.destroy", note), {
                preserveScroll: true,
                onSuccess: () => {
                    Swal.fire({
                        title: trans("Dialog.deletedTitle"),
                        text: trans("Note.deleted"),
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
    <div class="mb-4">
        <div class="items-start justify-between flex">
            <div class="flex items-center gap-3">
                <img
                    :src="`${$page.props.base_url}storage/${note.user.avatar}`"
                    class="w-13 rounded-full"
                />
                <div>
                    <p
                        class="text-lg font-semibold text-slate-800 dark:text-neutral-100"
                    >
                        {{ `${note.user.first_name} ${note.user.last_name}` }}
                    </p>
                    <small class="text-sm text-slate-500 dark:text-neutral-400">
                        {{ note.created_at }}
                    </small>
                </div>
            </div>
            <div
                v-if="
                    $page.props.auth.user.role == 'admin' ||
                    $page.props.auth.user.id == note.user.id
                "
                class="items-center gap-2 flex"
            >
                <ModalLink
                    :href="route('notes.edit', note)"
                    class="rounded-lg bg-slate-600 px-2 py-1 text-xl text-white transition hover:bg-slate-700 dark:bg-neutral-700 dark:hover:bg-neutral-600"
                >
                    <i class="ri-edit-line"></i>
                </ModalLink>
                <button
                    @click="deleteNote(note)"
                    class="rounded-lg bg-red-600 px-2 py-1 text-xl text-white transition hover:bg-red-700"
                >
                    <i class="ri-delete-bin-5-fill"></i>
                </button>
            </div>
        </div>
        <div
            class="mt-2 border-b border-slate-100 py-4 font-medium text-slate-700 dark:border-neutral-700 dark:text-neutral-200"
        >
            {{ note.content }}
        </div>
    </div>
</template>
