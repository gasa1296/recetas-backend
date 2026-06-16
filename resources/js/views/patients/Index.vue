<script setup lang="ts">
import type { Patient } from '../../types'
import { ref, onMounted } from 'vue'
import { listPatients, deletePatient as deletePatientRequest } from '../../repositories/patient'

const patients = ref<Patient[]>([])
const loading = ref(true)

async function fetchPatients() {
    try {
        const { data } = await listPatients()
        patients.value = data.data
    } finally {
        loading.value = false
    }
}

async function deletePatient(id: number | string) {
    if (!confirm('Are you sure?')) return
    await deletePatientRequest(id)
    patients.value = patients.value.filter((p) => p.id !== id)
}

onMounted(fetchPatients)
</script>

<template>
    <div>
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Patients</h1>
            <router-link :to="{ name: 'patients.create' }" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-md transition-colors">
                Create Patient
            </router-link>
        </div>
        <div v-if="loading" class="text-center py-12 text-gray-500">Loading...</div>
        <div v-else-if="patients.length === 0" class="text-center py-12 text-gray-500">No patients found.</div>
        <div v-else class="overflow-x-auto">
            <table class="w-full bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-lg">
                <thead>
                    <tr class="border-b border-gray-200 dark:border-gray-800 text-left">
                        <th class="px-4 py-3 text-sm font-medium text-gray-500 dark:text-gray-400">Name</th>
                        <th class="px-4 py-3 text-sm font-medium text-gray-500 dark:text-gray-400">Email</th>
                        <th class="px-4 py-3 text-sm font-medium text-gray-500 dark:text-gray-400">Phone</th>
                        <th class="px-4 py-3 text-sm font-medium text-gray-500 dark:text-gray-400">Gender</th>
                        <th class="px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="patient in patients" :key="patient.id" class="border-b border-gray-100 dark:border-gray-800 last:border-0">
                        <td class="px-4 py-3 text-gray-900 dark:text-gray-100">{{ patient.first_name }} {{ patient.last_name1 }} {{ patient.last_name2 }}</td>
                        <td class="px-4 py-3 text-gray-600 dark:text-gray-400">{{ patient.email }}</td>
                        <td class="px-4 py-3 text-gray-600 dark:text-gray-400">{{ Array.isArray(patient.phone) ? patient.phone.join(', ') : patient.phone }}</td>
                        <td class="px-4 py-3 text-gray-600 dark:text-gray-400">{{ patient.gender }}</td>
                        <td class="px-4 py-3 text-right whitespace-nowrap">
                            <router-link :to="{ name: 'patients.edit', params: { id: patient.id } }" class="text-sm text-indigo-600 dark:text-indigo-400 hover:underline mr-3">Edit</router-link>
                            <router-link :to="{ name: 'prescriptions.create', query: { patient_id: patient.id } }" class="text-sm text-green-600 dark:text-green-400 hover:underline mr-3">Prescription</router-link>
                            <button @click="deletePatient(patient.id)" class="text-sm text-red-600 dark:text-red-400 hover:underline">Delete</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>
