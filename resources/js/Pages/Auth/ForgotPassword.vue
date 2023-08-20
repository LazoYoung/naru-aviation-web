<script setup>
import AuthLayout from '@/Layouts/AuthLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, useForm } from '@inertiajs/vue3';

defineProps({
    status: {
        type: String,
    },
});

const form = useForm({
    email: '',
});

const submit = () => {
    form.post(route('password.email'));
};
</script>

<template>
    <AuthLayout>
        <Head title="Forgot Password" />

        <div class="mb-4 text-sm text-white">
            Forgot your password? No problem! We will send you a mail with a password reset
            link where you can choose a new one.
        </div>

        <div v-if="status" class="mb-4 font-medium text-sm text-white">
            {{ status }}
        </div>

        <form @submit.prevent="submit">
            <div>
                <TextInput
                    id="email"
                    type="email"
                    hint="Email"
                    label="top"
                    class="mt-1 block w-full"
                    v-model="form.email"
                    required
                    autofocus
                    autocomplete="username"
                />

                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <div class="flex items-center justify-end mt-4">
                <button small filled :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                    Request mail
                </button>
            </div>
        </form>
    </AuthLayout>
</template>
