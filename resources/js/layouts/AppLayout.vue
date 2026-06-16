<script setup>
import NavBar from '../components/NavBar.vue'
</script>

<template>
    <div class="min-h-screen bg-gray-50 dark:bg-gray-950">
        <NavBar />
        <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <router-view />
        </main>
    </div>
</template>
