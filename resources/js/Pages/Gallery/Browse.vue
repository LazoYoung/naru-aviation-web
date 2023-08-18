<script setup>
import {Head, usePage} from "@inertiajs/vue3"
import MainLayout from "@/Layouts/MainLayout.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import {computed, onMounted, reactive} from "vue";
import {fetchJSON} from "@/api.js";
import GalleryView from "@/Components/GalleryView.vue";
import GalleryModal from "@/Components/GalleryModal.vue";

const auth = usePage().props.auth;
const csrfToken = auth['csrf_token'];
const state = reactive({
    images: [],
    image: null,
    showModal: false,
});
const isEmpty = computed(() => {
    return !state.images || state.images.length === 0;
})

onMounted(() => {
    fetchImages();
});

async function fetchImages() {
    let json = await fetchJSON(route('image.fetch.all'), csrfToken);

    if (json) {
        state.images = JSON.parse(json);
    }
}

function onImageClick(event) {
    let target = event.target.closest('a');
    let id = Number.parseInt(target.dataset.id);

    for (let image of state.images) {
        if (image['id'] === id) {
            state.image = image;
            break;
        }
    }
}

function onUploadClick() {
    if (auth['user']) {
        state.showModal = true;
    } else {
        location.href = route('login');
    }
}

function closeImageView() {
    state.image = null;
}

function closeModal() {
    state.showModal = false;
}
</script>

<template>
    <Head title="Gallery"></Head>

    <MainLayout>
        <template #header>
            <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                <h2 class="font-semibold text-xl leading-tight">Gallery</h2>
            </div>
        </template>

        <div class="max-w-7xl mx-auto sm:px-6">
            <div class="mb-6 flex flex-row justify-end">
                <primary-button @click="onUploadClick">
                    Upload
                </primary-button>
            </div>
            <div class="p-6 bg-gray-500 shadow-sm sm:rounded-lg">
                <p v-if="isEmpty" class="text-center">Gallery is empty.</p>
                <div v-else class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
                    <a v-for="image in state.images" :data-id="image['id']" @click.prevent="onImageClick($event)" href="#">
                        <img :src="image['file']" :alt="image['title']" class="w-full h-full object-cover">
                    </a>
                </div>
            </div>
        </div>

        <GalleryView :show="state.image != null" :image="state.image" @close="closeImageView"></GalleryView>
        <GalleryModal :show="state.showModal" @update="fetchImages" @close="closeModal"></GalleryModal>
    </MainLayout>
</template>
