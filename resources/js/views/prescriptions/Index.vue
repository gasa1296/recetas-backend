<script setup lang="ts">
import type { Prescription } from '../../types'
import { ref, onMounted } from 'vue'
import { usePrescriptionsStore } from '../../stores/prescriptions'

const { items, removePrescription, fetchPrescriptions } = usePrescriptionsStore()
const prescriptions = ref<Prescription[]>([])
const loading = ref(true)

async function deletePrescription(id: number) {
    if (!confirm('Are you sure?')) return
    await removePrescription(id)
    prescriptions.value = prescriptions.value.filter((p) => p.id !== id)
}
onMounted(fetchPrescriptions)
</script>

<template>
    <div>
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold text-brand-primary">Prescriptions</h1>
            <router-link :to="{ name: 'prescriptions.create' }" class="px-4 py-2 bg-brand-primary hover:bg-slate-800 text-white font-medium rounded-md transition-colors">
                Create Prescription
            </router-link>
        </div>
        <div v-if="loading" class="text-center py-12 ">Loading...</div>
        <div v-else-if="prescriptions.length === 0" class="text-center py-12 ">No prescriptions found.</div>
        <div v-else class="overflow-x-auto">
            <table class="w-full bg-white border border-slate-200 rounded-xl overflow-hidden shadow-sm">
                <thead>
                    <tr class="border-b border-gray-200 text-left">
                        <th class="px-4 py-3 text-sm font-medium  ">Patient</th>
                        <th class="px-4 py-3 text-sm font-medium  ">Room</th>
                        <th class="px-4 py-3 text-sm font-medium  ">Status</th>
                        <th class="px-4 py-3 text-sm font-medium  ">Diagnostic</th>
                        <th class="px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="p in items" :key="p.id" class="border-b border-gray-100 last:border-0">
                        <td class="px-4 py-3 text-gray-900 ">{{ p.patient?.first_name }} {{ p.patient?.last_name }}</td>
                        <td class="px-4 py-3 text-gray-600 ">{{ p.room?.name }}</td>
                        <td class="px-4 py-3">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium" :class="p.status === 1 ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'">
                                {{ p.pretty_status || p.status }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-gray-600  max-w-xs truncate">{{ p.diagnostic }}</td>
                        <td class="px-4 py-3 text-right whitespace-nowrap">
                            <router-link v-if="p.status === 0" :to="{ name: 'prescriptions.edit', params: { id: p.id } }" class="text-sm text-brand-primary hover:underline mr-3">Edit</router-link>
                            <button @click="deletePrescription(p.id ?? 0)" class="text-sm text-red-600  hover:underline">Delete</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>
