<script setup>
import {onMounted, ref} from 'vue';
import Logo from '@/Components/Logo.vue';
import {usePage} from '@inertiajs/vue3';
import {getGravatarHash} from "@/api.js";
import { Link } from '@inertiajs/vue3';
import StandardMenu from "@/Components/StandardMenu.vue";
import PilotMenu from "@/Components/PilotMenu.vue";

const auth = usePage().props.auth;
const content = ref();
let lastWidth = window.innerWidth;
let lastWidthChange = null;
let nav = null;

defineProps({
    pilot: {
        type: Boolean,
        default: false
    }
});

onMounted(() => {
    nav = document.querySelector("#nav");
    window.addEventListener("resize", () => {
        if (
            window.innerWidth > lastWidth &&
            window.innerWidth > 1200 &&
            lastWidthChange !== "wide"
        ) {
            closeAll();
            lastWidthChange = "wide";
        } else if (
            window.innerWidth < lastWidth &&
            window.innerWidth <= 1200 &&
            lastWidthChange !== "narrow"
        ) {
            closeAll();
            lastWidthChange = "narrow";
        }
        lastWidth = window.innerWidth;
    });
    for (const dropdown of nav.querySelectorAll(
        ".menu-wide .element.dropdown"
    )) {
        dropdown.addEventListener("click", (event) => {
            let target = event.target;
            while (!target.classList.contains("element")) {
                if (target.classList.contains("content")) {
                    return;
                }
                target = target.parentElement;
            }
            target.getAttribute("opened") === ""
                ? closeWideDropdown(target)
                : openWideDropdown(target);
            nav.querySelectorAll(".menu-wide .element.dropdown[opened]")
                .length === 0
                ? hideBackground()
                : showBackground();
        });
    }
    for (const dropdown of nav.querySelectorAll(
        ".menu-narrow .element.dropdown"
    )) {
        dropdown.addEventListener("click", (event) => {
            let target = event.target;
            while (!target.classList.contains("element")) {
                if (target.classList.contains("content")) {
                    return;
                }
                target = target.parentElement;
            }
            target.getAttribute("opened") === ""
                ? closeNarrowDropdown(target)
                : openNarrowDropdown(target);
        });
    }

    let account = document.querySelector("#account");
    if (account) {
        account.addEventListener("click", () => expandProfile(account));
    }

    document
        .querySelector("#nav-background")
        .addEventListener("click", closeAll);
    document
        .querySelector("#button-nav-narrow")
        .addEventListener("click", () => {
            document.querySelector("#button-nav-narrow").classList.contains("cross")
                ? closeNarrowMenu()
                : openNarrowMenu();
        });
});

function getProfileSource() {
    let hash = getGravatarHash(auth.user.email);
    return `https://www.gravatar.com/avatar/${hash}?s=512`;
}

function closeAll() {
    closeWideDropdowns();
    closeNarrowDropdowns();
    foldProfile();
    closeNarrowMenu();
    hideBackground();
}

function showBackground() {
    document.querySelector("#nav-background").classList.add("show");
}

function hideBackground() {
    document.querySelector("#nav-background").classList.remove("show");
}

function openWideDropdown(element) {
    if (element) {
        closeAll();
        element.setAttribute("opened", "");
    }
}

function closeWideDropdowns() {
    for (const other of nav.querySelectorAll(
        ".menu-wide .element.dropdown"
    )) {
        closeWideDropdown(other);
    }
}

function closeWideDropdown(element) {
    element.removeAttribute("opened");
}

function expandProfile(element) {
    if (element) {
        closeAll();
        showBackground();
        element.setAttribute("expand", "");
        document
            .querySelector("#button-nav-narrow")
            .classList.add("hidden");
    }
}

function foldProfile() {
    let elem = document.querySelector("#account");
    if (elem) {
        elem.removeAttribute("expand");
        document
            .querySelector("#button-nav-narrow")
            .classList.remove("hidden");
    }
}

