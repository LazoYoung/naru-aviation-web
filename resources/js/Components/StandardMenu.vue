<template>
    <a class="element link" :href="route('home')">
        <label><span class="name">About</span></label>
    </a>
    <div class="element dropdown">
        <label>
            <span class="name">Projects</span>
            <span class="arrow">&#x25BC;</span>
        </label>
        <div class="content">
            <a
                    href="https://github.com/LazoYoung/NaruACARS"
                    target="_blank"
            >
                <label>Naru ACARS</label>
                <span class="arrow">&#x1F865;</span>
            </a>
            <a
                    href="https://github.com/LazoYoung/naru-aviation-web"
                    target="_blank"
            >
                <label>Naru web</label>
                <span class="arrow">&#x1F865;</span>
            </a>
        </div>
    </div>
    <div class="element dropdown">
        <label>
            <span class="name">Community</span>
            <span class="arrow">&#x25BC;</span>
        </label>
        <div class="content">
            <a :href="route('forum.browse')"><label>Forum</label></a>
            <a :href="route('image.show.gallery')"><label>Gallery</label></a>
            <a :href="route('event.show.calendar')"><label>Official Events</label></a>
        </div>
    </div>
    <a class="element link" :href="route('pilot.dashboard')">
        <label>
            <span class="name">Pilot Center</span>
            <span class="arrow-link">&#x1F865;</span>
        </label>
    </a>
</template>