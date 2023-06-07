<script setup>
import {ref} from "vue";
import {usePage} from "@inertiajs/vue3";
import InkMde from "ink-mde/vue";

const csrfToken = usePage().props.auth['csrf_token'];
const props = defineProps(['thread']);
const style = {
    form: "fixed bottom-0 left-0 right-0 max-w-2xl mx-auto h-1/2 bg-gray-100 flex flex-col",
    title: "border border-neutral-500 p-2 px-3 w-96 resize-none",
    content: "w-full h-full resize-none",
    submit: "px-4 py-2 me-4 bg-neutral-600 text-white",
    close: "px-4 py-2 me-4 bg-amber-600 bg-opacity-70 text-white"
};
const content = ref('');
const emit = defineEmits(['close']);
const options = {
    files: {
        clipboard: true,
        dragAndDrop: true,
        handler: onFileUpload,
    },
    hooks: {
        afterUpdate: onContentChange,
    },
    interface: {
        spellcheck: false,
        toolbar: true,
    },
    placeholder: 'Write your post here...',
    toolbar: {
        upload: true,
    },
};

function submitForm() {
    let form = new FormData();
    let id = props.thread['id'];

    form.append('thread-id', id);
    form.append('content', content.value);
    fetch(route('forum.post.store'), {
        method: 'POST',
        headers: { 'X-CSRF-Token': csrfToken },
        body: form
    }).then(r => {
        if (r.ok) {
            emit('close');
        } else {
            window.alert(`Failed to process: ${r.statusText}`);
        }
    });
}

function onContentChange(doc) {
    form['content'] = doc;
}

async function onFileUpload(fileList) {
    if (isFileInvalid(fileList)) {
        return;
    }

    let formData = new FormData();
    formData.append('file', fileList.item(0));

    let response = await fetch(route('file.upload'), {
        method: 'POST',
        body: formData,
        headers: { 'X-CSRF-Token': csrfToken }
    });

    if (!response.ok) {
        let text = await response.text();
        window.alert(`Failed to upload: ${text}`);
        return;
    }

    let fileName = await response.text();
    content.value = content.value.concat(`![](${fileName})`);
}

function isFileInvalid(fileList) {
    if (fileList.length > 1) {
        window.alert('Too many files!');
        return true;
    }

    if (fileList.length < 1) {
        return true;
    }

    let mime = fileList.item(0).type;

    if (!mime.startsWith('image')) {
        window.alert('Unsupported file type!');
        return true;
    }

    return false;
}
</script>

<style>
#editor {
    --ink-block-background-color: #ffffff;
    --ink-code-background-color: #ffffff;
    background-color: white;
}
</style>

<template>
    <form @submit.prevent="submitForm" :class="style.form">
        <div class="flex-grow m-4">
            <InkMde id="editor" v-model="content" :options="options" :class="style.editor"></InkMde>
        </div>
        <div class="mx-4 mb-4 flex flex-row">
            <button type="submit" :class="style.submit">Save</button>
            <button @click="$emit('close')" :class="style.close">Close</button>
        </div>
    </form>
</template>