function openNarrowMenu() {
    closeAll();
    showBackground();
    document.querySelector("#button-nav-narrow").classList.add("cross");
    document.querySelector("#nav-bottom .menu-narrow").classList.add("show");
}

function closeNarrowMenu() {
    document.querySelector("#button-nav-narrow").classList.remove("cross");
    document.querySelector("#nav-bottom .menu-narrow").classList.remove("show");
    hideBackground();
}

function openNarrowDropdown(element) {
    if (element) {
        closeNarrowDropdowns();
        element.setAttribute("opened", "");
    }
}

function closeNarrowDropdowns() {
    for (const other of nav.querySelectorAll(
        ".menu-narrow .element.dropdown"
    )) {
        closeNarrowDropdown(other);
    }
}

function closeNarrowDropdown(element) {
    element.removeAttribute("opened");
}
</script>

<template>
    <nav id="nav">
        <div id="nav-background"></div>
        <div id="nav-top">
            <div class="left">
                <a :href="route('home')" class="logo">
                    <div class="icon">
                        <Logo :color="pilot ? 'blue' : 'white'"></Logo>
                    </div>
                    <div class="text">
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            viewBox="0 0 528.09 141.36"
                        >
                            <g id="_레이어_1-2" data-name="레이어 1">
                                <g>
                                    <g>
                                        <path
                                            d="m50.72,0v60.09h-12.66l-16.71-26.75c-3.8-5.99-7.68-12.24-10.72-18.06.34,10.63.34,20.26.34,29.12v15.7H0V0h12.66l16.8,26.75c3.38,5.49,7.6,12.41,10.63,18.06-.25-10.63-.34-20.26-.34-29.12V0h10.97Z"
                                        />
                                        <path
                                            d="m87.94,60.09c-.67-1.18-1.01-3.21-1.18-5.32-2.87,4.05-7.6,6.33-14.01,6.33-9.28,0-15.7-4.64-15.7-13.08,0-7.26,4.39-12.41,16.71-13.59l6.58-.59c4.05-.51,6.16-1.77,6.16-4.98,0-3.38-1.77-5.4-7.93-5.4s-8.61,1.6-9.03,6.92h-10.47c.59-9.2,5.99-14.85,19.58-14.85s18.15,5.23,18.15,13.08v23.21c0,3.12.59,6.58,1.77,8.27h-10.63Zm-1.43-17.22v-3.97c-1.1,1.01-2.7,1.52-4.98,1.77l-5.74.68c-5.91.68-7.85,2.79-7.85,6.25s2.45,5.91,7.34,5.91c5.65,0,11.23-3.04,11.23-10.63Z"
                                        />
                                        <path
                                            d="m130.82,16.04v9.79h-2.7c-7.85,0-12.58,3.88-12.58,12.15v22.11h-10.63V16.54h10.3v7.76c2.11-4.9,6.25-8.44,13-8.44.93,0,1.69,0,2.62.17Z"
                                        />
                                        <path
                                            d="m175.3,16.54v43.55h-10.3v-6.33c-2.87,4.22-7.43,7.34-13.67,7.34-9.03,0-15.19-5.32-15.19-15.7v-28.86h10.63v27.09c0,5.57,2.7,8.27,8.19,8.27,4.64,0,9.71-3.21,9.71-10.97v-24.39h10.63Z"
                                        />
                                        <path
                                            d="m241.04,46.84h-26.33l-4.64,13.25h-11.73L221.21,0h13.34l23.29,60.09h-12.07l-4.73-13.25Zm-3.46-9.71l-2.36-6.5c-2.11-5.49-4.98-13.5-7.43-20.59-2.45,7.09-5.32,15.11-7.34,20.59l-2.28,6.5h19.41Z"
                                        />
                                        <path
                                            d="m296.83,16.54l-16.2,43.55h-12.24l-16.12-43.55h11.39l4.22,11.73c2.28,7.17,4.64,14.94,6.84,22.2,2.19-7.34,4.56-15.02,6.84-22.28l4.22-11.65h11.06Z"
                                        />
                                        <path
                                            d="m300.97,16.54h10.63v43.55h-10.63V16.54Zm.08-15.61h10.47v9.96h-10.47V.93Z"
                                        />
                                        <path
                                            d="m348.74,60.09c-.67-1.18-1.01-3.21-1.18-5.32-2.87,4.05-7.6,6.33-14.01,6.33-9.28,0-15.7-4.64-15.7-13.08,0-7.26,4.39-12.41,16.71-13.59l6.58-.59c4.05-.51,6.16-1.77,6.16-4.98,0-3.38-1.77-5.4-7.93-5.4s-8.61,1.6-9.03,6.92h-10.47c.59-9.2,5.99-14.85,19.58-14.85s18.15,5.23,18.15,13.08v23.21c0,3.12.59,6.58,1.77,8.27h-10.63Zm-1.43-17.22v-3.97c-1.1,1.01-2.7,1.52-4.98,1.77l-5.74.68c-5.91.68-7.85,2.79-7.85,6.25s2.45,5.91,7.34,5.91c5.65,0,11.22-3.04,11.22-10.63Z"
                                        />
                                        <path
                                            d="m378.45,24.64v23.55c0,2.87,1.43,3.8,4.98,3.8h3.63v8.1c-1.94.17-3.88.34-5.57.34-9.2,0-13.59-3.12-13.59-11.14v-24.64h-6.75v-8.1h6.75V3.88h10.55v12.66h8.61v8.1h-8.61Z"
                                        />
                                        <path
                                            d="m393.3,16.54h10.63v43.55h-10.63V16.54Zm.08-15.61h10.47v9.96h-10.47V.93Z"
                                        />
                                        <path
                                            d="m410.6,38.32c0-13.84,8.95-22.79,22.2-22.79s22.11,8.95,22.11,22.79-8.86,22.79-22.11,22.79-22.2-8.95-22.2-22.79Zm33.51,0c0-9.2-4.47-14.01-11.31-14.01s-11.31,4.81-11.31,14.01,4.39,14.01,11.31,14.01,11.31-4.81,11.31-14.01Z"
                                        />
                                        <path
                                            d="m500.74,31.23v28.86h-10.63v-27.09c0-5.57-2.79-8.27-8.19-8.27-4.64,0-9.71,3.21-9.71,10.97v24.39h-10.63V16.54h10.3v6.25c2.79-4.14,7.43-7.26,13.59-7.26,9.12,0,15.28,5.32,15.28,15.7Z"
                                        />
                                    </g>
                                    <g>
                                        <path
                                            d="m49.46,140.09h-11.73c-1.1-1.77-1.52-4.22-1.6-7.17l-.25-6.58c-.34-7.68-3.54-9.79-9.62-9.79h-14.85v23.55H0v-60.09h27.26c13.84,0,21.18,6.84,21.18,16.96,0,6.92-3.54,12.07-10.04,14.35,6.67,2.11,8.44,6.58,8.69,12.83l.51,8.95c.08,2.62.67,5.23,1.86,7.01Zm-22.87-32.49c6.16,0,10.13-3.21,10.13-9.37s-3.97-8.86-10.55-8.86h-14.77v18.23h15.19Z"
                                        />
                                        <path
                                            d="m96.55,121.61h-32.58c.76,7.76,5.15,11.23,11.31,11.23,5.06,0,8.36-2.36,9.96-6.25h10.21c-2.19,8.86-9.79,14.52-20.26,14.52-12.83,0-21.69-8.86-21.69-22.79s8.61-22.79,21.52-22.79,21.52,9.71,21.52,23.55v2.53Zm-32.49-7.43h21.86c-.84-6.16-4.64-10.38-10.97-10.38-5.66,0-9.88,3.21-10.89,10.38Z"
                                        />
                                        <path
                                            d="m100.52,126.25h10.38c.59,5.49,3.38,7.43,9.71,7.43s8.61-1.94,8.61-5.23-1.94-4.64-8.52-5.74l-4.98-.84c-9.37-1.6-14.26-5.74-14.26-13.17,0-8.19,6.41-13.17,18.06-13.17,13.42,0,18.91,5.82,19.24,14.77h-10.04c-.25-5.57-3.71-7.17-9.2-7.17-5.15,0-7.6,1.94-7.6,5.06s2.53,4.47,7.26,5.32l5.49.84c10.47,1.86,15.11,5.74,15.11,13.25,0,8.78-7.17,13.67-19.33,13.67s-19.24-4.47-19.92-15.02Z"
                                        />
                                        <path
                                            d="m186.52,121.61h-32.58c.76,7.76,5.15,11.23,11.31,11.23,5.06,0,8.36-2.36,9.96-6.25h10.21c-2.19,8.86-9.79,14.52-20.26,14.52-12.83,0-21.69-8.86-21.69-22.79s8.61-22.79,21.52-22.79,21.52,9.71,21.52,23.55v2.53Zm-32.49-7.43h21.86c-.84-6.16-4.64-10.38-10.97-10.38-5.66,0-9.88,3.21-10.89,10.38Z"
                                        />
                                        <path
                                            d="m221.47,140.09c-.68-1.18-1.01-3.21-1.18-5.32-2.87,4.05-7.6,6.33-14.01,6.33-9.28,0-15.7-4.64-15.7-13.08,0-7.26,4.39-12.41,16.71-13.59l6.58-.59c4.05-.51,6.16-1.77,6.16-4.98,0-3.38-1.77-5.4-7.93-5.4s-8.61,1.6-9.03,6.92h-10.47c.59-9.2,5.99-14.85,19.58-14.85s18.15,5.23,18.15,13.08v23.21c0,3.12.59,6.58,1.77,8.27h-10.63Zm-1.43-17.22v-3.97c-1.1,1.01-2.7,1.52-4.98,1.77l-5.74.68c-5.91.67-7.85,2.79-7.85,6.25s2.45,5.91,7.34,5.91c5.65,0,11.22-3.04,11.22-10.63Z"
                                        />
                                        <path
                                            d="m264.34,96.04v9.79h-2.7c-7.85,0-12.58,3.88-12.58,12.15v22.11h-10.63v-43.55h10.3v7.76c2.11-4.9,6.25-8.44,13-8.44.93,0,1.69,0,2.62.17Z"
                                        />
                                        <path
                                            d="m266.2,118.32c0-13.93,8.52-22.79,21.52-22.79,11.39,0,18.99,6.5,20.26,16.71h-10.63c-1.01-5.4-4.9-7.85-9.62-7.85-6.33,0-10.63,4.39-10.63,13.93s4.47,13.93,10.63,13.93c4.81,0,8.95-2.62,9.79-8.36h10.63c-1.18,10.63-9.28,17.22-20.42,17.22-12.91,0-21.52-8.86-21.52-22.79Z"
                                        />
                                        <path
                                            d="m353.13,111.23v28.86h-10.63v-27.09c0-5.57-2.79-8.27-8.19-8.27-4.64,0-9.71,3.21-9.71,10.97v24.39h-10.63v-60.09h10.63v22.37c2.87-3.97,7.34-6.84,13.25-6.84,9.12,0,15.28,5.32,15.28,15.7Z"
                                        />
                                        <path
                                            d="m379.71,117.47v-37.47h11.39v37.56c0,9.45,5.15,13.93,13.76,13.93s13.76-4.47,13.76-13.93v-37.56h11.39v37.47c0,15.61-9.71,23.89-25.15,23.89s-25.15-8.27-25.15-23.89Z"
                                        />
                                        <path
                                            d="m477.53,111.23v28.86h-10.63v-27.09c0-5.57-2.79-8.27-8.19-8.27-4.64,0-9.71,3.21-9.71,10.97v24.39h-10.63v-43.55h10.3v6.25c2.79-4.14,7.43-7.26,13.59-7.26,9.12,0,15.28,5.32,15.28,15.7Z"
                                        />
                                        <path
                                            d="m486.14,96.54h10.63v43.55h-10.63v-43.55Zm.08-15.61h10.47v9.96h-10.47v-9.96Z"
                                        />
                                        <path
                                            d="m519.48,104.64v23.55c0,2.87,1.43,3.8,4.98,3.8h3.63v8.1c-1.94.17-3.88.34-5.57.34-9.2,0-13.59-3.12-13.59-11.14v-24.64h-6.75v-8.1h6.75v-12.66h10.55v12.66h8.61v8.1h-8.61Z"
                                        />
                                    </g>
                                </g>
                            </g>
                        </svg>
                    </div>
                </a>
            </div>
            <div class="center">
                <div class="menu menu-wide">
                    <PilotMenu v-if="pilot"></PilotMenu>
                    <StandardMenu v-else></StandardMenu>
                </div>
            </div>
            <div class="right">
                <div v-if="auth.user" id="account">
                    <div class="profile">
                        <img class="image" :src="getProfileSource()" alt="image">
                        <div class="name">{{auth.user.name}}</div>
                    </div>
                    <a :href="route('profile.edit')" class="element">
                        <i class="fa-solid fa-user w-4 me-2"></i>
                        <span>Profile</span>
                    </a>
                    <Link :href="route('logout')" method="post" class="element">
                        <i class="fa-solid fa-arrow-right-from-bracket w-4 me-2"></i>
                        <span>Sign out</span>
                    </Link>
                </div>
                <div v-else id="guest">
                    <a :href="route('login')">
                        <button small>Login</button>
                    </a>
                    <a :href="route('register')">
                        <button small>Sign up</button>
                    </a>
                </div>
                <div class="narrowmenu">
                    <div id="button-nav-narrow">
                        <div class="line top"></div>
                        <div class="line bottom"></div>
                    </div>
                </div>
            </div>
        </div>
        <div id="nav-bottom">
            <div class="narrow-nav menu menu-narrow">
                <PilotMenu v-if="pilot"></PilotMenu>
                <StandardMenu v-else></StandardMenu>
            </div>
        </div>
    </nav>
