<script setup>
const props = defineProps(["editor"]);
</script>

<template>
    <div
        class="mt-1 flex items-center flex-wrap border-editor border-r border-l border-t rounded-tr-md rounded-tl-md text-black font-bold"
        v-if="props.editor"
    >
        <button
            type="button"
            @click="editor.chain().focus().toggleBold().run()"
            :disabled="!editor.can().chain().focus().toggleBold().run()"
            class="p-2"
            :class="{ 'bg-slate-400 text-white': editor.isActive('bold') }"
            title="Bold"
        >
            <i class="ri-bold"></i>
        </button>
        <button
            type="button"
            @click="editor.chain().focus().toggleItalic().run()"
            :disabled="!editor.can().chain().focus().toggleItalic().run()"
            class="p-2"
            :class="{ 'bg-slate-400 text-white': editor.isActive('italic') }"
        >
            <i class="ri-italic"></i>
        </button>
        <button
            type="button"
            @click="editor.chain().focus().toggleUnderline().run()"
            :disabled="!editor.can().chain().focus().toggleUnderline().run()"
            class="p-2"
            :class="{ 'bg-slate-400 text-white': editor.isActive('underline') }"
        >
            <i class="ri-underline"></i>
        </button>

        <button
            type="button"
            @click="editor.chain().focus().toggleHeading({ level: 3 }).run()"
            class="p-2"
            :class="{
                'bg-slate-400 text-white': editor.isActive('heading', {
                    level: 3,
                }),
            }"
        >
            <i class="ri-h-3"></i>
        </button>

        <button
            type="button"
            @click="editor.chain().focus().toggleBulletList().run()"
            class="p-2"
            :class="{
                'bg-slate-400 text-white': editor.isActive('bulletList'),
            }"
        >
            <i class="ri-list-unordered"></i>
        </button>
        <button
            type="button"
            @click="editor.chain().focus().toggleOrderedList().run()"
            class="p-2"
            :class="{
                'bg-slate-400 text-white': editor.isActive('orderedList'),
            }"
        >
            <i class="ri-list-ordered"></i>
        </button>
    </div>
</template>

<style>
.tiptap h1,
.tiptap h2,
.tiptap h3 {
    line-height: 1.5;
}
.tiptap h1 {
    font-size: 32px;
    margin-bottom: 0.5em;
}
.tiptap h2 {
    font-size: 25px;
    margin-bottom: 0.5em;
}
.tiptap h3 {
    font-size: 20px;
    margin-bottom: 0.5em;
}
.tiptap ol {
    list-style: decimal inside; /* Keep bullets inside the content area */
}
.tiptap ul {
    list-style: disc inside; /* Keep bullets inside the content area */
}
.tiptap ul,
.tiptap ol {
    margin: 0;
    padding: 0;
}
.tiptap ul li,
.tiptap ol li {
    margin: 0;
    padding: 0;
    display: list-item; /* Ensure it's treated as a proper list item */
}
.tiptap ul li p,
.tiptap ol li p {
    margin: 0; /* Remove margins from the <p> tags */
    display: inline;
}
</style>
