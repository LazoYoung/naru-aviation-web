<script setup>
import { useForm, usePage } from "@inertiajs/vue3";
import { onMounted, ref } from "vue";
import MainLayout from "@/Layouts/MainLayout.vue";
import TextInput from "@/Components/TextInput.vue";
import TextArea from "@/Components/TextArea.vue";
import Alert from "@/alert.js";
import { DateTime, TimeZone, TzDatabase } from "timezonecomplete";
import tzData from "tzdata/timezone-data.json";
import InputError from "@/Components/InputError.vue";
import DateTimeInput from "@/Components/DateTimeInput.vue";
import { fetchResponse } from "@/api.js";
import TimeInput from "@/Components/TimeInput.vue";

const user = usePage().props.auth["user"];
const token = usePage().props.auth["csrf_token"];
const url = route("pilot.dispatch.submit");
const alert = new Alert();
const loading = ref(false);
const off_block_date = ref();
const off_block_hour = ref();
const off_block_min = ref();
const block_hour = ref();
const block_min = ref();
const form = useForm({
    callsign: "",
    aircraft: "",
    origin: "",
    alternate: "",
    destination: "",
    off_block: "",
    block_time: "",
    route: "",
    remarks: "",
});

onMounted(() => {
    TzDatabase.init(tzData);
});

async function loadSimbrief() {
    let getResp = await fetchResponse(route("simbrief.get-id"));
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
    let dx_rmk = json["general"]["dx_rmk"];
    form.callsign = json["atc"]["callsign"];
    form.aircraft = json["aircraft"]["icao_code"];
    form.origin = json["origin"]["icao_code"];
    form.alternate = json["alternate"]["icao_code"];
    form.destination = json["destination"]["icao_code"];
    form.route = json["general"]["route"];
    form.remarks = Array.isArray(dx_rmk) ? dx_rmk.join("\n") : dx_rmk;
    form.off_block = getZuluTime(json["times"]["sched_out"]);
    let block = json["times"]["est_block"];
    let date = new Date(form.off_block);
    block_hour.value = Math.floor(block / 3600).toString().padStart(2, "0");
    block_min.value = (Math.floor(block / 60) % 60).toString().padStart(2, "0");
    off_block_date.value = getDate(json["times"]["sched_out"]);
    off_block_hour.value = date.getHours().toString().padStart(2, "0");
    off_block_min.value = date.getMinutes().toString().padStart(2, "0");
    updateOffBlock();
    updateBlockTime();
}

function getDate(seconds) {
    let zone = TimeZone.local();
    let dateTime = new DateTime(seconds * 1000, zone);
    return dateTime.format("yyyy-MM-dd");
}

function getZuluTime(seconds) {
    let zone = TimeZone.local();
    let dateTime = new DateTime(seconds * 1000, zone);
    return dateTime.format("yyyy-MM-ddTHH:mm");
}

function submit() {
    for (let key in form) {
        let value = form[key];

        if (typeof value === "string") {
            form[key] = value.toUpperCase();
        }
    }

    form.post(url, {
        onFinish: () => {
            form.reset("off_block");
        },
        onError: () => {
            alert.setType("error");
            alert.pop("Bad request!");
        },
        onSuccess: () => {
            alert.setType("success");
            alert.pop("Booking complete!");
        },
    });
}

function updateOffBlock() {
    let d = off_block_date.value;
    let h = off_block_hour.value;
    let m = off_block_min.value;

    if (d && h && m) {
        form.off_block = `${d}T${h}:${m}`;
        // console.log(form.off_block);
    }
}

function updateBlockTime() {
    let h = block_hour.value;
    let m = block_min.value;

    if (h && m) {
        form.block_time = parseInt(h) ? `${h}:${m}` : m;
        // console.log(form.block_time);
    }
}
</script>

