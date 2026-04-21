<script setup>

</script>

<template>
    <section class="bg-gray-50 dark:bg-gray-800 py-16" id="about">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 lg:gap-12 px-4 md:px-24 lg:px-32 lg:py-12">
            <figure
                class="col-span-1 relative max-w-screen-sm transition-all duration-300 cursor-pointer filter grayscale hover:grayscale-0">
                <img alt="Story" :src="'assets/images/accounting.jpg'"
                    class="w-full object-cover rounded-base shadow-xl dark:shadow-gray-800">
            </figure>
            <div class="col-span-1 flex flex-col justify-center h-full">
                <h2 class="mb-4 text-3xl font-extrabold text-gray-900 dark:text-white">
                    Kerja Keras, <strong class="text-sky-900 dark:text-sky-500">Kerja Cerdas.</strong>
                </h2>
                <p class="text-justify lg:text-left text-gray-500 sm:text-xl dark:text-gray-400">
                    Di PT Anugerah Cahaya Chandra, kami percaya bahwa perpaduan antara dedikasi tinggi dan teknologi
                    adalah kunci menuju keunggulan. Sebagai perusahaan yang terus berkembang di industri retail, kami
                    tidak hanya berfokus pada perluasan pasar, tetapi juga pada kemajuan infrastruktur teknologi di
                    dalam ekosistem kerja kami.
                </p>
                <p class="text-justify lg:text-left mt-4 text-gray-500 sm:text-xl dark:text-gray-400">
                    Melalui sistem ERP Finance ini, kami memastikan setiap langkah bisnis tercatat dengan akurat, setiap
                    keputusan didukung oleh data yang valid, dan setiap tim—mulai dari staf operasional hingga manajemen
                    tingkat atas—dapat berkolaborasi dengan mulus.
                </p>
            </div>
        </div>
    </section>
</template>