<script setup>
import {computed} from "vue";

const props = defineProps({
    color: {
        type: String,
        default: 'white'
    }
});
const getImageSource = computed(() => {
    switch (props.color) {
        case 'white':
            return '/icon.svg'
        case 'blue':
            return '/icon_blue.svg'
        default:
            return '';
    }
})
</script>

<template>
    <img :src="getImageSource" alt="Logo"/>
</template>
