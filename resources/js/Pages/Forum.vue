<script setup>
import {ref} from "vue";
import {Head} from "@inertiajs/vue3";
import MainLayout from "@/Layouts/MainLayout.vue";
import SearchBox from "@/Components/SearchBox.vue";

const query = ref("");
</script>

<template>
    <Head title="Forum" />

    <MainLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Forum</h2>
        </template>

        <div class="max-w-7xl mx-auto pt-12 sm:px-6">
            <div class="p-6 bg-white shadow-sm sm:rounded-lg">
                <div class="h-12 flex justify-between content-stretch">
                    <select name="categories" id="category">
                        <option value="all">All categories</option>
                        <optgroup label="Notice">
                            <option value="notam">NOTAM</option>
                            <option value="event">Event</option>
                        </optgroup>
                        <optgroup label="Community">
                            <option value="general">General</option>
                            <option value="question">Question</option>
                        </optgroup>
                    </select>
                    <SearchBox v-model:query="query"></SearchBox>
                </div>
            </div>
        </div>

        <div class="max-w-7xl mx-auto py-12 sm:px-6 lg:px-8">
            <div class="p-6 bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <p>{{ query }}</p>
            </div>
        </div>
    </MainLayout>
</template>
