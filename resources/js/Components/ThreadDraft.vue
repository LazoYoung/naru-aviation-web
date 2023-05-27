<script setup>
import CategorySelect from "@/Components/CategorySelect.vue";
import {useForm} from "@inertiajs/vue3";

const style = {
    form: "fixed bottom-0 left-0 right-0 max-w-2xl mx-auto h-1/2 bg-gray-100 flex flex-col",
    title: "border border-neutral-500 p-2 px-3 w-96 resize-none",
    content: "w-full h-full resize-none",
    submit: "px-4 py-2 me-4 bg-neutral-600 text-white",
    close: "px-4 py-2 me-4 bg-amber-600 bg-opacity-70 text-white"
};
const form = useForm({
    "title": null,
    "category": null,
    "content": null
});

function submitForm() {
    form.post(route("forum.thread.store"), {
        onError: (errors) => {
            for (let key in errors) {
                window.alert(errors[key]);
            }
        }
    })
}

defineEmits(["closed"]);
</script>

<template>
    <form @submit.prevent="submitForm" :class="style.form">
        <div class="flex flex-row justify-between m-4 mb-0">
            <input v-model="form.title" :class="style.title" maxlength="64" placeholder="Title" required>
            <CategorySelect :allow-all="false" v-model="form.category" :required="true" />
        </div>
        <div class="flex-grow m-4">
            <textarea v-model="form.content" :class="style.content" placeholder="Write your content here..." required></textarea>
        </div>
        <div class="mx-4 mb-4 flex flex-row">
            <button type="submit" :class="style.submit">Save</button>
            <button @click="$emit('closed')" :class="style.close">Close</button>
        </div>
    </form>
</template>
