<script setup>
import {router, usePage} from "@inertiajs/vue3";
import {reactive} from "vue";
import MainLayout from "@/Layouts/MainLayout.vue";
import TextInput from "@/Components/TextInput.vue";
import TextArea from "@/Components/TextArea.vue";
import Alert from "@/alert.js";

const token = usePage().props.auth['csrf_token'];
const url = route('pilot.dispatch.submit');
const alert = new Alert();
const form = reactive({
    callsign: '',      // 항공편명
    aircraft: '',      // 비행 기종
    origin: '',        // 출발 공항
    alternate: '',     // 회항 공항 (공란 가능)
    destination: '',   // 도착 공항
    off_block: '',     // 출발 날짜 및 시각 (ISO-8601 in UTC)
    flight_time: '',   // 비행 시간
    route: '',         // 비행 경로 (공란 가능)
    remarks: '',       // 비고 (공란 가능)
});

function loadSimbrief() {
    let url = "https://www.simbrief.com/api/xml.fetcher.php?username=lazoyoung&json=1"
    fetch(url)
        .catch(reason => {
            console.log(reason);
        })
        .then(r => r.json())
        .then(json => parseOFP(json));
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

function submit() {
    router.post(url, form, {
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
                            </div>

                            <div class="input">
                                <label for="off_block">Departure time</label>
                                <input
                                        v-model="form.off_block"
                                        id="off_block"
                                        type="datetime-local"
                                        required>
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
                            </div>

                            <div class="input">
                                <label for="alternate">Alternate airport *</label>
                                <TextInput
                                        v-model="form.alternate"
                                        id="alternate"
                                        hint="ICAO"
                                        autocomplete="none"
                                ></TextInput>
                            </div>

                            <div class="input">
                                <label for="route">Flight route</label>
                                <TextArea
                                        v-model="form.route"
                                        hint="Route"
                                        autocomplete="none"
                                ></TextArea>
                            </div>

                            <div class="input">
                                <label for="remarks">Remarks *</label>
                                <TextArea
                                        v-model="form.remarks"
                                        id="remarks"
                                        hint="Remarks"
                                        autocomplete="none"
                                ></TextArea>
                            </div>
                        </div>
                    </div>
                    <div id="actions">
                        <span class="text-lg">* These are optional fields</span>
                        <div class="flex-grow h-0"></div>
                        <button small @click.prevent="loadSimbrief()">Load Simbrief</button>
                        <button small filled>Submit</button>
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

input[type="datetime-local"] {
    color: var(--form-input-fg);
    background-color: var(--form-input-bg);
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
