<script setup>
import Modal from "@/Components/Modal.vue";
import {reactive} from "vue";
import {usePage} from "@inertiajs/vue3";

const csrfToken = usePage().props.auth['csrf_token'];
const props = defineProps({
    show: {
        type: Boolean,
        default: false,
    }
});
const style = {
    input: "block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6",
    label: "block text-sm font-medium leading-6 text-gray-900",
    submit: "rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600",
    cancel: "text-sm font-semibold leading-6 text-gray-900",
};
const state = reactive({
    name: null,
    icao: null,
    latitude: null,
    longitude: null,
});
const emit = defineEmits(['update', 'close']);

async function onSubmit() {
    let url = new URL(route('airport.new'));
    let form = new FormData();

    form.append('name', state.name);
    form.append('icao', state.icao);
    form.append('latitude', state.latitude);
    form.append('longitude', state.longitude);

    let response = await fetch(url, {
        method: 'POST',
        body: form,
        headers: {
            'X-CSRF-Token': csrfToken
        }
    });

    if (response.ok) {
        emit('close');
        emit('update');
    } else {
        let text = await response.text();
        window.alert('Failed to process: ' + text);
    }
}
</script>

<template>
    <Modal :show="show" @close="$emit('close')">
        <div class="max-w-2xl mx-auto p-8 bg-white rounded-xl">
            <h1 class="text-xl font-semibold leading-7 text-gray-900">New Airport</h1>
            <p class="mt-1 text-sm leading-6 text-gray-600">Add a new airport in service!</p>

            <div class="mt-10 grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">
                <div class="sm:col-span-3">
                    <label for="name" :class="style.label">Airport name</label>
                    <div class="mt-2">
                        <input type="text" id="name" v-model="state.name" :class="style.input" required/>
                    </div>
                </div>
                <div class="sm:col-span-3">
                    <label for="icao" :class="style.label">ICAO code</label>
                    <div class="mt-2">
                        <input type="text" id="icao" v-model="state.icao" :class="style.input" required/>
                    </div>
                </div>
                <div class="sm:col-span-3">
                    <label for="latitude" :class="style.label">Latitude</label>
                    <div class="mt-2">
                        <input type="text" id="latitude" v-model="state.latitude" :class="style.input" required/>
                    </div>
                </div>
                <div class="sm:col-span-3">
                    <label for="longitude" :class="style.label">Longitude</label>
                    <div class="mt-2">
                        <input type="text" id="longitude" v-model="state.longitude" :class="style.input" required/>
                    </div>
                </div>
            </div>

            <div class="flex justify-end items-center gap-4 mt-8">
                <button type="button" @click="$emit('close')" :class="style.cancel">Cancel</button>
                <button type="submit" @click="onSubmit" :class="style.submit">Submit</button>
            </div>
        </div>
    </Modal>
</template>
