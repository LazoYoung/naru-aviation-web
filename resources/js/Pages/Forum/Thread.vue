<script setup>
import {Head} from "@inertiajs/vue3";
import MainLayout from "@/Layouts/MainLayout.vue";
import Post from "@/Components/Post.vue";

defineProps(['thread', 'posts', 'user']);
</script>

<template>
    <Head :title="thread.title"/>

    <MainLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{thread.title}}</h2>
        </template>

        <div class="max-w-7xl mx-auto py-12 sm:px-6">
            <Post v-for="post in posts" :post="post" :user="user"></Post>
        </div>
    </MainLayout>
</template>
