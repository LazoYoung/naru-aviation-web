<script setup>
import {computed} from "vue";
import {usePage} from "@inertiajs/vue3";
import {getGravatarHash} from "@/api.js";

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
        default: 512
    }
});
const user = usePage().props.auth.user;
const imgSrc = computed(() => {
    let hash = props.hash ? props.hash : getGravatarHash(user.email);
    return `https://www.gravatar.com/avatar/${hash}?s=${props.size}`;
});
</script>

<style>
[layer] {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
}
.p-hover-bg {
    background-color: rgba(0,0,0,0.7);
    backdrop-filter: blur(2px);
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
.p-hidden:hover {
    opacity: 1;
}
.p-font {
    color: white;
    text-align: center;
    font-weight: 700;
    font-size: 1.5rem;
    line-height: 2rem;
}
.ratio-box {
    position: relative;
    width: 100%;
    padding-top: 100%;
}
.img-box {
    border-radius: 100%;
    border: 1px solid rgb(209, 213, 219);
    overflow: hidden;
}
</style>

<template>
<!-- given the width, 1:1 content box is generated  -->
    <div v-if="large" class="ratio-box">
        <div v-if="large" class="img-box" layer>
            <a
                href="https://www.gravatar.com"
                target="_blank"
                class="w-full h-full block"
            >
                <img :src="imgSrc" layer alt="My Avatar"/>
                <div ref="imgHintElem" layer class="p-hover-bg p-hidden">
                    <p class="p-hover-fg p-font">Change my gravatar</p>
                </div>
            </a>
        </div>
    </div>
    <img v-else :src="imgSrc" class="img-box" alt="My Avatar"/>
</template>
