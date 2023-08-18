<script setup>
defineProps(["modelValue"]);
defineEmits(["update:modelValue"]);
</script>

<template>
    <input class="inline w-full bg-black" type="search" placeholder="Search..." :value="modelValue" @input="$emit('update:modelValue', $event.target.value)" size="24">
</template>
