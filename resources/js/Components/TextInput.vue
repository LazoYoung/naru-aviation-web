<script setup>
import { onMounted, ref } from 'vue';

defineProps({
    label: {
        type: String,
        required: false,
    },
    labelType: {
        type: String,
        default: "inner",
    },
    type: {
        type: String,
        default: "text",
    },
    required: {
        type: Boolean,
        default: false,
    },
    placeholder: {
        type: String,
        required: false,
    },
    autofocus: {
        type: Boolean,
        default: false,
    },
    autocomplete: {
        type: String,
        default: "off",
    },
    modelValue: {
        type: String,
        required: true,
    },
});

defineEmits(['update:modelValue']);

const input = ref(null);

onMounted(() => {
    if (input.value.hasAttribute('autofocus')) {
        input.value.focus();
    }
});

defineExpose({ focus: () => input.value.focus() });
</script>

<template>
    <form-input-text :label="labelType">
        <input
            ref="input"
            @input="$emit('update:modelValue', $event.target.value)"
            :type="type"
            :value="modelValue"
            :required="required"
            :placeholder="placeholder"
            :autofocus="autofocus"
            :autocomplete="autocomplete"
        />
        <label v-html="label"></label>
        <form-input-border></form-input-border>
    </form-input-text>
</template>
