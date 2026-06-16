<script setup lang="ts">
import type { Prescription } from '../../types'
import { ref, onMounted } from 'vue'
import { listPrescriptions, deletePrescription as deletePrescriptionRequest, finishPrescription as finishPrescriptionRequest } from '../../repositories/prescription'

const prescriptions = ref<Prescription[]>([])
const loading = ref(true)

async function fetchPrescriptions() {
    try {
        const { data } = await listPrescriptions()
        prescriptions.value = data.data
    } finally {
        loading.value = false
    }
}

async function deletePrescription(id: number) {
    if (!confirm('Are you sure?')) return
    await deletePrescriptionRequest(id)
    prescriptions.value = prescriptions.value.filter((p) => p.id !== id)
}

async function finishPrescription(id: number) {
    if (!confirm('Finish this prescription?')) return
    await finishPrescriptionRequest(id)
    fetchPrescriptions()
}

onMounted(fetchPrescriptions)
</script>

<template>
    <div>
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Prescriptions</h1>
            <router-link :to="{ name: 'prescriptions.create' }" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-md transition-colors">
                Create Prescription
            </router-link>
        </div>
        <div v-if="loading" class="text-center py-12 text-gray-500">Loading...</div>
        <div v-else-if="prescriptions.length === 0" class="text-center py-12 text-gray-500">No prescriptions found.</div>
        <div v-else class="overflow-x-auto">
            <table class="w-full bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-lg">
                <thead>
                    <tr class="border-b border-gray-200 dark:border-gray-800 text-left">
                        <th class="px-4 py-3 text-sm font-medium text-gray-500 dark:text-gray-400">Patient</th>
                        <th class="px-4 py-3 text-sm font-medium text-gray-500 dark:text-gray-400">Room</th>
                        <th class="px-4 py-3 text-sm font-medium text-gray-500 dark:text-gray-400">Specialty</th>
                        <th class="px-4 py-3 text-sm font-medium text-gray-500 dark:text-gray-400">Status</th>
                        <th class="px-4 py-3 text-sm font-medium text-gray-500 dark:text-gray-400">Diagnostic</th>
                        <th class="px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="p in prescriptions" :key="p.id" class="border-b border-gray-100 dark:border-gray-800 last:border-0">
                        <td class="px-4 py-3 text-gray-900 dark:text-gray-100">{{ p.patient?.first_name }} {{ p.patient?.last_name1 }}</td>
                        <td class="px-4 py-3 text-gray-600 dark:text-gray-400">{{ p.room?.name }}</td>
                        <td class="px-4 py-3 text-gray-600 dark:text-gray-400">{{ p.specialty?.name }}</td>
                        <td class="px-4 py-3">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium" :class="p.status === '1' ? 'bg-green-100 text-green-800 dark:bg-green-900/50 dark:text-green-300' : 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/50 dark:text-yellow-300'">
                                {{ p.pretty_status || p.status }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-gray-600 dark:text-gray-400 max-w-xs truncate">{{ p.diagnostic }}</td>
                        <td class="px-4 py-3 text-right whitespace-nowrap">
                            <router-link v-if="p.status === '0'" :to="{ name: 'prescriptions.edit', params: { id: p.id } }" class="text-sm text-indigo-600 dark:text-indigo-400 hover:underline mr-3">Edit</router-link>
                            <button v-if="p.status !== '1'" @click="finishPrescription(p.id)" class="text-sm text-green-600 dark:text-green-400 hover:underline mr-3">Finish</button>
                            <button @click="deletePrescription(p.id)" class="text-sm text-red-600 dark:text-red-400 hover:underline">Delete</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>
