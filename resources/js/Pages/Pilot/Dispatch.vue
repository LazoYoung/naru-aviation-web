<script setup>
import {useForm, usePage} from "@inertiajs/vue3";
import {onMounted, ref} from "vue";
import MainLayout from "@/Layouts/MainLayout.vue";
import TextInput from "@/Components/TextInput.vue";
import TextArea from "@/Components/TextArea.vue";
import Alert from "@/alert.js";
import {DateTime, TimeZone, TzDatabase} from "timezonecomplete";
import tzData from "tzdata/timezone-data.json";
import InputError from "@/Components/InputError.vue";
import DateTimeInput from "@/Components/DateTimeInput.vue";
import {fetchResponse} from "@/api.js";

const user = usePage().props.auth['user'];
const token = usePage().props.auth['csrf_token'];
const url = route('pilot.dispatch.submit');
const alert = new Alert();
const loading = ref(false);
const form = useForm({
    callsign: '',
    aircraft: '',
    origin: '',
    alternate: '',
    destination: '',
    off_block: '',
    flight_time: '',
    route: '',
    remarks: '',
});

onMounted(() => {
    TzDatabase.init(tzData);
});

async function loadSimbrief() {
    let getResp = await fetchResponse(route('simbrief.get-id'));
    let username = await getResp.text();

    if (!username || !getResp.ok) {
        username = window.prompt("Please specify your Simbrief ID.");
    }

    let id = parseInt(username);
    let url;

    if (isNaN(id)) {
        url = `https://www.simbrief.com/api/xml.fetcher.php?username=${username}&json=1`;
    } else {
        url = `https://www.simbrief.com/api/xml.fetcher.php?userid=${id}&json=1`;
    }

    loading.value = true;
    let response = await fetch(url);

    if (!response.ok) {
        alert.setType("error");
        alert.pop("Failed to fetch!");
        loading.value = false;
        return;
    }

    let json = await response.json();
    parseOFP(json);
    alert.setType("success");
    alert.pop("Import complete.");
    loading.value = false;
}

function parseOFP(json) {
    let dx_rmk = json['general']['dx_rmk'];
    let block = json['times']['sched_block'];
    let hour = Math.floor(block / 3600);
    let min = Math.floor(block / 60) % 60;
    form.callsign = json['atc']['callsign'];
    form.aircraft = json['aircraft']['icao_code'];
    form.origin = json['origin']['icao_code'];
    form.alternate = json['alternate']['icao_code'];
    form.destination = json['destination']['icao_code'];
    form.flight_time = (hour > 0) ? `${hour}:${min}` : min;
    form.route = json['general']['route'];
    form.remarks = Array.isArray(dx_rmk) ? dx_rmk.join('\n') : dx_rmk;
}

function getZuluTime(localTime) {
    let zone = TimeZone.local();
    let dateTime = new DateTime(localTime, zone);
    let zuluTime = dateTime.convert(TimeZone.utc());
    return zuluTime.format("yyyy-MM-ddTHH:mm");
}

function submit() {
    form.off_block = getZuluTime(form.off_block);
    form.post(url, {
        onFinish: () => {
            form.reset('off_block');
        },
        onError: () => {
            alert.setType('error');
            alert.pop('Bad request!');
        },
        onSuccess: () => {
            alert.setType('success');
            alert.pop('Booking complete!');
        }
    });
}
</script>

