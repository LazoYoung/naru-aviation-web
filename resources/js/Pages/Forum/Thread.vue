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
            <div class="max-w-3xl mx-auto py-6 sm:px-2 lg:px-4">
                <h2 class="font-semibold text-2xl leading-tight">{{thread.title}}</h2>
            </div>
        </template>

        <div class="max-w-3xl mx-auto">
            <div class="p-8 bg-gray-500 overflow-hidden shadow-sm sm:rounded-lg">
                <Post v-for="(post, index) in posts" :post="post" :index="index" :last="posts.length - 1" @open-draft="draft = true" />
            </div>
            <PostDraft v-if="draft" :thread="thread" @close="closeDraft()" />
        </div>
    </MainLayout>
</template>
