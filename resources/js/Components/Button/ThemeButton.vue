<script setup>
import { ref, onMounted } from 'vue';
import { MoonIcon, SunIcon } from '@heroicons/vue/24/solid';

// Gunakan variabel reaktif Vue, bukan document.getElementById
const isDark = ref(false);

// onMounted berjalan SETELAH HTML siap di layar
onMounted(() => {
    if (localStorage.getItem('color-theme') === 'dark' || (!('color-theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
        isDark.value = true;
        document.documentElement.classList.add('dark');
    } else {
        isDark.value = false;
        document.documentElement.classList.remove('dark');
    }
});

// Fungsi untuk mengganti tema (dipanggil oleh @click di HTML)
const toggleTheme = () => {
    isDark.value = !isDark.value;

    if (isDark.value) {
        document.documentElement.classList.add('dark');
        localStorage.setItem('color-theme', 'dark');
    } else {
        document.documentElement.classList.remove('dark');
        localStorage.setItem('color-theme', 'light');
    }
};
</script>

<template>
    <div class="flex items-center">
        <label for="ThemeCondition" class="relative h-8 w-14 cursor-pointer [-webkit-tap-highlight-color:transparent]">
            <input type="checkbox" id="ThemeCondition" class="peer sr-only" :checked="isDark" @change="toggleTheme" />

            <span
                class="absolute inset-0 m-auto h-4 rounded-full bg-gray-300 dark:bg-gray-600 transition-colors"></span>

            <span class="absolute inset-y-0 start-0 m-auto size-7 rounded-full bg-white shadow-sm transition-all duration-300 flex items-center justify-center 
                       peer-checked:start-7 peer-checked:bg-gray-800">
                <SunIcon v-if="!isDark" class="size-5 text-yellow-500 transition-all" />

                <MoonIcon v-else class="size-5 text-blue-400 transition-all" />
            </span>
        </label>
    </div>
</template>