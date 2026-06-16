<script setup>
import { ref, onMounted } from 'vue'
import { useAuthStore } from '../stores/auth'

const auth = useAuthStore()
const form = ref({
    first_name: '',
    last_name1: '',
    last_name2: '',
    phone: [],
    gender: '',
    email: '',
})
const loading = ref(false)
const success = ref('')
const error = ref('')

onMounted(() => {
    if (auth.user) {
        const user = { ...auth.user }
        user.phone = Array.isArray(user.phone) ? user.phone : (user.phone ? [user.phone] : [])
        form.value = user
    }
})

function addPhone() {
    form.value.phone.push('')
}

function removePhone(index) {
    form.value.phone.splice(index, 1)
}

async function handleUpdate() {
    loading.value = true
    success.value = ''
    error.value = ''
    try {
        await auth.updateProfile(form.value)
        success.value = 'Profile updated successfully'
    } catch (err) {
        error.value = err.response?.data?.message || 'Failed to update profile'
    } finally {
        loading.value = false
    }
}
</script>

<template>
    <div>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-6">Profile</h1>
        <form @submit.prevent="handleUpdate" class="max-w-2xl bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-lg p-6 space-y-4">
            <div v-if="success" class="p-3 rounded-md bg-green-50 dark:bg-green-900/30 text-green-700 dark:text-green-400 text-sm">
                {{ success }}
            </div>
            <div v-if="error" class="p-3 rounded-md bg-red-50 dark:bg-red-900/30 text-red-700 dark:text-red-400 text-sm">
                {{ error }}
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">First Name</label>
                    <input v-model="form.first_name" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-md bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Last Name 1</label>
                    <input v-model="form.last_name1" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-md bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Last Name 2</label>
                    <input v-model="form.last_name2" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-md bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100" />
                </div>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Email</label>
                    <input v-model="form.email" type="email" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-md bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Phone</label>
                    <div class="space-y-2">
                        <div v-for="(p, i) in form.phone" :key="i" class="flex gap-2">
                            <input v-model="form.phone[i]" class="flex-1 px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-md bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100" />
                            <button type="button" @click="removePhone(i)" class="px-2 text-red-500 hover:text-red-700">✕</button>
                        </div>
                        <button type="button" @click="addPhone" class="text-sm text-indigo-600 dark:text-indigo-400 hover:underline">+ Add phone</button>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Gender</label>
                    <select v-model="form.gender" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-md bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100">
                        <option value="">Select</option>
                        <option value="male">Male</option>
                        <option value="female">Female</option>
                    </select>
                </div>
            </div>
            <div class="pt-4">
                <button type="submit" :disabled="loading" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-md transition-colors disabled:opacity-50">
                    {{ loading ? 'Saving...' : 'Save changes' }}
                </button>
            </div>
        </form>
    </div>
</template>
