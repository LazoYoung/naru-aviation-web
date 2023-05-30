<script setup>
import { onMounted, ref } from "vue";
import { usePage } from "@inertiajs/vue3";

const props = defineProps(['post', 'user']);
const csrfToken = usePage().props.csrf_token;
const liked = ref(false);
const likeCount = ref(0);

onMounted(() => {
    fetchLiked();
    fetchLikeCount();
});

function onLikeAction() {
    fetchLiked().then(() => {
        let promise;

        if (liked.value) {
            promise = fetchData(route('forum.post.dislike'));
        } else {
            promise = fetchData(route('forum.post.like'));
        }

        promise.then(() => {
            fetchLiked();
            fetchLikeCount();
        });
    });
}

function copyLink() {
    let link = location.href;
    navigator.clipboard.writeText(link).then(() => {
        window.alert("URL copied to your clipboard!")
    });
}

function fetchLiked() {
    return fetchData(route('forum.post.has-liked'))
        .then(text => liked.value = (text === 'true'));
}

function fetchLikeCount() {
    return fetchData(route('forum.post.like-count'))
        .then(text => likeCount.value = parseInt(text));
}

function fetchData(url) {
    let form = new FormData();
    form.append('post-id', props.post['id']);

    return fetch(url, {
        method: 'POST',
        body: form,
        headers: {
            'X-CSRF-Token': csrfToken
        }
    }).then(r => r.text(), reason => window.alert(`Failed to fetch data: ${reason}`));
}
</script>

<template>
    <div class="p-8 bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="flex flex-row leading-8">
            <i class="fa-solid fa-circle-user fa-2x"></i>
            <span class="mx-4">{{ user['name'] }}</span>
        </div>
        <div class="p-8">
            <span>{{ post['content' ]}}</span>
        </div>
        <div class="flex flex-row justify-end gap-4">
            <button @click="onLikeAction" class="p-1 bg-opacity-0">
                <span class="pe-2 font-bold">{{ likeCount }}</span>
                <i v-if="liked" class="fa-solid fa-thumbs-up"></i>
                <i v-else class="fa-regular fa-thumbs-up"></i>
            </button>
            <button @click="copyLink" class="p-1 bg-opacity-0">
                <i class="fa-solid fa-link"></i>
            </button>
        </div>
    </div>
</template>
