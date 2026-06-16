<script setup lang="ts">
import { onMounted } from 'vue'
import { useSpecialtiesStore } from '../../stores/specialties'

const { items, loading, fetchSpecialties, removeSpecialty } = useSpecialtiesStore()

async function deleteSpecialty(id: number) {
    if (!confirm('Are you sure?')) return
    await removeSpecialty(id)
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
        <div v-else-if="items.length === 0" class="text-center py-12 ">No specialties found.</div>
        <div v-else class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            <div v-for="specialty in items" :key="specialty.id" class="bg-white border border-slate-200 rounded-xl p-5 shadow-sm">
                <h3 class="font-semibold text-brand-primary text-lg">{{ specialty.name }}</h3>
                <p v-if="specialty.identification" class="text-sm   mt-1">{{ specialty.identification }}</p>
                <div class="flex gap-2 mt-4">
                    <router-link :to="{ name: 'specialties.edit', params: { id: specialty.id } }" class="text-sm text-brand-primary hover:underline">Edit</router-link>
                    <button @click="deleteSpecialty(specialty.id ?? 0)" class="text-sm text-red-600  hover:underline">Delete</button>
                </div>
            </div>
        </div>
    </div>
</template>