</template>

<style>
#nav {
    --nav-bg: black;
    --nav-fg: white;
    --nav-button-bg: rgb(45, 45, 45);
    --nav-button-bg-hover: rgb(80, 80, 80);

    position: fixed;
    width: 100%;
    height: 100vh;
    overflow: hidden;

    z-index: 20000000;
    pointer-events: none;
}

#nav-background {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgb(0, 0, 0, 0.5);
    backdrop-filter: blur(10px);
    opacity: 0;
    transition: opacity 0.1s ease-out;
    pointer-events: none;
}

#nav-background.show {
    opacity: 1;
    transition: opacity 0.2s ease-out;
    pointer-events: all;
}

#nav .menu .element > label {
    display: flex;
    justify-content: flex-start;
    align-items: center;
}
#nav .menu .element > label > .name {
    font-size: 1.25rem;
    font-weight: 500;
    line-height: 1.25rem;
    word-break: keep-all;
    cursor: pointer;
}
#nav .menu .element > label:hover > .name {
    text-decoration: underline;
}
#nav .menu .element > label > .arrow {
    display: inline-block;
    font-size: 0.8rem;
    line-height: 1.25rem;
    font-weight: 500;
    margin-left: 0.35rem;
}

#nav .menu .element > label > .arrow-link {
    display: inline-block;
    font-size: 1.25rem;
    line-height: 1.25rem;
    font-weight: 500;
    margin: 0.35rem;
}

