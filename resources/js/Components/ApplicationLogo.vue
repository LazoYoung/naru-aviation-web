<script setup>
import {computed} from "vue";

const props = defineProps({
    mode: {
        type: String,
        default: 'icon'
    }
});
const getImageSource = computed(() => {
    switch (props.mode) {
        case 'text':
            // return '/logo_text.png';
            return '/icon.svg'
        case 'icon':
            // return '/logo_icon.png';
            return '/icon.svg'
        default:
            return '';
    }
})
</script>

<template>
    <img :src="getImageSource" alt="Logo"/>
</template>
