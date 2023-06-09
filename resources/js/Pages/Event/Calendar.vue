<script setup>
import {fetchJSON, fetchResponse} from "@/api.js";
import dayGridPlugin from "@fullcalendar/daygrid";
import interactionPlugin from "@fullcalendar/interaction"
import MainLayout from "@/Layouts/MainLayout.vue";
import {onMounted, reactive, ref} from "vue";
import {useForm, usePage} from "@inertiajs/vue3";
import FullCalendar from "@fullcalendar/vue3";
import CalendarModal from "@/Components/CalendarModal.vue";

let editMode = false;
const editIcon = 'fa-solid fa-pencil';
const viewIcon = 'fa-solid fa-eye';
const editText = 'Editing';
const viewText = 'Viewing';
const csrfToken = usePage().props.auth['csrf_token'];
const state = reactive({
    showModal: false,
    modeIcon: viewIcon,
    modeText: viewText,
});
const props = defineProps({
    admin: {
        type: Boolean,
        default: true // todo delete on production
    }
});
const calendarRef = ref();
const options = {
    plugins: [dayGridPlugin, interactionPlugin],
    initialView: 'dayGridMonth',
    headerToolbar: {
        start: 'prev',
        center: 'title',
        end: 'next'
    },
    displayEventTime: false,
    dateClick: onDateClick,
    eventClick: onEventClick,
};
const form = useForm({
    "name": null,
    "start-date": null,
    "end-date": null,
    "link": null,
});

onMounted(() => {
    fetchEvents();
});

function onDateClick() {
    if (!props.admin) return;

    if (window.confirm('Would you like to host a new event?')) {
        state.showModal = true;
    }
}

async function onEventClick(info) {
    if (!props.admin) return;

    let eventId = info.event.id;
    let url = new URL(route('event.thread'));
    url.searchParams.append('id', eventId);
    let response = await fetchResponse(url, csrfToken);

    if (!response.ok) {
        let message = await response.text();
        window.alert(message);
    } else if (response.redirected) {
        location.href = response.url;
    }
}

function onModeClick() {
    editMode = !editMode;
    state.modeIcon = editMode ? editIcon : viewIcon;
    state.modeText = editMode ? editText : viewText;
}

async function fetchEvents() {
    let json = await fetchJSON(route('event.fetch.all'), csrfToken);

    if (!json) return;

    let array = JSON.parse(json);
    let calendar = calendarRef.value.getApi();

    for (let key in array) {
        let elem = array[key];
        let event = {
            id: elem['id'],
            title: elem['title'],
            start: elem['start'],
            end: elem['end'],
        };
        calendar.addEvent(event);
    }
}
</script>

<template>
    <MainLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Calendar</h2>
        </template>
        <div class="max-w-2xl mx-auto my-8">
            <div class="mb-8">
                <FullCalendar ref="calendarRef" :options="options" />
            </div>
            <button v-if="admin" @click="onModeClick" class="inline-flex items-center px-4 py-2 bg-neutral-600 rounded-md text-white text-md">
                <i :class="state.modeIcon"></i>
                <span class="ms-2">{{ state.modeText }}</span>
            </button>
        </div>
        <CalendarModal :show="state.showModal" @close="state.showModal = false"></CalendarModal>
    </MainLayout>
</template>
