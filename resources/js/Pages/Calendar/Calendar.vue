<script setup>
import FullCalendar from "@fullcalendar/vue3";
import dayGridPlugin from "@fullcalendar/daygrid";
import interactionPlugin from "@fullcalendar/interaction"
import MainLayout from "@/Layouts/MainLayout.vue";
import {ref} from "vue";

let editMode = false;
const editIcon = 'fa-solid fa-pencil';
const viewIcon = 'fa-solid fa-eye';
const editText = 'Editing';
const viewText = 'Viewing';
const modeIcon = ref(viewIcon);
const modeText = ref(viewText);
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

function onDateClick() {
    if (!editMode || !props.admin) return;

    let confirm = window.confirm('Confirm you want to host a new event?');

    if (confirm) {
        location.href = route('calendar.form');
    }
}

function onModeClick() {
    editMode = !editMode;
    modeIcon.value = editMode ? editIcon : viewIcon;
    modeText.value = editMode ? editText : viewText;
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
                <i :class="modeIcon"></i>
                <span class="ms-2">{{ modeText }}</span>
            </button>
        </div>
    </MainLayout>
</template>
