<script setup lang="ts">
import type { Specialty } from '../../types'
import { ref, onMounted } from 'vue'
import { listSpecialties, deleteSpecialty as deleteSpecialtyRequest } from '../../repositories/specialty'

const specialties = ref<Specialty[]>([])
const loading = ref(true)

async function fetchSpecialties() {
    try {
        const { data } = await listSpecialties()
        specialties.value = data.data
    } finally {
        loading.value = false
    }
}

async function deleteSpecialty(id: number) {
    if (!confirm('Are you sure?')) return
    await deleteSpecialtyRequest(id)
    specialties.value = specialties.value.filter((s) => s.id !== id)
}

onMounted(fetchSpecialties)
</script>

<template>
    <div>
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold text-brand-primary">Specialties</h1>
            <router-link :to="{ name: 'specialties.create' }" class="px-4 py-2 bg-brand-primary hover:bg-slate-800 text-white font-medium rounded-md transition-colors">
                Create Specialty
            </router-link>
        </div>
        <div v-if="loading" class="text-center py-12 ">Loading...</div>
        <div v-else-if="specialties.length === 0" class="text-center py-12 ">No specialties found.</div>
        <div v-else class="overflow-x-auto">
            <table class="w-full bg-white border border-slate-200 rounded-xl overflow-hidden shadow-sm">
                <thead>
                    <tr class="border-b border-gray-200 text-left">
                        <th class="px-4 py-3 text-sm font-medium  ">Name</th>
                        <th class="px-4 py-3 text-sm font-medium  ">Identification</th>
                        <th class="px-4 py-3 text-sm font-medium  ">University</th>
                        <th class="px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="specialty in specialties" :key="specialty.id" class="border-b border-gray-100 last:border-0">
                        <td class="px-4 py-3 text-gray-900 ">{{ specialty.name }}</td>
                        <td class="px-4 py-3 text-gray-600 ">{{ specialty.identification }}</td>
                        <td class="px-4 py-3 text-right">
                            <router-link :to="{ name: 'specialties.edit', params: { id: specialty.id } }" class="text-sm text-brand-primary hover:underline mr-3">Edit</router-link>
                            <button @click="deleteSpecialty(specialty.id ?? 0)" class="text-sm text-red-600  hover:underline">Delete</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>
