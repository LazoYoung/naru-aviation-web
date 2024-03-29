<script setup>
import Modal from "@/Components/Modal.vue";
import {computed, reactive, watch} from "vue";
import InkMde from "ink-mde/vue";
import {usePage} from "@inertiajs/vue3";

const emit = defineEmits(['close', 'update']);
const csrfToken = usePage().props.auth['csrf_token'];
const props = defineProps({
    show: {
        type: Boolean
    },
    event: {
        type: Number,
        default: null
    },
    date: {
        type: String,
        default: ''
    }
});
const style = {
    input: "block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6",
    label: "block text-sm font-medium leading-6 text-gray-900",
    editor: "w-full h-full resize-none max-h-60 overflow-y-scroll",
    submit: "rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600",
    cancel: "text-sm font-semibold leading-6 text-gray-900",
};
const title = computed(() => {
    return isNewEvent() ? 'Event form' : 'New Event';
});
const subtitle = computed(() => {
    return isNewEvent() ? 'Rearrange the existing event.' : 'Submit a new event for the community.'
});
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
const state = reactive({
    title: '',
    start: '',
    end: '',
    description: '',
});

watch(() => props.date, (date) => {
    state.start = date + "T00:00";
    state.end = date + "T23:59";
});

async function submitForm() {
    if (!state.description) {
        window.alert('Content is empty!');
        return;
    }

    let link = isNewEvent() ? route('event.submit.new') : route('event.submit.update');
    let url = new URL(link);
    let form = new FormData();

    form.append('title', state.title);
    form.append('start', new Date(state.start).toISOString());
    form.append('end', new Date(state.end).toISOString());
    form.append('description', state.description);

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

function onContentChange(doc) {
    state.description = doc;
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
    state.description = state.description.concat(`![](${fileName})`);
}

function isNewEvent() {
    return !props.event;
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

function closeModal() {
    emit('close');
}
</script>

<template>
    <Modal :show="show" @close="closeModal">
        <form @submit.prevent="submitForm">
            <div class="max-w-2xl mx-auto p-8 bg-white rounded-xl">
                <h1 class="text-xl font-semibold leading-7 text-gray-900">{{title}}</h1>
                <p class="mt-1 text-sm leading-6 text-gray-600">{{subtitle}}</p>

                <div class="mt-10 grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">
                    <div class="sm:col-span-3">
                        <label for="title" :class="style.label">Title</label>
                        <div class="mt-2">
                            <input type="text" id="title" v-model="state.title" :class="style.input" required/>
                        </div>
                    </div>

                    <div class="sm:col-span-3 sm:col-start-1">
                        <label for="start" :class="style.label">Start date</label>
                        <div class="mt-2">
                            <input id="start" v-model="state.start" type="datetime-local" :class="style.input" required/>
                        </div>
                    </div>

                    <div class="sm:col-span-3">
                        <label for="end" :class="style.label">End date</label>
                        <div class="mt-2">
                            <input id="end" v-model="state.end" type="datetime-local" :class="style.input" required/>
                        </div>
                    </div>

                    <div class="col-span-full">
                        <label for="description" :class="style.label">Description</label>
                        <div class="mt-2">
                            <InkMde id="editor" :options="options" :class="style.editor"></InkMde>
                        </div>
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