#nav-top {
    position: absolute;
    top: 0;
    left: 0;
    display: flex;
    width: 100%;
    height: 5rem;
    background-color: var(--nav-bg);
    align-items: center;
    justify-content: space-between;
    pointer-events: all;
    z-index: 20000000;
}
#nav-top > * {
    flex-wrap: nowrap;
}

#nav-top > .left {
    width: 18rem;
    padding-left: 2rem;
    display: flex;
    align-items: center;
}
#nav-top > .left > .logo {
    height: 2.6rem;
}
#nav-top .logo {
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: flex-start;
}
#nav-top .logo .icon {
    height: 100%;
}
#nav-top .logo .text {
    height: 70%;
    margin-left: 0.35rem;
}
#nav-top .logo .icon > img,
#nav-top .logo .text > img {
    height: 100%;
    fill: white;
}

#nav-top .logo .icon > svg,
#nav-top .logo .text > svg {
    height: 100%;
    fill: white;
}

#nav-top > .center {
    width: calc(100% - 40rem);
    max-width: 75vw;
}
#nav-top .menu-wide {
    display: flex;
    justify-content: flex-start;
    align-items: center;
    flex-wrap: wrap;
}
#nav-top .menu-wide .element {
    display: flex;
    position: relative;
    margin-left: 2.5rem;
    width: fit-content;
    justify-content: flex-start;
    align-items: center;
}
#nav-top .menu-wide .element:first-child {
    margin-left: 0;
}
#nav-top .menu-wide .element.dropdown[opened] > label > .arrow {
    transform: rotate(180deg);
}
#nav-top .menu-wide .element.dropdown > .content {
    display: none;
}
#nav-top .menu-wide .element.dropdown[opened] > .content {
    display: block;
    position: absolute;
    background: var(--nav-bg);
    padding: 0 0.75rem 0 0.75rem;
    top: 2rem;
    left: -0.75rem;
}
#nav-top .menu-wide .element.dropdown > .content > a {
    display: flex;
    width: max-content;
    font-size: 1.25rem;
    font-weight: 500;
    line-height: 1.25rem;
    margin: 0.75rem 0 0.75rem 0;
    word-break: keep-all;
    cursor: pointer;
    justify-content: flex-start;
    align-items: center;
}
#nav-top .menu-wide .element.dropdown > .content > a span {
    margin-left: 0.35rem;
}
#nav-top .menu-wide .element.dropdown > .content > a:hover label {
    text-decoration: underline;
    cursor: pointer;
}

