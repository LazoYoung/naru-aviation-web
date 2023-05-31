<script setup>
import {Head, usePage} from "@inertiajs/vue3";
import MainLayout from "@/Layouts/MainLayout.vue";
import Post from "@/Components/Post.vue";
import {ref} from "vue";
import PostDraft from "@/Components/PostDraft.vue";

const props = defineProps(['thread', 'posts']);
const user = usePage().props.auth['user'];
const draft = ref(false);
const thread = props.thread;
const posts = props.posts;

function closeDraft() {
    draft.value = false;
    location.reload();
}
</script>

<template>
    <Head :title="thread.title"/>

    <MainLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{thread.title}}</h2>
        </template>

        <div class="max-w-7xl mx-auto py-12 sm:px-6">
            <div class="p-8 bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <Post v-for="(post, index) in posts" :post="post" :user="user" :index="index" :last="posts.length - 1" @open-draft="draft = true" />
            </div>
            <PostDraft v-if="draft" :thread="thread" @close="closeDraft()" />
        </div>
    </MainLayout>
</template>
