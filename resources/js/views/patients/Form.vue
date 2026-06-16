<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { getPatient, updatePatient, createPatient } from '../../repositories/patient'
import { listGenders } from '../../repositories/general'

const router = useRouter()
const route = useRoute()
const isEdit = !!route.params.id

import type { Gender, Patient } from '../../types'

const genders = ref<Gender[]>([])
const form = ref<Patient>({
    id: 0,
    first_name: '',
    last_name1: '',
    last_name2: '',
    email: '',
    phone: [],
    gender: '',
    birth_date: null,
})
const loading = ref(false)
const error = ref('')

onMounted(async () => {
    try {
        const { data } = await listGenders()
        genders.value = data.data
    } catch {}

    if (isEdit) {
        const id = parseInt(route.params.id as string)
        const { data } = await getPatient(id)
        const patient = { ...data.data }
        patient.phone = Array.isArray(patient.phone) ? patient.phone : (patient.phone ? [patient.phone] : [])
        form.value = patient
    }
})

function addPhone() {
    form.value.phone.push('')
}

function removePhone(index: number) {
    const phone = form.value.phone
    if (Array.isArray(phone)) {
        phone.splice(index, 1)
    } else if (typeof phone === 'string') {
        form.value.phone = []
    }
}

async function handleSubmit() {
    loading.value = true
    error.value = ''
    try {
        if (isEdit) {
            const id = parseInt(route.params.id as string)
            await updatePatient(id, form.value)
        } else {
            await createPatient(form.value)
        }
        router.push({ name: 'patients.index' })
    } catch (err) {
        error.value = (err as any).response?.data?.message || 'Failed to save patient'
    } finally {
        loading.value = false
    }
}
</script>

<template>
    <div>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-6">{{ isEdit ? 'Edit Patient' : 'Create Patient' }}</h1>
        <form @submit.prevent="handleSubmit" class="max-w-2xl bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-lg p-6 space-y-4">
            <div v-if="error" class="p-3 rounded-md bg-red-50 dark:bg-red-900/30 text-red-700 dark:text-red-400 text-sm">{{ error }}</div>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">First Name *</label>
                    <input v-model="form.first_name" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-md bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Last Name 1 *</label>
                    <input v-model="form.last_name1" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-md bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Last Name 2</label>
                    <input v-model="form.last_name2" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-md bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100" />
                </div>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Email *</label>
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
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Gender</label>
                    <select v-model="form.gender" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-md bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100">
                        <option value="">Select</option>
                        <option v-for="gender in genders" :key="gender" :value="gender">{{ gender }}</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Birth Date</label>
                    <input v-model="form.birth_date" type="date" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-md bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100" />
                </div>
            </div>
            <div class="pt-4 flex gap-3">
                <button type="submit" :disabled="loading" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-md transition-colors disabled:opacity-50">
                    {{ loading ? 'Saving...' : 'Save' }}
                </button>
                <router-link :to="{ name: 'patients.index' }" class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-md hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors">
                    Cancel
                </router-link>
            </div>
        </form>
    </div>
</template>
