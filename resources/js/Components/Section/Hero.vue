<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
    auth: Boolean
});
</script>

<template>
    <section :style="{ backgroundImage: 'url(\'/assets/images/hero.jpg\')' }"
        class="bg-center bg-no-repeat bg-dark bg-blend-multiply w-full">
        <div class="flex flex-col justify-center h-screen items-center px-4 text-center">
            <h1 class="mb-6 text-4xl font-bold tracking-tighter text-white md:text-5xl lg:text-6xl">
                Menggerakkan Bisnis Retail, Menghadirkan Inovasi.
            </h1>
            <p class="mb-8 text-base font-normal text-white md:text-xl sm:px-16 lg:px-48">
                Selamat datang di Portal Enterprise Resource Planning (ERP) PT Anugerah Cahaya Chandra. Sistem
                manajemen terpadu yang dirancang untuk mendukung efisiensi operasional dan transparansi finansial
                secara real-time.
            </p>
            <div class="flex flex-col space-y-4 sm:flex-row sm:justify-center sm:space-y-0 md:space-x-4">
                <a :href="route('filament.admin.auth.login')"
                    class="inline-flex items-center justify-center text-white bg-brand hover:bg-brand-strong box-border border border-transparent focus:ring-4 focus:ring-brand-medium shadow-xs font-medium rounded-base text-base px-5 py-3 focus:outline-none">
                    Getting started
                </a>
                <Link href="#about"
                    class="text-body bg-neutral-secondary-medium box-border border border-default-medium hover:bg-neutral-tertiary-medium hover:text-heading focus:ring-4 focus:ring-neutral-tertiary shadow-xs font-medium leading-5 rounded-base text-base px-5 py-3 focus:outline-none">
                    Learn more
                </Link>
            </div>
        </div>
    </section>
</template>