<script setup>
defineProps(['query']);
defineEmits(['update:query']);
</script>

<template>
    <div class="flex">
        <input type="search" placeholder="Search..." :value="query" @input="$emit('update:query', $event.target.value)" size="24">
    </div>
</template>
