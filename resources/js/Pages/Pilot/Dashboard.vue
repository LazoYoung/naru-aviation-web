<script setup>
import { Head } from '@inertiajs/vue3';
import MainLayout from "@/Layouts/MainLayout.vue";
</script>

<template>
    <Head title="Pilot Center"></Head>

    <MainLayout :pilot="true">
        <template #header>
            <div class="max-w-5xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                <h2 class="font-semibold text-2xl text-white leading-tight">Pilot Center</h2>
            </div>
        </template>

        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="bg">

            </div>
        </div>
    </MainLayout>
</template>
