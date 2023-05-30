<script setup>
// const props = defineProps({
//     "id": Number,
//     "title": String,
//     "category": String,
//     "color": String,
//     "author": String,
//     "date": Date,
//     "view": Number
// });
import {ref} from "vue";
import {usePage} from "@inertiajs/vue3";
import {Category} from "@/category.ts";

const props = defineProps(['thread']);
const csrfToken = usePage().props['csrf_token'];
const id = props.thread['id'];
const title = props.thread['title'];
const contentPeek = ref('');
const authorName = ref('N/A');
const createdTime = ref('N/A');
const viewCount = ref('0 view');

fetchContent();
fetchAuthorName();
fetchCreatedTime();
fetchViewCount();

// function getDateFormat() {
//     let diff = Date.now() - props.date.getTime();
//
//     let rem = Math.round(diff / 60000);
//     let y = Math.floor(rem / year);
//     rem -= y * year;
//     let m = Math.floor(rem / month);
//     rem -= m * month;
//     let d = Math.floor(rem / day);
//     rem -= d * day;
//     let h = Math.floor(rem / hour);
//     let min = rem - h * hour;
//
//     // todo l18n
//     if (y > 1) {
//         return y + " years ago";
//     }
//     if (m > 1) {
//         return m + " months ago";
//     }
//     if (d > 1) {
//         return d + " days ago";
//     }
//     if (h > 1) {
//         return h + " hours ago";
//     }
//     if (min > 1) {
//         return min + " minutes ago";
//     }
//     return "Just now";
// }

function getLink() {
    return route('forum.thread.show', { id: props.thread['id'] });
}


function getCategory() {
    return Category.byId(props.thread['category']);
}

function fetchContent() {
    fetchData(route('forum.thread.content-peek'))
        .then(text => contentPeek.value = text);
}

function fetchAuthorName() {
    fetchData(route('forum.thread.author-name'))
        .then(text => authorName.value = text);
}

function fetchCreatedTime() {
    fetchData(route('forum.thread.created-time'))
        .then(text => createdTime.value = text);
}

function fetchViewCount() {
    fetchData(route('forum.thread.view-count'))
        .then(text => parseInt(text ? text : '0'))
        .then(count => {
            // todo l18n
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
    params.append('id', props.thread['id']);

    return fetch(locator, {
        method: 'GET',
        headers: { 'X-CSRF-Token': csrfToken }
    }).then(r => r.text(), reason => window.alert(`Failed to fetch data: ${reason}`));
}
</script>

<template>
    <tr class="flex flex-row items-center rounded-lg border-solid border-2 m-4">
        <td class="p-8 flex-shrink-0">
            <i class="fa-solid fa-circle-user fa-3x"></i>
        </td>
        <td class="flex-grow">
            <a :href="getLink()">
                <div>
                    <span class="text-black text-lg font-bold">{{ title }}</span>
                    <p class="block whitespace-nowrap overflow-hidden overflow-ellipsis w-fit">{{ contentPeek }}</p>
                    <div>
                        <svg class="inline-block w-[8px] h-[16px]">
                            <g :fill="getCategory().getColor()">
                                <rect width="100%" height="100%"/>
                            </g>
                        </svg>
                        <span class="text-black text-sm ps-2 pe-8">{{ getCategory().getName() }}</span>
                        <i class="fa-solid fa-user align-text-bottom"></i>
                        <span class="text-black text-sm ps-2">{{ authorName }}</span>
                    </div>
                </div>
            </a>
        </td>
        <td class="flex flex-col flex-shrink-0 px-8">
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
