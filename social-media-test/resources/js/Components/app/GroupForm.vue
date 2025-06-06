<script setup>

import InputTextarea from "@/Components/InputTextarea.vue";
import Checkbox from "@/Components/Checkbox.vue";
import TextInput from "@/Components/TextInput.vue";
import ClassicEditor from "@ckeditor/ckeditor5-build-classic";

defineProps({
    form: Object
})

const editor = ClassicEditor;
const editorConfig = {
    toolbar: ['bold', 'italic', '|', 'bulletedList', 'numberedList', '|', 'heading', '|', 'outdent', 'indent', '|', 'link', '|', 'blockQuote'],
}

</script>

<template>
    <div class="mb-3 dark:text-gray-100">
        <label>Group Name</label>
        <TextInput
            type="text"
            class="mt-1 block w-full"
            v-model="form.name"
            required
            autofocus
        />
    </div>


    <div class="mb-3 dark:text-gray-100">
        <label>About Group</label>
        <ckeditor :editor="editor" v-model="form.about" :config="editorConfig"></ckeditor>
    </div>
    <div class="mb-3 dark:text-gray-100">
    <label>Visibility</label>
    <select
        v-model="form.privacy"
        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm dark:bg-slate-900 dark:text-white"
    >
        <option value="public">Public</option>
        <option value="private">Private</option>
    </select>
</div>
</template>

<style scoped>

</style>