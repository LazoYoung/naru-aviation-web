<script setup>
import {ref} from 'vue';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import NavLink from '@/Components/NavLink.vue';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';
import {Link, usePage} from '@inertiajs/vue3';

const auth = usePage().props.auth;
const showingNavigationDropdown = ref(false);
const props = defineProps({
    innerBody: {
        type: Boolean,
        default: false,
    }
});
const content = ref();
</script>

<template>
    <div>
        <div class="min-h-screen bg-gray-100">
            <div class="fixed w-full top-0 z-20">
                <!-- Primary Navigation Menu -->
                <nav class="bg-white border-b border-gray-100">
                    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                        <div class="flex justify-between h-16">
                            <!-- Logo -->
                            <div class="shrink-0 flex items-center">
                                <Link :href="route('home')">
                                    <ApplicationLogo
                                        mode="text" class="block w-40 h-auto fill-current text-gray-800"
                                    />
                                </Link>
                            </div>

                            <div class="flex">
                                <!-- Navigation Links -->
                                <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
                                    <NavLink :href="route('forum.browse')" :active="route().current('forum.*')">
                                        Forum
                                    </NavLink>
                                </div>

                                <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
                                    <NavLink :href="route('image.show.gallery')" :active="route().current('gallery.*')">
                                        Gallery
                                    </NavLink>
                                </div>

                                <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
                                    <NavLink :href="route('event.show.calendar')" :active="route().current('event.show.*')">
                                        Calendar
                                    </NavLink>
                                </div>
                            </div>

                            <div class="hidden sm:flex sm:items-center sm:ml-6">
                                <!-- Profile Dropdown -->
                                <div class="ml-3 relative">
                                    <Dropdown v-if="auth.user" align="right" width="48">
                                        <template #trigger>
                                        <span class="inline-flex rounded-md">
                                            <button
                                                type="button"
                                                class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150"
                                            >
                                                {{ auth.user.name }}

                                                <svg
                                                    class="ml-2 -mr-0.5 h-4 w-4"
                                                    xmlns="http://www.w3.org/2000/svg"
                                                    viewBox="0 0 20 20"
                                                    fill="currentColor"
                                                >
                                                    <path
                                                        fill-rule="evenodd"
                                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                        clip-rule="evenodd"
                                                    />
                                                </svg>
                                            </button>
                                        </span>
                                        </template>

                                        <template #content>
                                            <DropdownLink :href="route('profile.edit')"> Profile </DropdownLink>
                                            <DropdownLink :href="route('logout')" method="post" as="button" :preserve-state="false">
                                                Log Out
                                            </DropdownLink>
                                        </template>
                                    </Dropdown>
                                    <template v-else>
                                        <Link
                                            :href="route('login')"
                                            class="font-semibold text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white focus:outline focus:outline-2 focus:rounded-sm focus:outline-red-500"
                                        >Log in</Link
                                        >

                                        <Link
                                            :href="route('register')"
                                            class="ml-4 font-semibold text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white focus:outline focus:outline-2 focus:rounded-sm focus:outline-red-500"
                                        >Register</Link
                                        >
                                    </template>
                                </div>
                            </div>

                            <!-- Hamburger -->
                            <div class="-mr-2 flex items-center sm:hidden">
                                <button
                                    @click="showingNavigationDropdown = !showingNavigationDropdown"
                                    class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out"
                                >
                                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                                        <path
                                            :class="{
                                            hidden: showingNavigationDropdown,
                                            'inline-flex': !showingNavigationDropdown,
                                        }"
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M4 6h16M4 12h16M4 18h16"
                                        />
                                        <path
                                            :class="{
                                            hidden: !showingNavigationDropdown,
                                            'inline-flex': showingNavigationDropdown,
                                        }"
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12"
                                        />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Responsive Navigation Menu -->
                    <div
                        :class="{ block: showingNavigationDropdown, hidden: !showingNavigationDropdown }"
                        class="sm:hidden"
                    >
                        <div class="pt-2 pb-3 space-y-1">
                            <ResponsiveNavLink :href="route('home')" :active="route().current('home')">
                                Home
                            </ResponsiveNavLink>
                            <ResponsiveNavLink :href="route('forum.browse')" :active="route().current('forum.*')">
                                Forum
                            </ResponsiveNavLink>
                            <ResponsiveNavLink :href="route('image.show.gallery')" :active="route().current('image.*')">
                                Gallery
                            </ResponsiveNavLink>
                            <ResponsiveNavLink :href="route('event.show.calendar')" :active="route().current('event.*')">
                                Calendar
                            </ResponsiveNavLink>
                        </div>

                        <!-- Responsive Settings Options -->
                        <div class="pt-4 pb-1 border-t border-gray-200">
                            <template v-if="auth.user">
                                <div class="px-4">
                                    <div class="font-medium text-base text-gray-800">
                                        {{ auth.user.name }}
                                    </div>
                                    <div class="font-medium text-sm text-gray-500">{{ auth.user.email }}</div>
                                </div>

                                <div class="mt-3 space-y-1">
                                    <ResponsiveNavLink :href="route('profile.edit')"> Profile </ResponsiveNavLink>
                                    <ResponsiveNavLink :href="route('logout')" method="post" as="button" :preserve-state="false">
                                        Log Out
                                    </ResponsiveNavLink>
                                </div>
                            </template>
                            <template v-else>
                                <div class="mt-3 space-y-1">
                                    <ResponsiveNavLink :href="route('login')"> Login </ResponsiveNavLink>
                                    <ResponsiveNavLink :href="route('register')"> Register </ResponsiveNavLink>
                                </div>
                            </template>
                        </div>
                    </div>
                </nav>

                <!-- Page Heading -->
                <header class="bg-white shadow" v-if="$slots.header">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        <slot name="header" />
                    </div>
                </header>

                <main v-if="innerBody">
                    <slot />
                </main>
            </div>
            <main v-if="!innerBody">
                <div class="pt-32">
                    <slot />
                </div>
            </main>
        </div>
    </div>
</template>