<template>
    <Head title="Dispatch"></Head>

    <MainLayout :pilot="true">
        <template #header>
            <div class="max-w-5xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                <h2 class="font-semibold text-2xl text-white leading-tight">Flight Booking</h2>
            </div>
        </template>

        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="bg">
                <form @submit.prevent="submit()">
                    <div class="form">
                        <div class="column">
                            <div class="input">
                                <label for="callsign">Callsign</label>
                                <TextInput
                                        v-model="form.callsign"
                                        id="callsign"
                                        hint="NAR100"
                                        autocomplete="none"
                                        required
                                ></TextInput>
                                <InputError class="mt-2" :message="form.errors.callsign"></InputError>
                            </div>

                            <div class="input">
                                <label for="aircraft">Aircraft type</label>
                                <TextInput
                                        v-model="form.aircraft"
                                        id="aircraft"
                                        hint="ICAO"
                                        autocomplete="none"
                                        required
                                ></TextInput>
                                <InputError class="mt-2" :message="form.errors.aircraft"></InputError>
                            </div>

                            <div class="input">
                                <label for="off_block">Departure time</label>
                                <DateTimeInput
                                        v-model="form.off_block"
                                        id="off_block"
                                        required
                                ></DateTimeInput>
                                <InputError class="mt-2" :message="form.errors.off_block"></InputError>
                            </div>

                            <div class="input">
                                <label for="flight_time">Flight time</label>
                                <TextInput
                                        v-model="form.flight_time"
                                        id="flight_time"
                                        hint="hh:mm"
                                        autocomplete="none"
                                        required
                                ></TextInput>
                                <InputError class="mt-2" :message="form.errors.flight_time"></InputError>
                            </div>
                        </div>
                        <div class="column">
                            <div class="input">
                                <label for="origin">Departure airport</label>
                                <TextInput
                                        v-model="form.origin"
                                        id="origin"
                                        hint="ICAO"
                                        autocomplete="none"
                                        required
                                ></TextInput>
                                <InputError class="mt-2" :message="form.errors.origin"></InputError>
                            </div>

                            <div class="input">
                                <label for="destination">Arrival airport</label>
                                <TextInput
                                        v-model="form.destination"
                                        id="destination"
                                        hint="ICAO"
                                        autocomplete="none"
                                        required
                                ></TextInput>
                                <InputError class="mt-2" :message="form.errors.destination"></InputError>
                            </div>

                            <div class="input">
                                <label for="alternate">Alternate airport *</label>
                                <TextInput
                                        v-model="form.alternate"
                                        id="alternate"
                                        hint="ICAO"
                                        autocomplete="none"
                                ></TextInput>
                                <InputError class="mt-2" :message="form.errors.alternate"></InputError>
                            </div>

                            <div class="input">
                                <label for="route">Flight route</label>
                                <TextArea
                                        v-model="form.route"
                                        hint="Route"
                                        autocomplete="none"
                                ></TextArea>
                                <InputError class="mt-2" :message="form.errors.route"></InputError>
                            </div>

                            <div class="input">
                                <label for="remarks">Remarks *</label>
                                <TextArea
                                        v-model="form.remarks"
                                        id="remarks"
                                        hint="Remarks"
                                        autocomplete="none"
                                ></TextArea>
                                <InputError class="mt-2" :message="form.errors.remarks"></InputError>
                            </div>
                        </div>
                    </div>
                    <div id="actions">
                        <span class="text-lg">* These are optional fields</span>
                        <div class="flex-grow h-0"></div>
                        <button small @click.prevent="loadSimbrief()" :disabled="loading">Load Simbrief</button>
                        <button small filled :disabled="form.processing">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </MainLayout>
</template>

<style scoped>
#actions {
    display: flex;
    flex-direction: row;
    column-gap: 1.5rem;
    padding: 1rem;
}

div.input > label {
    margin-bottom: 4px;
}

label {
    font-size: medium;
    color: var(--form-input-label);
}

div.bg {
    padding: 1.5rem;
    background-color: var(--form-bg);
    border-radius: 0.5rem;
}

div.form {
    display: flex;
    flex-direction: row;
}

div.form > .column {
    display: flex;
    flex-direction: column;
    flex-wrap: wrap;
    flex-grow: 1;
    padding: 1rem;
    row-gap: 1.5rem;
}

div.form > .column > .input {
    display: flex;
    flex-direction: column;
}

@media (max-width: 640px) {
    div.bg {
        border-radius: 0;
    }

    div.form {
        flex-direction: column;
    }
}
</style>
