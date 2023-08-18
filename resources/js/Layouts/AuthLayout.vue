<script setup>
import { Link } from '@inertiajs/vue3';
import Logo from "@/Components/Logo.vue";
</script>

<template>
    <main>
        <div>
            <Link :href="route('home')">
                <Logo class="w-24 h-24 fill-current"></Logo>
            </Link>
        </div>

        <div class="form">
            <slot />
        </div>
    </main>
</template>

<style scoped>
main {
    display: flex;
    flex-direction: column;
    align-items: center;
    min-height: 100vh;
    padding-top: 3rem;
    background-color: rgb(0, 0, 0);
}
div.form {
    width: 100%;
    margin-top: 1.5rem;
    padding: 1rem 1.5rem 1rem 1.5rem;
    background-color: var(--fg);
    overflow: hidden;
}
@media (min-width: 640px) {
    main {
        justify-content: center;
        padding-top: 0;
    }
    div.form {
        max-width: 28rem;
        border-radius: 0.5rem;
    }
}
</style>
