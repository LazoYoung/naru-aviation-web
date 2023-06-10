<script setup>
import Modal from "@/Components/Modal.vue";
import {reactive} from "vue";
import {usePage} from "@inertiajs/vue3";

const emit = defineEmits(['close', 'update']);
const props = defineProps({
    show: {
        type: Boolean,
        default: false,
    }
});
const csrfToken = usePage().props.auth['csrf_token'];
const style = {
    input: "block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6",
    label: "block text-sm font-medium leading-6 text-gray-900",
    submit: "rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600",
    cancel: "text-sm font-semibold leading-6 text-gray-900",
};
const state = reactive({
    title: '',
    description: '',
    file: null,
    preview: null,
});

async function submitForm() {
    let url = new URL(route('image.submit.new'));
    let form = new FormData();

    form.append('title', state.title);
    form.append('description', state.description);
    form.append('file', state.file);

    let response = await fetch(url, {
        method: 'POST',
        body: form,
        headers: {'X-CSRF-Token': csrfToken},
    });

    if (response.ok) {
        emit('update');
        closeModal();
    } else {
        let text = await response.text();
        window.alert(text);
    }
}

function onFileChange(event) {
    let files = event.target.files;

    if (!files || files.length !== 1) return;

    state.file = files[0];
    state.preview = URL.createObjectURL(state.file);
}

function closeModal() {
    emit('close');
}
</script>

<template>
    <Modal :show="show" @close="closeModal">
        <form @submit.prevent="submitForm">
            <div class="max-w-2xl mx-auto p-8 bg-white rounded-xl">
                <h1 class="text-xl font-semibold leading-7 text-gray-900">New Image</h1>
                <p class="mt-1 text-sm leading-6 text-gray-600">Share with people your flight experience!</p>

                <div class="mt-10 grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">
                    <div class="sm:col-span-3">
                        <label for="title" :class="style.label">Title</label>
                        <div class="mt-2">
                            <input type="text" id="title" v-model="state.title" :class="style.input"/>
                        </div>
                    </div>

                    <div class="sm:col-span-3">
                        <label for="description" :class="style.label">Description</label>
                        <div class="mt-2">
                            <input type="text" id="description" v-model="state.description" :class="style.input"/>
                        </div>
                    </div>

                    <div class="sm:col-span-3">
                        <label for="file" :class="style.label">Image file</label>
                        <div class="mt-2">
                            <input
                                type="file"
                                accept="image/*"
                                id="file"
                                @change="onFileChange($event)"
                                required
                            >
                        </div>
                    </div>
                    <div class="sm:col-span-3">
                        <label for="img" :class="style.label">Preview</label>
                        <img v-if="state.preview" alt="" id="img" :src="state.preview"/>
                        <p v-else>No image selected.</p>
                    </div>
                </div>

                <div class="flex justify-end items-center gap-4 mt-8">
                    <button type="button" @click="closeModal" :class="style.cancel">Cancel</button>
                    <button type="submit" :class="style.submit">Submit</button>
                </div>
            </div>
        </form>
    </Modal>
</template>
