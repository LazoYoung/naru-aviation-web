<script setup>
import { Loader } from "@googlemaps/js-api-loader"
import {onMounted} from "vue";
import {fetchJSON} from "@/api.js";
import {usePage} from "@inertiajs/vue3";

const csrfToken = usePage().props.auth['csrf_token'];
const props = defineProps(['apiKey']);
const loader = new Loader({
    apiKey: props.apiKey,
});

onMounted(() => {
    loader.load().then(async () => {
        const {Map} = await google.maps.importLibrary('maps');
        const map = new Map(document.getElementById('map'), {
            center: {lat: 37.0, lng: 128.644},
            zoom: 1,
            mapId: 'DEMO_MAP_ID',
        });

        await fetchAirports(map);
    });
});

async function fetchAirports(map) {
    let data = await fetchJSON(route('airport.fetch.all'), csrfToken);

    if (!data) return;

    for (let elem of JSON.parse(data)) {
        await addMarker(map, elem);
    }
}

async function addMarker(map, airport) {
    let {AdvancedMarkerElement} = await google.maps.importLibrary('marker');
    let latitude = parseFloat(airport.latitude);
    let longitude = parseFloat(airport.longitude);
    const infoWindow = new google.maps.InfoWindow();
    let marker = new AdvancedMarkerElement({
        map: map,
        position: { lat: latitude, lng: longitude },
        title: airport.name,
    });
    marker.addListener("click", () => {
        infoWindow.close();
        infoWindow.setContent(marker.title);
        infoWindow.open(marker.map, marker);
    });
}
</script>

<template>
    <div id="map" class="h-full w-full"/>
</template>
