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
            <div class="flex justify-between max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                <h2 class="font-semibold text-2xl leading-tight">Forum</h2>
                <div class="narrow">
                    <button small @click="findMyPost">
                        <i class="fa-regular fa-folder-open"></i>
                        <span class="ms-2">My Posts</span>
                    </button>
                    <button small @click="state.draft = true">
                        <i class="fa-solid fa-plus"></i>
                        <span class="ms-2">New Post</span>
                    </button>
                </div>
            </div>
        </template>

        <div class="max-w-7xl mx-auto sm:px-6">
            <div class="p-6 bg-gray-500 shadow-sm sm:rounded-lg">
                <div class="flex gap-x-4 items-center">
                    <CategorySelect @input="reload" v-model="state.category" :allow-all="true" />
                    <SearchBox class="max-w-md" @input="reload" v-model="state.search" />
                    <div class="flex-grow"></div>
                    <div class="wide">
                        <button small @click="findMyPost">
                            <i class="fa-regular fa-folder-open"></i>
                            <span class="ms-2">My Posts</span>
                        </button>
                        <button small @click="state.draft = true">
                            <i class="fa-solid fa-plus"></i>
                            <span class="ms-2">New Post</span>
                        </button>
                    </div>
                </div>
            </div>

            <div class="mt-6 bg-gray-500 overflow-hidden shadow-sm sm:rounded-lg">
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

<style scoped>
div.narrow {
    display: none;
}
div.wide {
    display: flex;
    flex-direction: row;
    column-gap: 1rem;
    align-items: center;
    flex-shrink: 0;
}

@media(max-width: 600px) {
    div.narrow {
        display: flex;
        flex-direction: row;
        column-gap: 1rem;
        align-items: center;
    }
    div.wide {
        display: none;
    }
}
</style>