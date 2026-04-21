<script setup>
import Footer from '../Components/Navigation/Footer.vue';
import NavBar from '../Components/Navigation/NavBar.vue';
import About from '../Components/Section/About.vue';
import Contact from '../Components/Section/Contact.vue';
import Hero from '../Components/Section/Hero.vue';
import Vision from '../Components/Section/Vision.vue';

defineProps({
    user: Object,
    auth: Boolean,
});
</script>

<template>
    <NavBar :user="user?.name ?? ''" :auth />

    <Hero :auth />
    <About />
    <Vision />
    <Contact />

    <Footer />
</template>
