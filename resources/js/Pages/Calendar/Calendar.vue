<script setup>
import FullCalendar from "@fullcalendar/vue3";
import dayGridPlugin from "@fullcalendar/daygrid";
import interactionPlugin from "@fullcalendar/interaction"
import MainLayout from "@/Layouts/MainLayout.vue";

let options = {
    plugins: [dayGridPlugin, interactionPlugin],
    initialView: 'dayGridMonth'
};
</script>

<template>
    <MainLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Calendar</h2>
        </template>
        <div class="max-w-2xl mx-auto my-8">
            <FullCalendar :options="options" />
        </div>
    </MainLayout>
</template>
