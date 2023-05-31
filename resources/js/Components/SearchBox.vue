<script setup>
defineProps(["modelValue"]);
defineEmits(["update:modelValue"]);
</script>

<template>
    <input class="inline min-w-[20%]" type="search" placeholder="Search..." :value="modelValue" @input="$emit('update:modelValue', $event.target.value)" size="24">
</template>
