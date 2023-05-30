<script setup>
import {ref} from "vue";
import {usePage} from "@inertiajs/vue3";

const csrfToken = usePage().props['csrf_token'];
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
</script>

<template>
    <form @submit.prevent="submitForm" :class="style.form">
        <div class="flex-grow m-4">
            <textarea v-model="content" :class="style.content" placeholder="Write your comment here..." required></textarea>
        </div>
        <div class="mx-4 mb-4 flex flex-row">
            <button type="submit" :class="style.submit">Save</button>
            <button @click="$emit('close')" :class="style.close">Close</button>
        </div>
    </form>
</template>
