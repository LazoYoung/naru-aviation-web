<script setup>
</script>

<template>
    <a class="element link" :href="route('home')">
        <label><span class="name">Home</span></label>
    </a>
    <a class="element link" :href="route('pilot.dispatch.view')">
        <label><span class="name">Booking</span></label>
    </a>
    <div class="element dropdown">
        <label>
            <span class="name">Dashboard</span>
            <span class="arrow">&#x25BC;</span>
        </label>
        <div class="content">
            <a href='#'><label>Statistics</label></a>
            <a href='#'><label>Logbook</label></a>
            <a href='#'><label>Timetable</label></a>
        </div>
    </div>
</template>
