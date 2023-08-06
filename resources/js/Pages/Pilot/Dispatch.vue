<script setup>
import MainLayout from "@/Layouts/MainLayout.vue";
import {router, usePage} from "@inertiajs/vue3";
import {reactive} from "vue";

const token = usePage().props.auth['csrf_token'];
const url = route('pilot.dispatch.submit');
const form = reactive({
    aircraft: null, // 비행 기종
    flight_number: null, // 비행 편명
    off_block: null, // 출발 날짜 및 시각 (국제표준시간, ISO 8601 문자열)
    origin: null, // 출발 공항
    destination: null, // 도착 공항
    route: null, // 비행 경로
    remarks: null, // 비고 (공란 가능)
});

function submit() {
    router.post(url, form);
}
</script>

<template>
    <Head title="Dispatch"/>

    <MainLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Dispatch</h2>
        </template>

        <div class="py-12">
            <!-- TODO write the content here -->
        </div>
    </MainLayout>
</template>
