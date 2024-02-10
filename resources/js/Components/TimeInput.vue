<script setup>

// const props = defineProps(["hour", "min", "suffix"]);
const props = defineProps(['suffix']);
const hour = defineModel('hour');
const min = defineModel('min');
const emit = defineEmits(['update']);

function onUpdate() {
    let h = hour.value;
    let m = min.value;

    if (h && m) {
        hour.value = h.toString().padStart(2, "0");
        min.value = m.toString().padStart(2, "0");
    }
    emit("update");
}
</script>

<template>
    <div class="block">
        <input
                class="num"
                type="number"
                placeholder="HH"
                min="0"
                max="24"
                id="off_block_h"
                v-model="hour"
                @input="onUpdate"
        />
        <span>:</span>
        <input
                class="num"
                type="number"
                placeholder="MM"
                min="0"
                max="60"
                id="off_block_m"
                v-model="min"
                @input="onUpdate"
        />
        <span v-if="$props.suffix">{{suffix}}</span>
    </div>
</template>

<style scoped>
input[type=date]:invalid,
::placeholder {
    color: var(--form-input-placeholder) !important;
}
.block {
    display: flex;
    align-items: stretch;
    background: rgb(61, 61, 61);
    border-radius: 3px;
}
span {
    display: inline-flex;
    align-items: center;
}
input {
    width: 40px;
    font-size: 13pt;
    text-align: center;
    background: rgb(61, 61, 61);
    padding: 0;
}
input::-webkit-inner-spin-button {
    display: none;
}
</style>