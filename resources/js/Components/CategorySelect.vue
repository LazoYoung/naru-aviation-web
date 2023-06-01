<script setup>
import {Category} from "@/category.ts";

const notam = Category.byName("notam");
const event = Category.byName("event");
const general = Category.byName("general");
const question = Category.byName("question");

defineProps({
    allowAll: Boolean,
    modelValue: String,
    required: {
        type: Boolean,
        default: false
    }
});
defineEmits(["update:modelValue"]);
</script>

<template>
    <select :value="modelValue" @input="$emit('update:modelValue', $event.target.value)" class="font-semibold" :required="required">
        <option v-if="allowAll" value="" selected>All categories</option>
        <option v-else value="" disabled selected>Select a category...</option>
        <optgroup label="Notice">
            <option :value="notam.getIndex()">{{ notam.getName() }}</option>
            <option :value="event.getIndex()">{{ event.getName() }}</option>
        </optgroup>
        <optgroup label="Community">
            <option :value="general.getIndex()">{{ general.getName() }}</option>
            <option :value="question.getIndex()">{{ question.getName() }}</option>
        </optgroup>
    </select>
</template>
