<script setup>
const props = defineProps(['post', 'user']);

function processLike() {
    let userId = props.user['id'];
    // todo Feature: like on posts, database restructure required
    window.alert("Work in progress...");
}

function copyLink() {
    let link = location.href;
    navigator.clipboard.writeText(link).then(() => {
        window.alert("URL copied to your clipboard!")
    });
}
</script>

<template>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <div class="p-8 bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="flex flex-row leading-8">
            <i class="fa-solid fa-circle-user fa-2x"></i>
            <span class="mx-4">{{user['name']}}</span>
        </div>
        <div class="p-8">
            <span>{{post['content']}}</span>
        </div>
        <div class="flex flex-row justify-end gap-4">
            <button class="p-1 bg-opacity-0">
                <span @click="processLike" class="pe-2 font-bold">{{post['like']}}</span>
                <i class="fa-regular fa-thumbs-up"></i>
            </button>
            <button @click="copyLink" class="p-1 bg-opacity-0">
                <i class="fa-solid fa-link"></i>
            </button>
        </div>
    </div>
</template>
