<script setup>
import TextInput from "@/Components/TextInput.vue";
import {onMounted, ref} from "vue";
import {usePage} from "@inertiajs/vue3";
import Alert from "@/alert.js";
import {fetchResponse} from "@/api.js";

const user = usePage().props.auth['user'];
const token = usePage().props.auth['csrf_token'];
let inputValue = ref('');
let processing = ref(false);
let alertSuccess = new Alert("success");
let alertFail = new Alert("error");

onMounted(() => {
    fetchId();
});

async function fetchId() {
    let url = route('simbrief.get-id');
    let response = await fetchResponse(url, token);

    if (response.ok) {
        console.log('test2')
        inputValue.value = await response.text();
    }
}

async function save() {
    let url;
    let id = parseInt(inputValue.value);

    if (isNaN(id)) {
        url = `https://www.simbrief.com/api/xml.fetcher.php?username=${inputValue.value}&json=1"`;
    } else {
        url = `https://www.simbrief.com/api/xml.fetcher.php?userid=${id}&json=1"`;
    }

    let response = await fetch(url);

    if (!response.ok) {
        alertFail.pop("Account not found!");
    } else {
        let url = route('simbrief.update-id');
        response = await fetch(`${url}?id=${inputValue.value}`, {
            headers: { 'X-CSRF-Token': token },
        });

        if (response.ok) {
            alertSuccess.pop("Simbrief account saved.");
        } else {
            alertFail.pop("Internal error!");
        }
    }
}
</script>

<template>
    <section class="space-y-6">
        <header>
            <h2 class="text-lg font-medium text-gray-900">Simbrief account</h2>

            <p class="mt-1 text-sm text-gray-600">
                Your <a href="https://www.simbrief.com/" target="_blank" class="underline font-bold">Simbrief account</a>
                is required to seamlessly import and dispatch flightplans.
            </p>
        </header>

        <div class="mt-6">
            <TextInput
                    id="simbrief-id"
                    hint="Simbrief ID or username"
                    v-model="inputValue"
            ></TextInput>
        </div>

        <div class="flex items-center gap-4">
            <button small filled :disabled="processing" @click="save">Save</button>
        </div>
    </section>
</template>
