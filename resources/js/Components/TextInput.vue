<script setup>
import { onMounted, ref } from 'vue';

const props = defineProps({
    hint: {
        type: String,
        required: false,
    },
    label: {
        type: String,
        required: false,
    },
    type: {
        type: String,
        default: "text",
    },
    required: {
        type: Boolean,
        default: false,
    },
    autofocus: {
        type: Boolean,
        default: false,
    },
    autocomplete: {
        type: String,
        default: "off",
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
    input.value.addEventListener('focusin', onFocusIn);
    input.value.addEventListener('focusout', onFocusOut);
});

defineExpose({ focus: () => input.value.focus() });

function onFocusIn() {
    target.value.setAttribute('label', props.label);
}

function onFocusOut() {
    target.value.removeAttribute('label');
}
</script>

<template>
    <form-input-text ref="target">
        <input
            ref="input"
            @input="$emit('update:modelValue', $event.target.value)"
            :type="type"
            :value="modelValue"
            :required="required"
            :placeholder="hint"
            :autofocus="autofocus"
            :autocomplete="autocomplete"
            :readonly="readonly"
        />
        <label class="btn-label" v-html="hint"></label>
        <form-input-border></form-input-border>
    </form-input-text>
</template>
