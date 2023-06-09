<script setup>
import {reactive} from "vue";
import {Head, usePage} from "@inertiajs/vue3";
import MainLayout from "@/Layouts/MainLayout.vue";
import SearchBox from "@/Components/SearchBox.vue";
import ThreadCard from "@/Components/ThreadCard.vue";
import ThreadDraft from "@/Components/ThreadDraft.vue";
import CategorySelect from "@/Components/CategorySelect.vue";

const user = usePage().props.auth['user'];
const csrfToken = usePage().props.auth['csrf_token'];
const state = reactive({
    category: null,
    search: '',
    draft: false,
    threads: []
});
const throttlePeriod = 1500;
let isThrottled = false;
let queued = false;

reload();

function reload() {
    if (isThrottled) {
        queued = true;
        return;
    }

    let url = new URL(route('forum.thread.fetch'));

    if (state.category) {
        url.searchParams.append('category', state.category);
        console.log('category: ' + state.category);
    }
    if (state.search && state.search.length > 0) {
        url.searchParams.append('search', state.search);
        console.log('search: ' + state.search);
    }

    startThrottle();
    fetch(url, {
        method: 'GET',
        headers: { 'X-CSRF-Token': csrfToken }
    }).then(r => r.json()).then(json => {
        state.threads = json;
    });
}

function findMyPost() {
    state.search = 'user:' + user['name'].toLowerCase();
    reload();
}

function startThrottle() {
    isThrottled = true;

    setTimeout(() => {
        isThrottled = false;

        if (queued) {
            reload();
            queued = false;
        }
    }, throttlePeriod);
}
</script>

<template>
    <Head title="Forum" />

    <MainLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Forum</h2>
        </template>

        <div class="max-w-7xl mx-auto py-12 sm:px-6">
            <div class="p-6 bg-white shadow-sm sm:rounded-lg">
                <div class="h-12 flex gap-x-4">
                    <CategorySelect @input="reload" v-model="state.category" :allow-all="true" />
                    <SearchBox @input="reload" v-model="state.search" />
                    <div class="flex-grow"></div>
                    <button @click="findMyPost" class="flex-shrink-0 inline-flex items-center px-4 py-4 bg-neutral-600 rounded-md text-white font-bold text-lg">
                        <i class="fa-regular fa-folder-open"></i>
                        <span class="ms-2 hidden md:inline">My Posts</span>
                    </button>
                    <button @click="state.draft = true" class="flex-shrink-0 inline-flex items-center px-4 py-4 bg-neutral-600 rounded-md text-white font-bold text-lg">
                        <i class="fa-solid fa-plus"></i>
                        <span class="ms-2 hidden md:inline">New Post</span>
                    </button>
                </div>
            </div>

            <div class="mt-6 bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <table class="w-full">
                    <tbody>
                        <ThreadCard v-for="t in state.threads" :id="t.id" :title="t.title" :category="t.category" />
                    </tbody>
                </table>
            </div>

            <ThreadDraft v-if="state.draft" @close="state.draft = false" />
        </div>
    </MainLayout>
</template>
