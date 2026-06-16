<script setup>
import { ref, onMounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import api from '../../api/axios'

const router = useRouter()
const route = useRoute()
const isEdit = !!route.params.id

const form = ref({
    name: '',
    identification: '',
    university: '',
})
const universities = ref([])
const loading = ref(false)
const error = ref('')

onMounted(async () => {
    try {
        const { data } = await api.get('/universities')
        universities.value = data.data
    } catch {}
    if (isEdit) {
        const { data } = await api.get(`/specialties/${route.params.id}`)
        form.value = { ...data.data }
    }
})

async function handleSubmit() {
    loading.value = true
    error.value = ''
    try {
        if (isEdit) {
            await api.put(`/specialties/${route.params.id}`, form.value)
        } else {
            await api.post('/specialties', form.value)
        }
        router.push({ name: 'specialties.index' })
    } catch (err) {
        error.value = err.response?.data?.message || 'Failed to save specialty'
    } finally {
        loading.value = false
    }
}
</script>

<template>
    <div>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-6">{{ isEdit ? 'Edit Specialty' : 'Create Specialty' }}</h1>
        <form @submit.prevent="handleSubmit" class="max-w-2xl bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-lg p-6 space-y-4">
            <div v-if="error" class="p-3 rounded-md bg-red-50 dark:bg-red-900/30 text-red-700 dark:text-red-400 text-sm">{{ error }}</div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Name *</label>
                <input v-model="form.name" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-md bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100" />
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Identification *</label>
                <input v-model="form.identification" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-md bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100" />
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">University</label>
                <select v-model="form.university" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-md bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100">
                    <option value="">Select university</option>
                    <option v-for="u in universities" :key="u.alpha_two_code" :value="u.name">{{ u.name }}</option>
                </select>
            </div>
            <div class="pt-4 flex gap-3">
                <button type="submit" :disabled="loading" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-md transition-colors disabled:opacity-50">
                    {{ loading ? 'Saving...' : 'Save' }}
                </button>
                <router-link :to="{ name: 'specialties.index' }" class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-md hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors">
                    Cancel
                </router-link>
            </div>
        </form>
    </div>
</template>
