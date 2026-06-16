<script setup>
import { useEditor, EditorContent } from "@tiptap/vue-3";
import StarterKit from "@tiptap/starter-kit";
import Buttons from "@/js/Components/Editor/Buttons.vue";
import Underline from "@tiptap/extension-underline";
import { Paragraph } from "@tiptap/extension-paragraph";
import { watch } from "vue";
const CustomParagraph = Paragraph.extend({
    content: "inline*",
});
const props = defineProps({
    modelValue: {
        type: String,
        required: true,
    },
    label: {
        type: String,
        required: true,
    },
});
const emit = defineEmits(["update:modelValue"]);
const editor = useEditor({
    content: props.modelValue,
    onUpdate: ({ editor }) => {
        emit("update:modelValue", editor.getHTML());
    },

    editorProps: {
        attributes: {
            class: "border border-editor p-4  min-h-[12rem] max-h-[12rem] overflow-y-auto rounded-bl-md rounded-br-md shadow-sm   text-black outline-none",
        },
    },
    extensions: [CustomParagraph, StarterKit, Underline],
});
watch(
    () => props.modelValue,
    (newValue) => {
        if (editor.value && editor.value.getHTML() !== newValue) {
            editor.value.commands.setContent(newValue || "");
        }
    },
);
</script>

<template>
    <Buttons :editor="editor"></Buttons>
    <EditorContent :editor="editor" />
</template>
