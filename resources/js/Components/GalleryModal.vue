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
    state.title = '';
    state.description = '';
    state.file = null;
    state.preview = null;
    emit('close');
}
</script>

<template>
    <Modal :show="show" @close="closeModal">
        <form @submit.prevent="submitForm">
            <div class="modal">
                <h1 class="text-xl font-semibold leading-7 text-gray-900">New Image</h1>
                <p class="mt-1 text-sm leading-6 text-white">Share anything for this community!</p>

                <div class="mt-10 grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">
                    <div class="sm:col-span-3">
                        <label for="title" class="text-black">Title</label>
                        <div class="mt-2">
                            <input type="text" id="title" v-model="state.title" class="text-black"/>
                        </div>
                    </div>

                    <div class="sm:col-span-3">
                        <label for="description" class="text-black">Description</label>
                        <div class="mt-2">
                            <input type="text" id="description" v-model="state.description" class="text-black w-full"/>
                        </div>
                    </div>

                    <div class="sm:col-span-3">
                        <label for="file" class="text-black">Image file</label>
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
                        <label for="img" class="text-black">Preview</label>
                        <img v-if="state.preview" alt="" id="img" :src="state.preview"/>
                        <p v-else class="mt-2">No image selected.</p>
                    </div>
                </div>

                <div class="flex justify-end items-center gap-4 mt-8">
                    <button small type="button" @click="closeModal">Cancel</button>
                    <button small filled type="submit">Submit</button>
                </div>
            </div>
        </form>
    </Modal>
</template>

<style scoped>
.modal {
    max-width: 42rem;
    margin-left: auto;
    margin-right: auto;
    padding: 2rem;
    border-radius: 0.75rem;
    background-color: var(--form-bg);
}
</style>