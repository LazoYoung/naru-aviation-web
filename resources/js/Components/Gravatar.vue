<script setup>
import {computed, ref} from "vue";
import MD5 from "crypto-js/md5.js";
import {usePage} from "@inertiajs/vue3";

const props = defineProps({
    hash: {
        type: String,
        default: null
    },
    large: {
        type: Boolean,
        default: false
    },
    size: {
        type: Number,
        default: 200
    }
});
const user = usePage().props.auth.user;
const imgHintElem = ref(null);
const imgSrc = computed(() => {
    let hash = getGravatarHash();
    console.log(hash);
    return `https://www.gravatar.com/avatar/${hash}?s=${props.size}`;
});

function getGravatarHash() {
    let def = props.hash;
    return def ? def : MD5(user.email.trim().toLowerCase());
}

function showHint() {
    imgHintElem.value.classList.remove('p-hidden');
}

function hideHint() {
    imgHintElem.value.classList.add('p-hidden');
}
</script>

<style>
.circle {
    border-radius: 50%;
}
.p-hover-bg {
    position: absolute;
    z-index: 1;
    background-color: black;
    opacity: 60%;
}
.p-hover-fg {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
}
.p-hidden {
    opacity: 0;
}
.p-font {
    text-align: center;
    font-weight: 700;
    font-size: 1.5rem;
    line-height: 2rem;
}
</style>

<template>
    <a
        v-if="large"
        href="https://www.gravatar.com"
        target="_blank"
        ref="imgHintElem"
        class="p-hidden"
        @mouseenter="showHint"
        @mouseleave="hideHint"
    >
        <div class="p-hover-bg circle">
            <p class="p-hover-fg p-font">Change my gravatar</p>
        </div>
    </a>
    <img :src="imgSrc" class="circle" alt="My Avatar"/>
</template>