#nav-top > .right {
    width: 18rem;
    padding-right: 2rem;
    display: flex;
    justify-content: flex-end;
}
#button-nav-narrow {
    width: 2.5rem;
    height: 2.5rem;
    position: relative;
    cursor: pointer;
    margin-left: 1rem;
    display: none;
}
#button-nav-narrow > .line {
    position: absolute;
    width: 100%;
    height: 0;
    border-bottom: solid 2px var(--nav-fg);
    transition: top 0.1s ease-out, bottom 0.1s ease-out, transform 0.1s ease-out;
}
#button-nav-narrow > .top {
    top: 0.75rem;
}
#button-nav-narrow > .bottom {
    bottom: 0.75rem;
}
#button-nav-narrow.cross > .top {
    top: calc(1.25rem - 1px);
    transform: rotate(45deg);
}
#button-nav-narrow.cross > .bottom {
    bottom: calc(1.25rem - 1px);
    transform: rotate(-45deg);
}

#guest {
    display: flex;
    align-items: center;
    column-gap: 1rem;
}
#account {
    border-radius: 5rem;
    height: 2.4rem;
    max-height: 2.4rem;
    padding: 0.35rem 0.65rem 0.35rem 0.35rem;
    background: var(--nav-button-bg);
    cursor: pointer;
    transition: max-height 0.5s ease-in-out;
}
#account[expand] {
    position: fixed;
    top: 1rem;
    display: flex;
    flex-direction: column;
    justify-content: flex-end;
    border-radius: 1rem;
    height: fit-content;
    max-height: 30rem;
    cursor: default;
    padding: 0.5rem 1rem 0.5rem 1rem;
    background: var(--nav-button-bg);
}
#account > .profile {
    display: flex;
    flex-direction: row;
    align-items: center;
}
#account[expand] > .profile {
    margin-bottom: 1.5rem;
}
#account .image {
    width: 1.8rem;
    height: 1.8rem;
    border-radius: 100%;
    border: solid 1px var(--nav-fg);
}
#account[expand] .image {
    width: 3.2rem;
    height: 3.2rem;
    border-radius: 100%;
    border: solid 1px var(--nav-fg);
}
#account .name {
    font-size: 1rem;
    font-weight: 500;
    margin-left: 0.5rem;
    cursor: pointer;
}
#account[expand] .name {
    font-weight: normal;
    margin-left: 1rem;
    cursor: default;
}
#account > .element {
    visibility: hidden;
}
#account[expand] > .element {
    visibility: visible;
    padding: 0 0 1rem 1rem;
    margin: 0.5rem 0 0.5rem 0;
    font-weight: bold;
    cursor: pointer;
}
#account:hover {
    background: var(--nav-button-bg-hover);
}
#account:hover .name {
    text-decoration: underline;
}
#account[expand]:hover .name {
    text-decoration: none;
}
#account[expand] .element:hover {
    text-decoration: underline;
}

