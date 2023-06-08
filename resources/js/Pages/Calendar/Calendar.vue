<script setup>
import FullCalendar from "@fullcalendar/vue3";
import dayGridPlugin from "@fullcalendar/daygrid";
import interactionPlugin from "@fullcalendar/interaction"
import MainLayout from "@/Layouts/MainLayout.vue";
import {reactive} from "vue";
import {useForm} from "@inertiajs/vue3";
import CalendarModal from "@/Components/CalendarModal.vue";

let editMode = false;
const editIcon = 'fa-solid fa-pencil';
const viewIcon = 'fa-solid fa-eye';
const editText = 'Editing';
const viewText = 'Viewing';
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
const options = {
    plugins: [dayGridPlugin, interactionPlugin],
    initialView: 'dayGridMonth',
    headerToolbar: {
        start: 'prev',
        center: 'title',
        end: 'next'
    },
    dateClick: onDateClick
};
const form = useForm({
    "name": null,
    "start-date": null,
    "end-date": null,
    "link": null,
});

function onDateClick() {
    if (!editMode || !props.admin) return;

    if (window.confirm('Confirm you want to host a new event?')) {
        state.showModal = true;
    }
}

function onModeClick() {
    editMode = !editMode;
    state.modeIcon = editMode ? editIcon : viewIcon;
    state.modeText = editMode ? editText : viewText;
}
</script>

<template>
    <MainLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Calendar</h2>
        </template>
        <div class="max-w-2xl mx-auto my-8">
            <div class="mb-8">
                <FullCalendar :options="options" />
            </div>
            <button v-if="admin" @click="onModeClick" class="inline-flex items-center px-4 py-2 bg-neutral-600 rounded-md text-white text-md">
                <i :class="state.modeIcon"></i>
                <span class="ms-2">{{ state.modeText }}</span>
            </button>
        </div>
        <CalendarModal :show="state.showModal" @close="state.showModal = false"></CalendarModal>
    </MainLayout>
</template>
