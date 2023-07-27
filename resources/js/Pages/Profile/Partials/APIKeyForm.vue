<script setup>
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import {onMounted, ref} from "vue";
import Alert from "@/alert.js";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import {usePage} from "@inertiajs/vue3";
import {fetchResponse} from "@/api.js";

const token = usePage().props.auth['csrf_token']
const inputValue = ref('');
const inputType = ref('text');
const keyExists = ref(false);
const processing = ref(true);
const successAlert = new Alert('success');
const failAlert = new Alert('fail');

onMounted(loadKey);

async function loadKey() {
    let response = await fetchResponse(route('acars.get'), token);

    if (!response.ok) {
        if (response.status === 404) {
            inputValue.value = 'Key not generated.';
            processing.value = false;
        } else {
            inputValue.value = 'Server error';
        }
        return;
    }

    inputValue.value = await response.text();
    inputType.value = 'password';
    processing.value = false;
    keyExists.value = true;
}

function revealKey() {
    inputType.value = 'text';
    navigator.clipboard.writeText(inputValue.value)
        .catch(() => failAlert.pop('Failed to access clipboard.'))
        .then(() => successAlert.pop('Copied to clipboard.'));
}

function resetKey() {
    fetch(route('acars.reset'), {
        method: 'POST',
        headers: {'X-CSRF-Token': token}
    }).catch(reason => {
        console.error(reason);
        processing.value = false;
        failAlert.pop('Failed to reset key!');
    }).then(response => response.text()
    ).then(key => {
        inputValue.value = key;
        keyExists.value = true;
        successAlert.pop('Key regenerated!');
    });
}
</script>

<template>
    <section class="space-y-6">
        <header>
            <h2 class="text-lg font-medium text-gray-900">ACARS Link</h2>

            <p class="mt-1 text-sm text-gray-600">
                Your <a href="https://github.com/LazoYoung/Naru-ACARS" target="_blank" class="underline font-bold">ACARS software</a>
                requires the API key to record your flight.
            </p>
        </header>

        <div class="mt-6">
            <InputLabel for="api-key" value="API Key" />

            <TextInput
                id="api-key"
                :type="inputType"
                :model-value="inputValue"
                @click="revealKey"
                class="mt-1 block w-full"
                model-value="test"
            />
        </div>

        <div class="flex items-center gap-4">
            <PrimaryButton v-if="keyExists" :disabled="processing" @click="resetKey">Reset</PrimaryButton>
            <PrimaryButton v-else :disabled="processing" @click="resetKey">Generate</PrimaryButton>
        </div>
    </section>
</template>