#nav-bottom {
    position: absolute;
    top: 5rem;
    left: 0;
    width: 100%;
    height: calc(100% - 5rem);
    z-index: 19000000;
}

#nav-bottom .menu-narrow {
    position: absolute;
    top: -100%;
    left: 0;
    width: 100%;
    background: var(--nav-bg);
    pointer-events: all;
    overflow-x: hidden;
    opacity: 0;
    transition: top 0.2s ease-out, opacity 0.1s ease-out;
}
#nav-bottom .menu-narrow.show {
    top: 0;
    max-height: 100%;
    overflow-x: auto;
    opacity: 1;
    transition: top 0.2s ease-out, opacity 0.2s ease-out;
}
#nav-bottom .menu-narrow .element {
    display: block;
    width: calc(100% - 4rem);
    padding: 1rem 2rem;
    border-bottom: solid 1px var(--nav-fg);
}
#nav-bottom .menu-narrow .element:first-child {
    border-top: solid 1px var(--nav-fg);
}
#nav-bottom .menu-narrow .element.dropdown > .content {
    display: none;
}
#nav-bottom .menu-narrow .element.dropdown[opened] > .content {
    display: block;
    padding: 1rem 1rem 0 1rem;
    top: 2rem;
    left: -0.75rem;
}
#nav-bottom .menu-narrow .element.dropdown[opened] > label > .arrow {
    transform: rotate(180deg);
}
#nav-bottom .menu-narrow .element.dropdown > .content > a {
    display: flex;
    font-size: 1.25rem;
    font-weight: 500;
    line-height: 1.25rem;
    margin-top: 0.75rem;
    cursor: pointer;
    justify-content: flex-start;
    align-items: center;
}
#nav-bottom .menu-narrow .element.dropdown > .content > a:first-child {
    margin-top: 0;
}
#nav-bottom .menu-narrow .element.dropdown > .content > a span {
    margin-left: 0.35rem;
}
#nav-bottom .menu-narrow .element.dropdown > .content > a:hover label {
    text-decoration: underline;
    cursor: pointer;
}

@media (max-width: 1200px) {
    #nav-top > .center {
        display: none;
    }
    #button-nav-narrow {
        display: block;
    }
    #button-nav-narrow.hidden {
        display: none;
    }
}
@media (max-width: 900px) {
    #nav-top > .left {
        padding-left: 1rem;
    }
    #nav-top > .right {
        padding-right: 1rem;
    }
    #nav-bottom .menu-narrow .element {
        width: calc(100% - 2rem);
        padding: 1rem 1rem;
    }
}
@media (max-width: 600px) {
    #nav-top {
        height: 4rem;
    }
    #nav-top > .left > .logo > .text {
        display: none;
    }
    #nav-bottom {
        top: 4rem;
        height: calc(100% - 4rem);
    }
    #nav-bottom .menu-narrow.show {
        height: 100%;
    }
}
@media print {
    #nav-top {
        display: none;
    }
}
</style>
