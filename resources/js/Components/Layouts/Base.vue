<script setup>
import { Head } from '@inertiajs/vue3'
import TopBar from '../Navigation/TopBar.vue'
</script>

<template>

    <Head>
        <title>PT Anugerah Cahaya Chandra</title>
        <link rel="icon" type="image/png" href="/assets/logo/white.svg">
    </Head>

    <TopBar />

    <slot />
</template>