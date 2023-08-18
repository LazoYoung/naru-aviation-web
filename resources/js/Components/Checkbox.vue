<script setup>
import {computed, ref} from 'vue';

const button = ref();
const input = ref();
const emit = defineEmits(['update:checked']);
const props = defineProps({
    name: {
        type: String,
        required: true,
    },
    checked: {
        type: [Array, Boolean],
        required: true,
    },
    value: {
        default: null,
    },
    label: {
        type: String,
        required: false,
    }
});
const proxyChecked = computed({
    get() {
        return props.checked;
    },

    set(val) {
        emit('update:checked', val);
    },
});

function onToggle() {
    let yes = "var(--form-input-bg)";
    let no = "var(--form-bg)";
    let checked = input.value.checked;
    button.value.style.backgroundColor = checked ? yes : no;
}
</script>

<template>
    <div ref="button" class="checkbtn"></div>
    <label v-html="label"></label>
    <input
        ref="input"
        :name="name"
        type="checkbox"
        :value="value"
        v-model="proxyChecked"
        @change="onToggle()"
    >
</template>

<style scoped>
input {
    position: absolute;
    top: 0;
    right: 0;
    width: 100%;
    height: 100%;
    cursor: pointer;
    background-color: unset;
    opacity: 0;
}
label {
    display: block;
    width: auto;
    min-height: 1.5rem;
    margin-left: 2rem;
    line-height: 1.5rem;
    text-align: left;
    font-weight: 500;
    color: var(--form-input-fg);
}
div.checkbtn {
    position: absolute;
    top: 0;
    left: 0;
    width: 1.5rem;
    height: 1.5rem;
    border-radius: 100%;
    border-width: 2px;
    border-color: var(--form-input-bg);
    background-color: var(--form-bg);
    transition: background-color 200ms ease-in-out;
}
</style>
