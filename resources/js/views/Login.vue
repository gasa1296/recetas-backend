<script setup lang="ts">
import type { Credentials } from '../types'
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../stores/auth'

const router = useRouter()
const auth = useAuthStore()
const form = ref<Credentials>({ email: '', password: '' })
const error = ref('')
const loading = ref(false)

async function handleLogin() {
    error.value = ''
    loading.value = true
    try {
        await auth.login(form.value)
        router.push({ name: 'dashboard' })
    } catch (err) {
        const message = (err as any).response?.data?.message || 'Login failed';
        error.value = message;
    } finally {
        loading.value = false
    }
}
</script>

<template>
    <div class="min-h-screen flex items-center justify-center bg-slate-50 px-4">
        <div class="w-full max-w-md">
            <div class="text-center mb-8">
                <h1 class="text-3xl font-bold text-brand-primary">Recetas</h1>
                <p class="mt-2 text-slate-600">Sign in to your account</p>
            </div>
            <form @submit.prevent="handleLogin" class="bg-white shadow-sm rounded-2xl px-8 py-8 border border-slate-200">
                <div v-if="error" class="mb-4 p-3 rounded-md bg-red-50  text-red-700  text-sm">
                    {{ error }}
                </div>
                <div class="mb-4">
                    <label for="email" class="block text-sm font-medium text-slate-700 mb-1">Email</label>
                    <input
                        id="email"
                        v-model="form.email"
                        type="email"
                        required
                        class="w-full px-3 py-2 border border-slate-300 rounded-md shadow-sm focus:ring-2 focus:ring-brand-primary/40 focus:border-brand-primary bg-white text-slate-900"
                        placeholder="doctor@example.com"
                    />
                </div>
                <div class="mb-6">
                    <label for="password" class="block text-sm font-medium text-slate-700 mb-1">Password</label>
                    <input
                        id="password"
                        v-model="form.password"
                        type="password"
                        required
                        class="w-full px-3 py-2 border border-slate-300 rounded-md shadow-sm focus:ring-2 focus:ring-brand-primary/40 focus:border-brand-primary bg-white text-slate-900"
                    />
                </div>
                <button
                    type="submit"
                    :disabled="loading"
                    class="w-full py-2 px-4 bg-brand-primary hover:bg-slate-800 text-white font-medium rounded-md shadow-sm transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                >
                    {{ loading ? 'Signing in...' : 'Sign in' }}
                </button>
            </form>
        </div>
    </div>
</template>
