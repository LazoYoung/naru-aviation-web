<script setup>
import { onMounted, ref } from 'vue';

const props = defineProps({
    required: {
        type: Boolean,
        default: false,
    },
    autofocus: {
        type: Boolean,
        default: false,
    },
    readonly: {
        type: Boolean,
        default: false,
    },
    modelValue: {
        type: String,
        required: true,
    },
});
const input = ref(null);
const target = ref(null);
defineEmits(['update:modelValue']);

onMounted(() => {
    if (input.value.hasAttribute('autofocus')) {
        input.value.focus();
    }
});

defineExpose({ focus: () => input.value.focus() });
</script>

<template>
    <form-datetime ref="target">
        <input
            @input="$emit('update:modelValue', $event.target.value)"
            ref="input"
            type="datetime-local"
            :value="modelValue"
            :required="required"
            :autofocus="autofocus"
            :readonly="readonly"
        />
        <form-input-border></form-input-border>
    </form-datetime>
</template>

<style scoped>
input[type="datetime-local"] {
    color: var(--form-input-fg);
    background-color: var(--form-input-bg);
}

input[type="datetime-local"]::-webkit-calendar-picker-indicator {
    background-image: url("data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSIxNiIgaGVpZ2h0PSIxNSIgdmlld0JveD0iMCAwIDI0IDI0Ij48cGF0aCBmaWxsPSIjZmZmZmZmIiBkPSJNMjAgM2gtMVYxaC0ydjJIN1YxSDV2Mkg0Yy0xLjEgMC0yIC45LTIgMnYxNmMwIDEuMS45IDIgMiAyaDE2YzEuMSAwIDItLjkgMi0yVjVjMC0xLjEtLjktMi0yLTJ6bTAgMThINFY4aDE2djEzeiIvPjxwYXRoIGZpbGw9Im5vbmUiIGQ9Ik0wIDBoMjR2MjRIMHoiLz48L3N2Zz4=");
}
</style>