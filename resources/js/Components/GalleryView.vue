<script setup>
import Modal from "@/Components/Modal.vue";

defineProps(['image', 'show']);
defineEmits(['close'])
</script>

<template>
    <Modal :show="show" max-width="4xl" @close="$emit('close')">
        <div id="model-div">
            <div class="flex flex-row justify-between">
                <div>
                    <h1 class="text-xl font-semibold">{{image['title']}}</h1>
                    <p class="text-sm">{{image['description']}}</p>
                </div>
                <div>
                    <p class="text-sm">by {{image['author']}}</p>

                </div>
            </div>
            <img class="mx-auto max-h-[500px]" :src="image['file']" :alt="image['id']"/>
        </div>
    </Modal>
</template>

<style scoped>
#model-div {
    padding: 2rem;
    background-color: var(--bg);
}
</style>
