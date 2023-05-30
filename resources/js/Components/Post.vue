<script setup>
import {computed, onMounted, ref} from "vue";
import { usePage } from "@inertiajs/vue3";

const props = defineProps(['post', 'user', 'index', 'last']);
const frame = ref();
const csrfToken = usePage().props['csrf_token'];
const liked = ref(false);
const likeCount = ref(0);
const likeLabel = computed(() => {
    return (likeCount.value > 0) ? likeCount.value : 'Like';
});

onMounted(() => {
    drawBorder();
    fetchLiked();
    fetchLikeCount();
});

defineEmits(['open-draft']);

function copyLink() {
    let link = location.href;
    navigator.clipboard.writeText(link).then(() => {
        window.alert("URL copied to your clipboard!")
    });
}

function onLikeAction() {
    fetchLiked().then(() => {
        let promise;

        if (liked.value) {
            promise = submitData(route('forum.post.dislike'));
        } else {
            promise = submitData(route('forum.post.like'));
        }

        promise.then(() => {
            fetchLiked();
            fetchLikeCount();
        });
    });
}

function drawBorder() {
    let div = frame.value;

    if (props.index === 0) {
        div.classList.add('pt-0');
    }

    if (props.index === props.last) {
        div.classList.add('pb-0');
    }

    if (props.index < props.last) {
        div.classList.add('border-neutral-500');
        div.classList.add('border-b');
    }
}

function fetchLiked() {
    return fetchData(route('forum.post.has-liked'))
        .then(text => liked.value = (text === 'true'));
}

function fetchLikeCount() {
    return fetchData(route('forum.post.like-count'))
        .then(text => likeCount.value = parseInt(text ? text : '0'));
}

function fetchData(url) {
    let locator = new URL(url);
    let params = locator.searchParams;
    params.append('post-id', props.post['id']);

    return fetch(locator, {
        method: 'GET',
        headers: { 'X-CSRF-Token': csrfToken }
    }).then(r => r.text(), reason => window.alert(`Failed to fetch data: ${reason}`));
}

function submitData(url) {
    let form = new FormData();
    form.append('post-id', props.post['id']);

    return fetch(url, {
        method: 'POST',
        headers: { 'X-CSRF-Token': csrfToken },
        body: form
    }).then(r => r.text(), reason => window.alert(`Failed to fetch data: ${reason}`));
}
</script>

<template>
    <div ref="frame" class="py-8">
        <div class="flex flex-row leading-8">
            <i class="fa-solid fa-circle-user fa-2x"></i>
            <span class="mx-4 font-semibold">{{ user['name'] }}</span>
        </div>
        <div class="p-8">
            <span>{{ post['content'] }}</span>
        </div>
        <div class="flex flex-row justify-end gap-4">
            <button @click="onLikeAction" class="px-2">
                <i v-if="liked" class="fa-solid fa-thumbs-up"></i>
                <i v-else class="fa-regular fa-thumbs-up"></i>
                <span class="ps-2 font-bold">{{ likeLabel }}</span>
            </button>
            <button @click="copyLink" class="px-2">
                <i class="fa-solid fa-link"></i>
                <span class="ps-2 font-semibold">Share</span>
            </button>
            <button @click="$emit('open-draft')" class="px-2">
                <i class="fa-solid fa-comment"></i>
                <span class="ps-2 font-semibold">Reply</span>
            </button>
        </div>
    </div>
</template>
