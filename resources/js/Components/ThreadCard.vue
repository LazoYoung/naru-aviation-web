<script setup>
const props = defineProps({
    "id": Number,
    "title": String,
    "category": String,
    "color": String,
    "author": String,
    "date": Date,
    "view": Number
});
const hour = 60;
const day = 24 * hour;
const month = 30 * day;
const year = 12 * month + 5 * day;

function getDateFormat() {
    let diff = Date.now() - props.date.getTime();

    let rem = Math.round(diff / 60000);
    let y = Math.floor(rem / year);
    rem -= y * year;
    let m = Math.floor(rem / month);
    rem -= m * month;
    let d = Math.floor(rem / day);
    rem -= d * day;
    let h = Math.floor(rem / hour);
    let min = rem - h * hour;

    // todo l18n
    if (y > 1) {
        return y + " years ago";
    }
    if (m > 1) {
        return m + " months ago";
    }
    if (d > 1) {
        return d + " days ago";
    }
    if (h > 1) {
        return h + " hours ago";
    }
    if (min > 1) {
        return min + " minutes ago";
    }
    return "Just now";
}

function getViewCount() {
    let count = props.view;

    // todo l18n
    if (count > 1) {
        return count + " views";
    } else {
        return count + " view";
    }
}

function getLink() {
    return route('forum.thread.show', {id: props.id});
}
</script>

<template>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <tr class="flex flex-row items-center rounded-lg border-solid border-2 m-4">
        <td class="p-8 flex-shrink-0">
            <i class="fa-solid fa-circle-user fa-3x"></i>
        </td>
        <td class="flex-grow">
            <a :href="getLink()">
                <div>
                    <span class="text-black text-lg font-bold">{{title}}</span>
                    <p class="block whitespace-nowrap overflow-hidden overflow-ellipsis w-fit">This is nothing more than just a placeholder.</p>
                    <div>
                        <svg class="inline-block w-[8px] h-[16px]">
                            <rect :style="{color: color}" width="100%" height="100%"/>
                        </svg>
                        <span class="text-black text-sm ps-2 pe-8">{{category}}</span>
                        <i class="fa-solid fa-user align-text-bottom"></i>
                        <span class="text-black text-sm ps-2">{{author}}</span>
                    </div>
                </div>
            </a>
        </td>
        <td class="flex flex-col flex-shrink-0 px-8">
            <div>
                <i class="fa-solid fa-clock align-text-bottom"></i>
                <span class="px-2">{{getDateFormat()}}</span>
            </div>
            <div>
                <i class="fa-solid fa-eye fa-sm align-text-bottom"></i>
                <span class="px-2">{{getViewCount()}}</span>
            </div>
        </td>
    </tr>
</template>
