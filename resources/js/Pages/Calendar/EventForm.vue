<script setup>
import MainLayout from "@/Layouts/MainLayout.vue";
import {useForm} from "@inertiajs/vue3";

const form = useForm({
    "name": null,
    "start-date": null,
    "end-date": null,
    "link": null,
});

function submitForm() {
    form.post(route("calendar.new"), {
        onError: (errors) => {
            for (let key in errors) {
                window.alert(errors[key]);
            }
        }
    });
}
</script>

<template>
    <MainLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Event</h2>
        </template>
        <form @submit.prevent="submitForm">
            <div class="max-w-2xl mx-auto my-8">
                <div>
                    <h2>Name</h2>
                    <input type="text">
                    <h2>Date</h2>
                    <input type="datetime-local">
                    <input type="datetime-local">
                    <h2>Link</h2>
                    <input type="url">
                </div>
                <input type="submit">
            </div>
        </form>
    </MainLayout>
</template>
