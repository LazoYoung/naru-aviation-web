<script setup>
import {onMounted, ref, watch} from "vue";
import {usePage} from "@inertiajs/vue3";
import {Category} from "@/category.ts";
import Gravatar from "@/Components/Gravatar.vue";

const csrfToken = usePage().props.auth['csrf_token'];
const props = defineProps(['id', 'title', 'category']);
const authorName = ref('N/A');
const createdTime = ref('N/A');
const viewCount = ref('0 view');
const gravatarHash = ref('');

onMounted(() => {
    fetchAuthorName();
    fetchCreatedTime();
    fetchViewCount();
    fetchAuthorGravatar();
});

watch(props,  () => {
    fetchAuthorName();
    fetchCreatedTime();
    fetchViewCount();
});

function redirect() {
    location.href = route('forum.thread.show', { id: props.id });
}

function getCategory() {
    return Category.byId(props.category);
}

function fetchAuthorName() {
    fetchData(route('forum.thread.author-name'))
        .then(text => authorName.value = text);
}

function fetchAuthorGravatar() {
    fetchData(route('forum.thread.author-gravatar'))
        .then(text => gravatarHash.value = text);
}

function fetchCreatedTime() {
    fetchData(route('forum.thread.created-time'))
        .then(text => createdTime.value = text);
}

function fetchViewCount() {
    fetchData(route('forum.thread.view-count'))
        .then(text => parseInt(text ? text : '0'))
        .then(count => {
            if (count > 1) {
                viewCount.value = count + " views";
            } else {
                viewCount.value = count + " view";
            }
        });
}

function fetchData(url) {
    let locator = new URL(url);
    let params = locator.searchParams;
    params.append('id', props.id);

    return fetch(locator, {
        method: 'GET',
        headers: { 'X-CSRF-Token': csrfToken }
    }).then(r => r.text(), reason => console.error(`Failed to fetch data: ${reason}`));
}
</script>

<template>
    <tr @click="redirect" class="flex flex-row items-center rounded-lg border-solid border-2 m-4">
        <td class="p-8 flex-shrink-0">
            <i v-if="!gravatarHash" class="fa-solid fa-circle-user fa-3x"></i>
            <Gravatar v-else :hash="gravatarHash" :large="false" :size="70"></Gravatar>
        </td>
        <td class="center">
            <span class="text-white text-lg font-bold">{{ title }}</span>
            <div class="details">
                <div class="category">
                    <svg class="inline-block w-[8px] h-[16px]">
                        <g :fill="getCategory().getColor()">
                            <rect width="100%" height="100%"/>
                        </g>
                    </svg>
                    <span>{{ getCategory().getName() }}</span>
                </div>
                <div class="author">
                    <i class="fa-solid fa-user align-text-bottom"></i>
                    <span>{{ authorName }}</span>
                </div>
            </div>
        </td>
        <td class="right">
            <div>
                <i class="fa-solid fa-clock"></i>
                <span class="px-2">{{ createdTime }}</span>
            </div>
            <div>
                <i class="fa-solid fa-eye fa-sm"></i>
                <span class="px-2">{{ viewCount }}</span>
            </div>
        </td>
    </tr>
</template>

<style scoped>
span {
    color: var(--fg);
}
svg {
    border-width: 1px;
    border-color: var(--fg);
}
tr:hover {
    cursor: pointer;
    background-color: var(--fg);
}
tr:hover span, tr:hover i {
    color: var(--bg);
}
tr:hover svg {
    border-color: var(--bg);
}
td.center {
    flex-grow: 1;
    flex-shrink: 1;
}
td.right {
    display: flex;
    flex-direction: column;
    row-gap: 1rem;
    width: 170px;
}
td.right span {
    overflow: hidden;
    white-space: nowrap;
    text-overflow: ellipsis;
}
div.category, div.author {
    position: relative;
}
div.category > span,
div.author > span {
    position: relative;
    margin-left: 8px;
    font-size: 0.875rem;
    line-height: 1.25rem;
}
div.details {
    display: flex;
    flex-direction: row;
    column-gap: 2rem;
}

@media (max-width: 600px) {
    div.details {
        flex-direction: column;
        row-gap: 4px;
    }
    div.category > span,
    div.author > span {
        position: absolute;
        left: 1.5rem;
        margin-left: 0;
    }
    td.right {
        width: 120px;
    }
}
</style>
