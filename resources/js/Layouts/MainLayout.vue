<script setup>
import {ref} from 'vue';
import {usePage} from '@inertiajs/vue3';
import Navigation from "@/Layouts/Navigation.vue";

const auth = usePage().props.auth;
const content = ref();
</script>

<template>
    <div>
        <div class="min-h-screen bg-gray-100">
            <div class="fixed w-full top-0 z-20">
                <Navigation></Navigation>
                <header class="bg-white shadow pt-20" v-if="$slots.header">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        <slot name="header" />
                    </div>
                </header>
            </div>
            <main>
                <div class="pt-32">
                    <slot />
                </div>
            </main>
        </div>
    </div>
</template>
