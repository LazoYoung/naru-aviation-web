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
    spellcheck: {
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
    <form-textarea ref="target">
        <textarea
                ref="input"
                @input="$emit('update:modelValue', $event.target.value)"
                type="text"
                :value="modelValue"
                :required="required"
                :placeholder="hint"
                :autofocus="autofocus"
                :autocomplete="autocomplete"
                :spellcheck="spellcheck"
                :data-gramm="spellcheck"
                :readonly="readonly"
        ></textarea>
        <label class="btn-label" v-html="hint"></label>
        <form-input-border></form-input-border>
    </form-textarea>
</template>
