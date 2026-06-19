<script setup lang="ts">
import { useRouter } from 'vue-router'
import { useAuthStore } from '../stores/auth'

const router = useRouter()
const auth = useAuthStore()

const cards = [
    { name: 'Profile', description: 'Manage your profile information', href: '/profile', icon: '👤', color: 'bg-blue-500' },
    { name: 'Rooms', description: 'Manage consultation rooms', href: '/rooms', icon: '🏥', color: 'bg-green-500' },
    { name: 'Specialties', description: 'Manage your specialties', href: '/specialties', icon: '🔬', color: 'bg-purple-500' },
    { name: 'Prescriptions', description: 'Create and manage prescriptions', href: '/prescriptions', icon: '📋', color: 'bg-rose-500' },
]
</script>

<template>
    <div class="max-w-6xl">
        <h1 class="text-2xl font-bold text-brand-primary mb-2">
            Welcome, {{ auth.user?.first_name }} {{ auth.user?.last_name }}
        </h1>
        <p class="text-slate-600 mb-8">Select a module to get started</p>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            <div
                v-for="card in cards"
                :key="card.name"
                @click="router.push(card.href)"
                class="bg-white border border-slate-200 rounded-xl p-6 hover:shadow-md hover:border-brand-accent transition-shadow cursor-pointer"
            >
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-lg flex items-center justify-center text-2xl" :class="card.color">
                        {{ card.icon }}
                    </div>
                    <div>
                        <h3 class="font-semibold text-brand-primary">{{ card.name }}</h3>
                        <p class="text-sm text-slate-600">{{ card.description }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
