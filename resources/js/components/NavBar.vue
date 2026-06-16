<script setup lang="ts">
import { ref } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { useAuthStore } from '../stores/auth'

const router = useRouter()
const route = useRoute()
const auth = useAuthStore()
const mobileMenuOpen = ref(false)

const navigation = [
    { name: 'Dashboard', href: '/', icon: '📊' },
    { name: 'Profile', href: '/profile', icon: '👤' },
    { name: 'Rooms', href: '/rooms', icon: '🏥' },
    { name: 'Specialties', href: '/specialties', icon: '🔬' },
    { name: 'Patients', href: '/patients', icon: '👥' },
    { name: 'Prescriptions', href: '/prescriptions', icon: '📋' },
]

function isActive(href: string) {
    if (href === '/') return route.path === '/'
    return route.path.startsWith(href)
}

async function handleLogout() {
    await auth.logout()
    router.push({ name: 'login' })
}
</script>

<template>
    <nav class="bg-white dark:bg-gray-900 shadow-sm border-b border-gray-200 dark:border-gray-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <router-link to="/" class="text-xl font-bold text-indigo-600 dark:text-indigo-400">
                        Recetas
                    </router-link>
                    <div class="hidden sm:ml-10 sm:flex sm:space-x-4">
                        <router-link
                            v-for="item in navigation"
                            :key="item.name"
                            :to="item.href"
                            class="inline-flex items-center px-3 py-2 rounded-md text-sm font-medium transition-colors"
                            :class="isActive(item.href)
                                ? 'bg-indigo-50 text-indigo-700 dark:bg-indigo-900/50 dark:text-indigo-300'
                                : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50 dark:text-gray-400 dark:hover:text-gray-100 dark:hover:bg-gray-800'"
                        >
                            {{ item.icon }}
                            <span class="ml-2">{{ item.name }}</span>
                        </router-link>
                    </div>
                </div>
                <div class="flex items-center">
                    <span class="hidden sm:block text-sm text-gray-500 dark:text-gray-400 mr-4">
                        {{ auth.user?.first_name }} {{ auth.user?.last_name1 }}
                    </span>
                    <button
                        @click="handleLogout"
                        class="px-3 py-2 rounded-md text-sm font-medium text-gray-600 hover:text-gray-900 hover:bg-gray-50 dark:text-gray-400 dark:hover:text-gray-100 dark:hover:bg-gray-800 transition-colors"
                    >
                        Logout
                    </button>
                    <button
                        @click="mobileMenuOpen = !mobileMenuOpen"
                        class="sm:hidden ml-2 p-2 rounded-md text-gray-600 hover:text-gray-900 hover:bg-gray-50 dark:text-gray-400 dark:hover:text-gray-100 dark:hover:bg-gray-800"
                    >
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
        <div v-show="mobileMenuOpen" class="sm:hidden border-t border-gray-200 dark:border-gray-800">
            <div class="px-2 pt-2 pb-3 space-y-1">
                <router-link
                    v-for="item in navigation"
                    :key="item.name"
                    :to="item.href"
                    @click="mobileMenuOpen = false"
                    class="block px-3 py-2 rounded-md text-base font-medium"
                    :class="isActive(item.href)
                        ? 'bg-indigo-50 text-indigo-700 dark:bg-indigo-900/50 dark:text-indigo-300'
                        : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50 dark:text-gray-400 dark:hover:text-gray-100 dark:hover:bg-gray-800'"
                >
                    {{ item.icon }} {{ item.name }}
                </router-link>
            </div>
        </div>
    </nav>
</template>
