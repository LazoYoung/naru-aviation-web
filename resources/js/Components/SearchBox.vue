<script setup>
defineProps(["value"]);
defineEmits(["update:value"]);
</script>

<template>
    <input class="inline min-w-[20%]" type="search" placeholder="Search..." :value="value" @input="$emit('update:value', $event.target.value)" size="24">
</template>
