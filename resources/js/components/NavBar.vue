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
    <nav class="bg-white shadow-sm border-b border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <router-link to="/" class="text-xl font-bold text-brand-primary">
                        Recetas
                    </router-link>
                    <div class="hidden sm:ml-10 sm:flex sm:space-x-4">
                        <router-link
                            v-for="item in navigation"
                            :key="item.name"
                            :to="item.href"
                            class="inline-flex items-center px-3 py-2 rounded-md text-sm font-medium transition-colors"
                            :class="isActive(item.href)
                                ? 'bg-brand-accent/30 text-brand-primary'
                                : 'text-slate-700 hover:text-brand-primary hover:bg-slate-100'"
                        >
                            {{ item.icon }}
                            <span class="ml-2">{{ item.name }}</span>
                        </router-link>
                    </div>
                </div>
                <div class="flex items-center">
                    <span class="hidden sm:block text-sm text-slate-600 mr-4">
                        {{ auth.user?.first_name }} {{ auth.user?.last_name }}
                    </span>
                    <button
                        @click="handleLogout"
                        class="px-3 py-2 rounded-md text-sm font-medium text-slate-700 hover:text-brand-primary hover:bg-slate-100 transition-colors"
                    >
                        Logout
                    </button>
                    <button
                        @click="mobileMenuOpen = !mobileMenuOpen"
                        class="sm:hidden ml-2 p-2 rounded-md text-slate-700 hover:text-brand-primary hover:bg-slate-100"
                    >
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
        <div v-show="mobileMenuOpen" class="sm:hidden border-t border-slate-200">
            <div class="px-2 pt-2 pb-3 space-y-1">
                <router-link
                    v-for="item in navigation"
                    :key="item.name"
                    :to="item.href"
                    @click="mobileMenuOpen = false"
                    class="block px-3 py-2 rounded-md text-base font-medium"
                    :class="isActive(item.href)
                        ? 'bg-brand-accent/30 text-brand-primary'
                        : 'text-slate-700 hover:text-brand-primary hover:bg-slate-100'"
                >
                    {{ item.icon }} {{ item.name }}
                </router-link>
            </div>
        </div>
    </nav>
</template>
