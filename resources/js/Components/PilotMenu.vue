<script setup>
</script>

<template>
    <a class="element link" :href="route('home')">
        <label>
            <span class="name">Home</span>
            <span class="arrow-link">&#x1F865;</span>
        </label>
    </a>
    <a class="element link" :href="route('pilot.dashboard')">
        <label><span class="name">Dashboard</span></label>
    </a>
    <a class="element link" :href="route('pilot.dispatch')">
        <label><span class="name">Booking</span></label>
    </a>
</template>