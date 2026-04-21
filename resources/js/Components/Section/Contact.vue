<script setup>
import { MapPinIcon, HomeIcon } from '@heroicons/vue/24/solid';
</script>

<template>
    <section
        class="flex flex-col lg:flex-row-reverse justify-center items-center lg:justify-stretch gap-[25px] md:gap-[50px] lg:gap-[100px] py-[50px] md:py-[75px] lg:py-[125px] bg-gray-50 dark:bg-gray-800">
        <!-- Information -->
        <div
            class="flex flex-col items-center justify-center lg:items-start gap-[15px] md:gap-[25px] lg:gap-[75px] px-[10px] md:px-[75px] lg:ps-0 lg:pe-[125px]">
            <div class="w-full flex flex-col items-stretch gap-10px">
                <h2 class="mb-4 text-3xl font-extrabold text-gray-900 dark:text-white">Hubungi Kami</h2>
            </div>

            <div
                class="flex flex-col md:flex-row items-center md:items-start md:justify-stretch gap-[15px] md:gap-[25px] lg:gap-[75px]">
                <!-- Address -->
                <div class="flex flex-col items-center lg:items-start gap-[10px] md:gap-[15px] w-full">
                    <HomeIcon class="size-8 md:size-10 lg:size-12 text-blue-900 dark:text-blue-700" />
                    <h5 class="text-center lg:text-start font-medium text-gray-900 dark:text-gray-100">Head Office</h5>
                    <p class="text-center lg:text-start text-gray-700 dark:text-gray-300">
                        Gedung Nucira Annex Building Sayap Timur, Lantai 1, Tebet Timur, Jakarta Selatan.
                    </p>
                </div>
                <div class="flex flex-col items-center lg:items-start gap-[10px] md:gap-[15px] w-full">
                    <MapPinIcon class="size-8 md:size-10 lg:size-12 text-blue-900 dark:text-blue-700" />
                    <h5 class="text-center lg:text-start font-medium text-gray-900 dark:text-gray-100">
                        Logistik & Operations
                    </h5>
                    <p class="text-center lg:text-start text-gray-700 dark:text-gray-300">
                        Jl. Raya Tapos No.39, Ciriung, Cibinong, Jawa Barat.
                    </p>
                </div>
            </div>
        </div>

        <!-- Maps -->
        <div class="w-full h-[450px] lg:h-[720px] px-[10px] md:px-[75px] lg:px-0">
            <iframe class="w-full h-full rounded-[25px] md:rounded-[50px] lg:rounded-l-none lg:rounded-r-[75px]"
                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3964.4676275850993!2d106.85659847628635!3d-6.4622864935293025!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e69ebbbe8d8bc6d%3A0x5b8182b940925d67!2sPT%20Anugerah%20Cahaya%20Chandra!5e0!3m2!1sid!2sid!4v1776784783305!5m2!1sid!2sid"
                style="border:0;" allowfullscreen="" loading="lazy"
                referrerpolicy="no-referrer-when-downgrade"></iframe>
        </div>
    </section>
</template>