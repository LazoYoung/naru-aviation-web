<script setup>
import InputError from '@/Components/InputError.vue';
import TextInput from '@/Components/TextInput.vue';
import {Link, useForm, usePage} from '@inertiajs/vue3';
import Gravatar from "@/Components/Gravatar.vue";
import Alert from "@/alert.js";
import {ref} from "vue";

defineProps({
    mustVerifyEmail: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const alert = new Alert();
const user = usePage().props.auth.user;
const nameInput = ref(null);
const emailInput = ref(null);
const form = useForm({
    name: user.name,
    email: user.email,
});

function submit() {
    form.patch(route('profile.update'), {
        onSuccess: () => {
            alert.setType('success');
            alert.pop('Profile saved!');
        },
        onError: () => {
            if (form.errors.name) {
                form.reset('name');
                nameInput.value.focus();
            }
            if (form.errors.email) {
                form.reset('email');
                emailInput.value.focus();
            }
        }
    });
}
</script>

<template>
    <div class="flex flex-row">
        <section class="left-box max-w-xl flex-grow md:me-8">
            <header>
                <h2 class="text-lg font-medium text-gray-900">Profile Information</h2>

                <p class="mt-1 text-sm text-gray-600">
                    Update your account's profile information and email address.
                </p>
            </header>

            <div class="sm-show mt-6 space-y-6">
                <label for="pic-small">Picture</label>
                <div id="pic-small">
                    <Gravatar :large="true" size="200"/>
                </div>
            </div>

            <form @submit.prevent="submit()" class="mt-6 space-y-6 max-w-lg">
                <section>
                    <TextInput
                        id="name"
                        type="text"
                        hint="Name"
                        label="top"
                        ref="nameInput"
                        v-model="form.name"
                        required
                        autofocus
                        autocomplete="name"
                    />

                    <InputError class="mt-2" :message="form.errors.name" />
                </section>

                <section>
                    <TextInput
                        id="email"
                        type="email"
                        hint="Email"
                        label="top"
                        ref="emailInput"
                        v-model="form.email"
                        required
                        autocomplete="username"
                    />

                    <InputError class="mt-2" :message="form.errors.email" />
                </section>

                <div v-if="mustVerifyEmail && user.email_verified_at === null">
                    <p class="text-sm mt-2 text-gray-800">
                        Your email address is unverified.
                        <br>
                        <Link
                            :href="route('verification.send')"
                            method="post"
                            as="button"
                            class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                        >
                            Click here to re-send the verification email.
                        </Link>
                    </p>

                    <div
                        v-show="status === 'verification-link-sent'"
                        class="mt-2 font-medium text-sm text-green-600"
                    >
                        A new verification link has been sent to your email address.
                    </div>
                </div>

                <div class="flex items-center gap-4">
                    <button small filled :disabled="form.processing">Save</button>
                </div>
            </form>
        </section>

        <div class="right-box sm-hidden">
            <Gravatar :large="true" size="512"/>
        </div>
    </div>
</template>

<style>
.left-box {
    transition: width 0.2s ease-out;
}
.right-box {
    width: 12rem;
    transition: width 0.2s ease-out;
}
.sm-show {
    display: block;
}
.sm-hidden {
    display: none;
}
@media (min-width: 720px) {
    .right-box {
        width: 16rem;
        transition: width 0.2s ease-out;
    }
    .sm-show {
        display: none;
    }
    .sm-hidden {
        display: block;
    }
}
</style>
