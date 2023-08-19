<script setup>
import MainLayout from '@/Layouts/MainLayout.vue';
import DeleteUserForm from './Partials/DeleteUserForm.vue';
import UpdatePasswordForm from './Partials/UpdatePasswordForm.vue';
import UpdateProfileInformationForm from './Partials/UpdateProfileInformationForm.vue';
import { Head } from '@inertiajs/vue3';
import APIKeyForm from "@/Pages/Profile/Partials/APIKeyForm.vue";

defineProps({
    mustVerifyEmail: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});
</script>

<template>
    <Head title="Profile" />

    <MainLayout>
        <template #header>
            <div class="max-w-5xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                <h2 class="font-semibold text-2xl leading-tight">Profile</h2>
            </div>
        </template>

        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="p-8 bg-gray-400 shadow sm:rounded-lg">
                <UpdateProfileInformationForm
                    :must-verify-email="mustVerifyEmail"
                    :status="status"
                />
            </div>

            <div class="p-8 bg-gray-400 shadow sm:rounded-lg">
                <APIKeyForm class="max-w-lg" />
            </div>

            <div class="p-8 bg-gray-400 shadow sm:rounded-lg">
                <UpdatePasswordForm class="max-w-lg" />
            </div>

            <div class="p-8 bg-gray-400 shadow sm:rounded-lg">
                <DeleteUserForm class="max-w-lg" />
            </div>
        </div>
    </MainLayout>
</template>
