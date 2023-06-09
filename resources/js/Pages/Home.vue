<script setup>
import MainLayout from '@/Layouts/MainLayout.vue';
import {Head} from '@inertiajs/vue3';

defineProps({
    canLogin: {
        type: Boolean,
    },
    canRegister: {
        type: Boolean,
    },
    laravelVersion: {
        type: String,
        required: true,
    },
    phpVersion: {
        type: String,
        required: true,
    },
});
</script>

<style scoped>
/* Hide redundant scrollbars */
div {
    scrollbar-width: none;
}
div::-webkit-scrollbar {
    display: none;
}

article {
    height: calc(100vh - 4rem);
}
</style>

<template>
    <Head title="Home" />

    <MainLayout>
        <div class="fixed w-full h-full">
            <video class="object-cover w-full h-full" autoplay loop muted disablePictureInPicture>
                <source src="/promo.mp4">
            </video>
        </div>

        <div class="z-10 w-full h-screen p-0 overflow-y-scroll snap-y snap-mandatory scroll-smooth ">
            <article class="flex flex-col relative h-screen snap-end">
                <div class="my-auto text-center">
                    <h1 class="text-5xl text-white font-bold">We are virtual aviators.</h1>
                    <div class="h-10 invisible"></div>
                    <a :href="route('register')">
                        <button class="inline-block text-white bg-cyan-600 px-4 py-2">Join us</button>
                    </a>
                </div>
            </article>

            <article class="relative h-screen snap-end bg-black sm:px-6 lg:px-8">
                <div class="py-16">
                    <h1 class="text-4xl text-white text-center font-bold">Scheduled Flights</h1>
                </div>

                <div class="flex justify-center mt-16 px-6 sm:items-center sm:justify-between">
                    <div class="text-center text-sm text-white dark:text-gray-400 sm:text-left">
                        <div class="flex items-center gap-4">
                            <a
                                href="https://github.com/LazoYoung/virtual-airline-web"
                                target="_blank"
                                class="group inline-flex items-center hover:text-gray-700 dark:hover:text-white focus:outline focus:outline-2 focus:rounded-sm focus:outline-red-500"
                            >
                                <i class="fa-brands fa-github"></i>
                                <p class="ms-2">Crafted by Chanyoung Park</p>
                            </a>
                        </div>
                    </div>

                    <div class="ml-4 text-center text-sm text-white dark:text-gray-400 sm:text-right sm:ml-0">
                        Laravel v{{ laravelVersion }} (PHP v{{ phpVersion }})
                    </div>
                </div>
            </article>
        </div>
    </MainLayout>
</template>