<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useAuthStore } from '../stores/auth'

const auth = useAuthStore()
import type { Profile } from '../types'

const form = ref<Profile>({
    first_name: '',
    last_name: '',
    identification: '',
    phone: [],
    email: '',
})
const loading = ref(false)
const success = ref('')
const error = ref('')

onMounted(async () => {
    if (auth.user) {
        form.value = { ...auth.user }
    }
})

function addPhone() {
    form.value.phone?.push('')
}

function removePhone(index: number) {
    const phone = form.value.phone
    if (Array.isArray(phone)) {
        phone.splice(index, 1)
    } else if (typeof phone === 'string') {
        form.value.phone = []
    }
}

async function handleUpdate() {
    loading.value = true
    success.value = ''
    error.value = ''
    try {
        await auth.updateProfile(form.value)
        success.value = 'Profile updated successfully'
    } catch (err) {
        error.value = (err as any).response?.data?.message || 'Failed to update profile'
    } finally {
        loading.value = false
    }
}
</script>

<template>
    <div>
        <h1 class="text-2xl font-bold text-brand-primary mb-6">Profile</h1>
        <form @submit.prevent="handleUpdate" class="max-w-2xl bg-white border border-slate-200 rounded-2xl p-6 space-y-4 shadow-sm">
            <div v-if="success" class="p-3 rounded-md bg-green-50 text-green-700 text-sm">
                {{ success }}
            </div>
            <div v-if="error" class="p-3 rounded-md bg-red-50  text-red-700  text-sm">
                {{ error }}
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">First Name</label>
                    <input v-model="form.first_name" required class="w-full px-3 py-2 border border-gray-300  rounded-md bg-white  text-gray-900 " />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Last Name</label>
                    <input v-model="form.last_name" required class="w-full px-3 py-2 border border-gray-300  rounded-md bg-white  text-gray-900 " />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Identification</label>
                    <input v-model="form.identification" class="w-full px-3 py-2 border border-gray-300  rounded-md bg-white  text-gray-900 " />
                </div>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <input v-model="form.email" type="email" required class="w-full px-3 py-2 border border-gray-300  rounded-md bg-white  text-gray-900 " />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                    <div class="space-y-2">
                        <div v-for="(p, i) in form.phone" :key="i" class="flex gap-2">
                            <input v-model="form.phone[i]" class="flex-1 px-3 py-2 border border-gray-300  rounded-md bg-white  text-gray-900 " />
                            <button type="button" @click="removePhone(i)" class="px-2 text-red-500 hover:text-red-700">✕</button>
                        </div>
                        <button type="button" @click="addPhone" class="text-sm text-indigo-600 hover:underline">+ Add phone</button>
                    </div>
                </div>
            </div>
            <div class="pt-4">
                <button type="submit" :disabled="loading" class="px-4 py-2 bg-brand-primary hover:bg-slate-800 text-white font-medium rounded-md transition-colors disabled:opacity-50">
                    {{ loading ? 'Saving...' : 'Save changes' }}
                </button>
            </div>
        </form>
    </div>
</template>
