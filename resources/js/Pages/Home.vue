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

<template>
    <Head title="Home" />

    <MainLayout>
        <div class="fixed">
            <video class="object-cover w-screen h-screen" autoplay loop muted disablePictureInPicture>
                <source src="/promo.mp4">
            </video>
        </div>

        <div class="z-10 flex flex-col relative top-0 h-screen">
            <div class="my-auto text-center">
                <p class="text-5xl font-bold py-8">We are virtual aviators.</p>
                <button class="inline-block bg-cyan-600 px-4 py-2">Join us</button>
            </div>
        </div>

        <div class="z-10 relative w-full h-screen bg-black sm:px-6 lg:px-8">
            <div class="py-80">blank area</div>

            <div class="flex justify-center mt-16 px-6 sm:items-center sm:justify-between">
                <div class="text-center text-sm text-gray-500 dark:text-gray-400 sm:text-left">
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

                <div class="ml-4 text-center text-sm text-gray-500 dark:text-gray-400 sm:text-right sm:ml-0">
                    Laravel v{{ laravelVersion }} (PHP v{{ phpVersion }})
                </div>
            </div>
        </div>
    </MainLayout>
</template>
