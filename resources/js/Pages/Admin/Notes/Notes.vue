<script setup>
import { ref } from "vue";
import Note from "./Note.vue";
import NoteFound from "@/js/Components/NotFound.vue";
const props = defineProps({
    notes: {
        type: Object,
        required: true,
    },
    scrollable: {
        type: Boolean,
        default: false,
    },
});
</script>
<template>
    <div
        v-if="notes.length"
        :class="{ 'lg:max-h-100 lg:overflow-y-auto': scrollable }"
    >
        <Note v-for="note in notes" :note="note" :key="note.id"></Note>
    </div>
    <NoteFound v-else message="There is no notes found"></NoteFound>
</template>
