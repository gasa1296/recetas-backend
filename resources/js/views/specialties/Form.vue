<script setup lang="ts">
import type { Specialty } from '../../types'
import { ref, onMounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { useSpecialtiesStore } from '../../stores/specialties'

const { loading, loadSpecialty, saveSpecialty  } = useSpecialtiesStore()

const router = useRouter()
const route = useRoute()
const isEdit = !!route.params.id

const form = ref<Specialty>({
    name: '',
    identification: '',
})
const error = ref('')

onMounted(async () => {
    if (isEdit) {
        const id = parseInt(route.params.id as string)
        const { data } = await loadSpecialty(id)
        form.value = { ...data.data }
    }
})

async function handleSubmit() {
    error.value = ''
    const id = route.params.id ? parseInt(route.params.id as string) : undefined; 
    
    try {
        await saveSpecialty(id, form.value)
        router.push({ name: 'specialties.index' })
    } catch (err) {
        error.value = (err as any).response?.data?.message || 'Failed to save specialty'
    }
}
</script>

<template>
    <div>
        <h1 class="text-2xl font-bold text-brand-primary mb-6">{{ isEdit ? 'Edit Specialty' : 'Create Specialty' }}</h1>
        <form @submit.prevent="handleSubmit" class="max-w-2xl bg-white border border-slate-200 rounded-2xl p-6 space-y-4 shadow-sm">
            <div v-if="error" class="p-3 rounded-md bg-red-50  text-red-700  text-sm">{{ error }}</div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Name *</label>
                <input v-model="form.name" required class="w-full px-3 py-2 border border-gray-300  rounded-md bg-white  text-gray-900 " />
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Identification *</label>
                <input v-model="form.identification" required class="w-full px-3 py-2 border border-gray-300  rounded-md bg-white  text-gray-900 " />
            </div>
            <div class="pt-4 flex gap-3">
                <button type="submit" :disabled="loading" class="px-4 py-2 bg-brand-primary hover:bg-slate-800 text-white font-medium rounded-md transition-colors disabled:opacity-50">
                    {{ loading ? 'Saving...' : 'Save' }}
                </button>
                <router-link :to="{ name: 'specialties.index' }" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 transition-colors">
                    Cancel
                </router-link>
            </div>
        </form>
    </div>
</template>