<template>
    <Head title="Dispatch"></Head>

    <MainLayout :pilot="true">
        <template #header>
            <div class="max-w-5xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                <h2 class="font-semibold text-2xl text-white leading-tight">
                    Flight Booking
                </h2>
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
                                    uppercase
                                    required
                                ></TextInput>
                                <InputError
                                    class="mt-2"
                                    :message="form.errors.callsign"
                                ></InputError>
                            </div>

                            <div class="input">
                                <label for="aircraft">Aircraft type</label>
                                <TextInput
                                    v-model="form.aircraft"
                                    id="aircraft"
                                    hint="ICAO"
                                    autocomplete="none"
                                    uppercase
                                    required
                                ></TextInput>
                                <InputError
                                    class="mt-2"
                                    :message="form.errors.aircraft"
                                ></InputError>
                            </div>

                            <div class="input">
                                <label for="off_block">Departure time</label>
                                <div class="mock">
                                    <div class="child date">
                                        <input
                                            type="date"
                                            id="off_block_d"
                                            v-model="off_block_date"
                                            @input="updateOffBlock"
                                            required
                                        />
                                    </div>
                                    <div class="child time">
                                        <TimeInput
                                                v-model:hour="off_block_hour"
                                                v-model:min="off_block_min"
                                                suffix="Z"
                                                @update="updateOffBlock"
                                        ></TimeInput>
                                    </div>
                                    <div></div>
                                </div>
                                <DateTimeInput
                                    v-model="form.off_block"
                                    id="off_block"
                                    required
                                    tabindex="-1"
                                ></DateTimeInput>
                                <InputError
                                    class="mt-2"
                                    :message="form.errors.off_block"
                                ></InputError>
                            </div>

                            <div class="input">
                                <label for="block_time">Block time</label>
                                <div class="mock">
                                    <div class="child time">
                                        <TimeInput
                                                v-model:hour="block_hour"
                                                v-model:min="block_min"
                                                @update="updateBlockTime"
                                        ></TimeInput>
                                    </div>
                                </div>
                                <TextInput
                                    v-model="form.block_time"
                                    id="block_time"
                                    required
                                    tabindex="-1"
                                ></TextInput>
                                <InputError
                                    class="mt-2"
                                    :message="form.errors.block_time"
                                ></InputError>
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
                                    uppercase
                                    required
                                ></TextInput>
                                <InputError
                                    class="mt-2"
                                    :message="form.errors.origin"
                                ></InputError>
                            </div>

                            <div class="input">
                                <label for="destination">Arrival airport</label>
                                <TextInput
                                    v-model="form.destination"
                                    id="destination"
                                    hint="ICAO"
                                    autocomplete="none"
                                    uppercase
                                    required
                                ></TextInput>
                                <InputError
                                    class="mt-2"
                                    :message="form.errors.destination"
                                ></InputError>
                            </div>

                            <div class="input">
                                <label for="alternate"
                                    >Alternate airport *</label
                                >
                                <TextInput
                                    v-model="form.alternate"
                                    id="alternate"
                                    hint="ICAO"
                                    autocomplete="none"
                                    uppercase
                                ></TextInput>
                                <InputError
                                    class="mt-2"
                                    :message="form.errors.alternate"
                                ></InputError>
                            </div>

                            <div class="input">
                                <label for="route">Flight route</label>
                                <TextArea
                                    v-model="form.route"
                                    hint="Route"
                                    autocomplete="none"
                                    uppercase
                                ></TextArea>
                                <InputError
                                    class="mt-2"
                                    :message="form.errors.route"
                                ></InputError>
                            </div>

                            <div class="input">
                                <label for="remarks">Remarks *</label>
                                <TextArea
                                    v-model="form.remarks"
                                    id="remarks"
                                    hint="Remarks"
                                    autocomplete="none"
                                    uppercase
                                ></TextArea>
                                <InputError
                                    class="mt-2"
                                    :message="form.errors.remarks"
                                ></InputError>
                            </div>
                        </div>
                    </div>
                    <div id="actions">
                        <span class="text-md">* Optional fields</span>
                        <div class="flex-grow h-0"></div>
                        <button
                            small
                            @click.prevent="loadSimbrief()"
                            :disabled="loading"
                        >
                            Load Simbrief
                        </button>
                        <button small filled :disabled="form.processing">
                            Submit
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </MainLayout>
</template>

<style scoped>
input[type=date]:invalid,
::placeholder {
    color: var(--form-input-placeholder) !important;
}
input[type=date]::-webkit-calendar-picker-indicator {
    background-color: white;
    border-radius: 10%;
}
.mock {
    display: flex;
    background: rgb(61, 61, 61);
    border-radius: 3px;
    justify-content: space-between;
    overflow: hidden;
    padding: 15px 10px;
}
.mock input {
    background: rgb(61, 61, 61);
    padding: 0;
}
.mock > * > * {
    font-size: 13pt;
    font-weight: 500;
    font-family: "Pretendard Variable", "Malgun Gothic", sans-serif;
}
.mock .child {
    display: flex;
    align-items: stretch;
}

#off_block,
#block_time {
    height: 0;
    overflow: hidden;
}

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
    width: calc(50% - 2rem);
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

    div.form > .column {
        width: unset;
    }
}
</style>
