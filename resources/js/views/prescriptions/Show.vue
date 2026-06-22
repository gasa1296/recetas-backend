<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import type { Prescription } from '../../types'
import { usePrescriptionsStore } from '../../stores/prescriptions'

const { loading, loadPrescription, activePrescription } = usePrescriptionsStore()
const router = useRouter()
const route = useRoute()
const prescription = ref<Prescription | null>(null)
const error = ref('')

async function handleActivePrescription() {
    if (!prescription.value?.id) return
    if (!confirm('Are you sure?')) return
    error.value = ''
    try {
        if (!prescription.value?.id) return
        await activePrescription(prescription.value?.id)
        router.push({ name: 'prescriptions.show', params: { id: prescription.value?.id } })
    } catch (err) {
        error.value = (err as any).response?.data?.message || 'Failed to activate prescription'
    }
}
onMounted(async () => {
    try {
        prescription.value = await loadPrescription(Number(route.params.id))
    } catch (err) {
        error.value = (err as any).response?.data?.message || 'Failed to load prescription details'
    }
})
</script>

<template>
    <div class="max-w-5xl mx-auto px-4 py-6 text-slate-900">
        <header class="mb-8 flex flex-col sm:flex-row sm:items-center justify-between gap-4 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <div>
                <p class="text-xs uppercase tracking-[0.24em] text-brand-secondary">Prescription Information</p>
                <h1 class="mt-2 text-3xl font-semibold text-brand-primary">Prescription Details</h1>
            </div>
            <div>
                <router-link :to="{ name: 'prescriptions.index' }"
                    class="px-5 py-2.5 rounded-xl bg-slate-100 text-slate-700 font-semibold hover:bg-slate-200 transition">
                    Back to List
                </router-link>
            </div>
        </header>

        <div v-if="loading" class="text-center py-12">Loading...</div>
        <div v-else-if="error" class="rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-700">{{ error }}</div>
        <div v-else-if="prescription" class="space-y-6">
            <!-- Patient & Clinical Context -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Patient Card -->
                <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                    <h2 class="mb-4 flex items-center gap-3 text-lg font-semibold text-brand-primary">
                        <span class="h-6 w-1 rounded-full bg-brand-primary"></span>
                        Patient Info
                    </h2>
                    <div class="space-y-3">
                        <div>
                            <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Full Name</span>
                            <span class="text-base text-slate-900 font-medium">{{ prescription.patient?.first_name }} {{ prescription.patient?.last_name }}</span>
                        </div>
                        <div>
                            <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Identification</span>
                            <span class="text-base text-slate-950 font-mono">{{ prescription.patient?.identification }}</span>
                        </div>
                        <div v-if="prescription.patient?.email">
                            <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Email</span>
                            <span class="text-base text-slate-900">{{ prescription.patient?.email }}</span>
                        </div>
                        <div v-if="prescription.patient?.gender">
                            <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Gender</span>
                            <span class="text-base text-slate-900">{{ prescription.patient?.gender === 'M' ? 'Male' : prescription.patient?.gender === 'F' ? 'Female' : 'Other' }}</span>
                        </div>
                        <div v-if="prescription.patient?.birth_date">
                            <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Birth Date</span>
                            <span class="text-base text-slate-900">{{ prescription.patient?.birth_date }}</span>
                        </div>
                        <div v-if="prescription.patient?.phone && prescription.patient.phone.length > 0">
                            <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Phone Numbers</span>
                            <div class="space-y-1">
                                <span v-for="(phone, index) in prescription.patient.phone" :key="index" class="block text-base text-slate-900">{{ phone }}</span>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Details Card -->
                <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm flex flex-col justify-between">
                    <div>
                        <h2 class="mb-4 flex items-center gap-3 text-lg font-semibold text-brand-primary">
                            <span class="h-6 w-1 rounded-full bg-brand-primary"></span>
                            Prescription details
                        </h2>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Status</span>
                                <span class="inline-flex items-center px-2.5 py-0.5 mt-1 rounded-full text-xs font-medium" :class="prescription.status === 1 ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'">
                                    {{ prescription.pretty_status || 'Draft' }}
                                </span>
                            </div>
                            <div v-if="prescription.room">
                                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Room</span>
                                <span class="text-base text-slate-900">{{ prescription.room.name }}</span>
                            </div>
                        </div>
                    </div>
                </section>
            </div>

            <!-- Vital Signs -->
            <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <h2 class="mb-4 flex items-center gap-3 text-lg font-semibold text-brand-primary">
                    <span class="h-6 w-1 rounded-full bg-brand-primary"></span>
                    Vital Signs
                </h2>
                <div class="grid grid-cols-2 sm:grid-cols-6 gap-4">
                    <div class="bg-slate-50 p-3 rounded-xl border border-slate-100">
                        <span class="text-xs font-semibold text-slate-500 block">Temp</span>
                        <span class="text-lg font-semibold text-slate-900">{{ prescription.temp ? prescription.temp + ' °C' : '-' }}</span>
                    </div>
                    <div class="bg-slate-50 p-3 rounded-xl border border-slate-100">
                        <span class="text-xs font-semibold text-slate-500 block">Weight</span>
                        <span class="text-lg font-semibold text-slate-900">{{ prescription.weight ? prescription.weight + ' kg' : '-' }}</span>
                    </div>
                    <div class="bg-slate-50 p-3 rounded-xl border border-slate-100">
                        <span class="text-xs font-semibold text-slate-500 block">Height</span>
                        <span class="text-lg font-semibold text-slate-900">{{ prescription.height ? prescription.height + ' cm' : '-' }}</span>
                    </div>
                    <div class="bg-slate-50 p-3 rounded-xl border border-slate-100">
                        <span class="text-xs font-semibold text-slate-500 block">Blood Pressure</span>
                        <span class="text-lg font-semibold text-slate-900">{{ prescription.pressure ? prescription.pressure : '-' }}</span>
                    </div>
                    <div class="bg-slate-50 p-3 rounded-xl border border-slate-100">
                        <span class="text-xs font-semibold text-slate-500 block">Oxygen Sat.</span>
                        <span class="text-lg font-semibold text-slate-900">{{ prescription.saturation ? prescription.saturation + ' %' : '-' }}</span>
                    </div>
                    <div class="bg-slate-50 p-3 rounded-xl border border-slate-100">
                        <span class="text-xs font-semibold text-slate-500 block">Pulse (PPM)</span>
                        <span class="text-lg font-semibold text-slate-900">{{ prescription.ppm ? prescription.ppm : '-' }}</span>
                    </div>
                </div>
            </section>

            <!-- Diagnosis & Treatment -->
            <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm space-y-4">
                <h2 class="flex items-center gap-3 text-lg font-semibold text-brand-primary">
                    <span class="h-6 w-1 rounded-full bg-brand-primary"></span>
                    Diagnosis & Treatment
                </h2>
                <div class="space-y-3">
                    <div v-if="prescription.allergy">
                        <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Allergies</span>
                        <p class="text-base text-red-600 font-medium">{{ prescription.allergy }}</p>
                    </div>
                    <div>
                        <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Diagnostic</span>
                        <p class="text-base text-slate-900 whitespace-pre-wrap">{{ prescription.diagnostic || '-' }}</p>
                    </div>
                    <div v-if="prescription.diet">
                        <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Diet</span>
                        <p class="text-base text-slate-900 whitespace-pre-wrap">{{ prescription.diet }}</p>
                    </div>
                </div>
            </section>

            <!-- Medication Orders -->
            <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <h2 class="mb-4 flex items-center gap-3 text-lg font-semibold text-brand-primary">
                    <span class="h-6 w-1 rounded-full bg-brand-primary"></span>
                    Medication Orders
                </h2>
                <div v-if="prescription.medicaments && prescription.medicaments.length > 0" class="divide-y divide-slate-100">
                    <div v-for="med in prescription.medicaments" :key="med.id" class="py-4 first:pt-0 last:pb-0 flex flex-col sm:flex-row justify-between gap-4">
                        <div>
                            <h3 class="text-lg font-bold text-slate-900">{{ med.active_ingredient }}</h3>
                            <p class="text-sm text-slate-500">{{ med.type }} - {{ med.group }}</p>
                        </div>
                        <div class="flex flex-wrap gap-4 text-sm">
                            <div class="min-w-25">
                                <span class="text-xs font-semibold text-slate-400 block uppercase">Dosage</span>
                                <span class="text-slate-800 font-medium">{{ med.dosage }}</span>
                            </div>
                            <div class="min-w-25">
                                <span class="text-xs font-semibold text-slate-400 block uppercase">Frequency</span>
                                <span class="text-slate-800 font-medium">{{ med.frequency }}</span>
                            </div>
                            <div class="min-w-25">
                                <span class="text-xs font-semibold text-slate-400 block uppercase">Duration</span>
                                <span class="text-slate-800 font-medium">{{ med.duration }}</span>
                            </div>
                            <div class="min-w-25" v-if="med.medicament_quantity">
                                <span class="text-xs font-semibold text-slate-400 block uppercase">Quantity</span>
                                <span class="text-slate-800 font-medium">{{ med.medicament_quantity }}</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div v-else class="text-slate-400">No medications ordered.</div>
            </section>

            <!-- Additional Notes -->
            <section v-if="prescription.comments" class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <h2 class="mb-4 flex items-center gap-3 text-lg font-semibold text-brand-primary">
                    <span class="h-6 w-1 rounded-full bg-brand-primary"></span>
                    Additional Notes
                </h2>
                <p class="text-base text-slate-900 whitespace-pre-wrap">{{ prescription.comments }}</p>
            </section>
        </div>
    </div>
</template>
