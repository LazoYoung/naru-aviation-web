<script setup>
import {ref} from "vue";
import {Head} from "@inertiajs/vue3";
import MainLayout from "@/Layouts/MainLayout.vue";
import SearchBox from "@/Components/SearchBox.vue";
import ThreadCard from "@/Components/ThreadCard.vue";
import ThreadDraft from "@/Components/ThreadDraft.vue";
import CategorySelect from "@/Components/CategorySelect.vue";

const query = ref("");
const draft = ref(false);

function openDraft() {
    draft.value = true;
}

function closeDraft() {
    draft.value = false;
}

defineExpose({ openDraft, closeDraft });
</script>

<template>
    <Head title="Forum" />

    <MainLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Forum</h2>
        </template>

        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer" />

        <div class="max-w-7xl mx-auto py-12 sm:px-6">
            <div class="p-6 bg-white shadow-sm sm:rounded-lg">
                <div class="h-12 flex gap-x-4">
                    <CategorySelect :allow-all="true" />
                    <SearchBox v-model:value="query"></SearchBox>
                    <div class="flex-grow"></div>
                    <button class="flex-shrink-0 inline-flex items-center px-4 py-4 bg-neutral-600 rounded-md text-white font-bold text-lg">
                        <i class="fa-regular fa-folder-open"></i>
                        <span class="ms-2 hidden md:inline">My Posts</span>
                    </button>
                    <button @click="openDraft" class="flex-shrink-0 inline-flex items-center px-4 py-4 bg-neutral-600 rounded-md text-white font-bold text-lg">
                        <i class="fa-solid fa-plus"></i>
                        <span class="ms-2 hidden md:inline">New Post</span>
                    </button>
                </div>
            </div>

            <div class="mt-6 bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <table class="w-full">
                    <tbody>
                        <ThreadCard :id="1" title="Hello, world!" category="General" color="#000000" author="Chanyoung Park" :date="new Date(2023, 4, 26, 18, 22)" :view.number="13"></ThreadCard>
                        <ThreadCard :id="2" title="Hello, world!" category="General" color="#000000" author="Chanyoung Park" :date="new Date(2023, 4, 26, 18, 22)" :view.number="13"></ThreadCard>
                    </tbody>
                </table>
            </div>

            <ThreadDraft v-if="draft" @closed="closeDraft" />
        </div>
    </MainLayout>
</template>
