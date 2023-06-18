<script setup>
import {Loader} from "@googlemaps/js-api-loader"
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
            zoom: 2,
            mapId: '3969ad62e13f6478',
            mapTypeControl: false,
            scaleControl: false,
            streetViewControl: false,
        });

        await renderMarkers(map);
    });
});

async function renderMarkers(map) {
    let data = await fetchJSON(route('airport.fetch.all'), csrfToken);

    if (!data) return;

    for (let elem of JSON.parse(data)) {
        await addMarker(map, elem);
    }
}

async function addMarker(map, airport) {
    let {InfoWindow} = await google.maps.importLibrary('maps');
    let {AdvancedMarkerElement, PinElement} = await google.maps.importLibrary('marker');
    let infoWindow = new InfoWindow();
    let latitude = parseFloat(airport.latitude);
    let longitude = parseFloat(airport.longitude);
    let icon = document.createElement('div');
    icon.innerHTML = '<i class="fa-solid fa-plane-up fa-lg"></i>';
    let pin = new PinElement({
        scale: 1.2,
        glyph: icon,
        glyphColor: '#2836dc',
        background: '#bebebe',
        borderColor: '#000000',
    });
    let marker = new AdvancedMarkerElement({
        map: map,
        position: { lat: latitude, lng: longitude },
        content: pin.element,
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
    <div id="map" class="h-full"/>
</template>
