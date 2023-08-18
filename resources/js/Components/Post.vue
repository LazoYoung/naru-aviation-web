<script setup>
import {computed, onMounted, ref} from "vue";
import {usePage} from "@inertiajs/vue3";
import {marked} from "marked";
import Gravatar from "@/Components/Gravatar.vue";

const props = defineProps(['post', 'index', 'last']);
const postId = props.post['id'];
const frame = ref();
const csrfToken = usePage().props.auth['csrf_token'];
const content = ref();
const gravatarHash = ref('');
const liked = ref(false);
const likeCount = ref(0);
const likeLabel = computed(() => {
    return (likeCount.value > 0) ? likeCount.value : 'Like';
});

onMounted(() => {
    drawBorder();
    fetchContent();
    fetchLiked();
    fetchLikeCount();
    fetchGravatar();
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

async function fetchContent() {
    let data = await fetchData(route('forum.post.content'));
    content.value.innerHTML = marked.parse(data);

    for (let child of Array.from(content.value.children)) {
        child.classList.add('doc');
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

function fetchGravatar() {
    return fetchData(route('forum.post.gravatar'))
        .then(text => gravatarHash.value = text);
}

function fetchData(url) {
    let locator = new URL(url);
    let params = locator.searchParams;
    params.append('post-id', postId);

    return fetch(locator, {
        method: 'GET',
        headers: { 'X-CSRF-Token': csrfToken }
    }).then(r => r.text(), reason => window.alert(`Failed to fetch data: ${reason}`));
}

function submitData(url) {
    let form = new FormData();
    form.append('post-id', postId);

    return fetch(url, {
        method: 'POST',
        headers: { 'X-CSRF-Token': csrfToken },
        body: form
    }).then(r => r.text(), reason => window.alert(`Failed to fetch data: ${reason}`));
}
</script>

<style>
/*noinspection ALL*/
.doc {
    all: revert;
    margin-top: 0.75rem;
    margin-bottom: 0.75rem;
}
</style>

<template>
    <div ref="frame" class="py-8">
        <div class="flex flex-row items-center leading-8">
            <i v-if="!gravatarHash" class="fa-solid fa-circle-user fa-2x"></i>
            <Gravatar v-else :hash="gravatarHash" :large="false" :size="50"></Gravatar>
            <span class="mx-4 font-semibold">{{ post['username'] }}</span>
        </div>
        <div class="p-8" ref="content"></div>
        <div class="flex flex-row justify-end gap-4">
            <button small @click="onLikeAction" class="px-2">
                <i v-if="liked" class="fa-solid fa-thumbs-up"></i>
                <i v-else class="fa-regular fa-thumbs-up"></i>
                <span class="ps-2 font-bold">{{ likeLabel }}</span>
            </button>
            <button small @click="copyLink" class="px-2">
                <i class="fa-solid fa-link"></i>
                <span class="ps-2 font-semibold">Share</span>
            </button>
            <button small @click="$emit('open-draft')" class="px-2">
                <i class="fa-solid fa-comment"></i>
                <span class="ps-2 font-semibold">Reply</span>
            </button>
        </div>
    </div>
</template>
