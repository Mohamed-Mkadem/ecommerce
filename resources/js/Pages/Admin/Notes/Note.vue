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
    <div>
        <div class="items-start justify-between flex">
            <div class="flex items-center gap-3">
                <img
                    :src="`${$page.props.base_url}storage/${note.user.avatar}`"
                    class="w-13 rounded-full"
                />
                <div>
                    <p class="text-sky-800 text-lg font-semibold">
                        {{ `${note.user.first_name} ${note.user.last_name}` }}
                    </p>
                    <small class="text-neutral-500">
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
                    class="bg-slate-700 text-white text-xl rounded-md px-2 py-1 hover:bg-opacity-90"
                >
                    <i class="ri-edit-line"></i>
                </ModalLink>
                <button
                    @click="deleteNote(note)"
                    class="bg-red-500 text-white text-xl rounded-md px-2 py-1 hover:bg-opacity-90"
                >
                    <i class="ri-delete-bin-5-fill"></i>
                </button>
            </div>
        </div>
        <div class="mt-2 py-4 text-primary font-medium">
            {{ note.content }}
        </div>
    </div>
</template>
