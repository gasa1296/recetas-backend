<script setup>
import { ref, onMounted } from 'vue'
import api from '../../api/axios'

const specialties = ref([])
const loading = ref(true)

async function fetchSpecialties() {
    try {
        const { data } = await api.get('/specialties')
        specialties.value = data.data.data
    } finally {
        loading.value = false
    }
}

async function deleteSpecialty(id) {
    if (!confirm('Are you sure?')) return
    await api.delete(`/specialties/${id}`)
    specialties.value = specialties.value.filter((s) => s.id !== id)
}

onMounted(fetchSpecialties)
</script>

<template>
    <div>
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Specialties</h1>
            <router-link :to="{ name: 'specialties.create' }" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-md transition-colors">
                Create Specialty
            </router-link>
        </div>
        <div v-if="loading" class="text-center py-12 text-gray-500">Loading...</div>
        <div v-else-if="specialties.length === 0" class="text-center py-12 text-gray-500">No specialties found.</div>
        <div v-else class="overflow-x-auto">
            <table class="w-full bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-lg">
                <thead>
                    <tr class="border-b border-gray-200 dark:border-gray-800 text-left">
                        <th class="px-4 py-3 text-sm font-medium text-gray-500 dark:text-gray-400">Name</th>
                        <th class="px-4 py-3 text-sm font-medium text-gray-500 dark:text-gray-400">Identification</th>
                        <th class="px-4 py-3 text-sm font-medium text-gray-500 dark:text-gray-400">University</th>
                        <th class="px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="specialty in specialties" :key="specialty.id" class="border-b border-gray-100 dark:border-gray-800 last:border-0">
                        <td class="px-4 py-3 text-gray-900 dark:text-gray-100">{{ specialty.name }}</td>
                        <td class="px-4 py-3 text-gray-600 dark:text-gray-400">{{ specialty.identification }}</td>
                        <td class="px-4 py-3 text-gray-600 dark:text-gray-400">{{ specialty.university }}</td>
                        <td class="px-4 py-3 text-right">
                            <router-link :to="{ name: 'specialties.edit', params: { id: specialty.id } }" class="text-sm text-indigo-600 dark:text-indigo-400 hover:underline mr-3">Edit</router-link>
                            <button @click="deleteSpecialty(specialty.id)" class="text-sm text-red-600 dark:text-red-400 hover:underline">Delete</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>
