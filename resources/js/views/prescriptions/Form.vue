<script setup lang="ts">
import type { Medicament, Patient, PrescriptionPayload, Room, Specialty } from '../../types'
import { ref, onMounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { listPatients } from '../../repositories/patient'
import { listRooms } from '../../repositories/rooms'
import { listSpecialties } from '../../repositories/specialty'
import { listMedicaments } from '../../repositories/medicaments'
import { getPrescription, createPrescription, updatePrescription } from '../../repositories/prescription'

const router = useRouter()
const route = useRoute()
const isEdit = !!route.params.id

const form = ref<PrescriptionPayload>({
    patient_id: '',
    room_id: '',
    specialty_id: '',
    temp: '',
    weight: '',
    height: '',
    pressure: '',
    saturation: '',
    ppm: '',
    allergy: '',
    diagnostic: '',
    diet: '',
    comments: '',
    medicament_data: [],
})

const patients = ref<Patient[]>([])
const rooms = ref<Room[]>([])
const specialties = ref<Specialty[]>([])
const medicaments = ref<Medicament[]>([])
const loading = ref(false)
const error = ref('')

onMounted(async () => {
    const [patRes, roomRes, specRes, medRes] = await Promise.all([
        listPatients(),
        listRooms(),
        listSpecialties(),
        listMedicaments(),
    ])
    patients.value = patRes.data.data
    rooms.value = roomRes.data.data
    specialties.value = specRes.data.data
    medicaments.value = medRes.data.data

    if (route.query.patient_id) {
        form.value.patient_id = String(route.query.patient_id)
    }

            if (isEdit) {
                const { data } = await getPrescription(route.params.id as string)
                form.value = {
                    ...data.data,
                    temp: data.data.temp ?? '',
                    weight: data.data.weight ?? '',
                    height: data.data.height ?? '',
                    pressure: data.data.pressure ?? '',
                    saturation: data.data.saturation ?? '',
                    ppm: data.data.ppm ?? '',
                    allergy: data.data.allergy ?? '',
                    diagnostic: data.data.diagnostic ?? '',
                    diet: data.data.diet ?? '',
                    comments: data.data.comments ?? '',
                    patient_id: data.data.patient_id ?? '',
                    room_id: data.data.room_id ?? '',
                    specialty_id: data.data.specialty_id ?? '',
                    medicament_data: (data.data.medicaments || []).map((m) => ({
                        medicament_id: m.id,
                        dosage: m.dosage,
                        frequency: m.frequency,
                        duration: m.duration,
                    })),
                }
            }
})

function addMedicament() {
    form.value.medicament_data.push({ medicament_id: '', dosage: '', frequency: '', duration: '' })
}

function removeMedicament(index: number) {
    form.value.medicament_data.splice(index, 1)
}

async function handleSubmit() {
    loading.value = true
    error.value = ''
            try {
                const payload = {
                    ...form.value,
                    medicament_data: form.value.medicament_data.map((m) => ({
                        medicament_id: m.medicament_id as string | number,
                        dosage: m.dosage,
                        frequency: m.frequency,
                        duration: m.duration,
                    })),
                }
                if (isEdit) {
                    await updatePrescription(route.params.id as string, payload)
                } else {
                    await createPrescription(payload)
                }
                router.push({ name: 'prescriptions.index' })
            } catch (err) {
                error.value = (err as any).response?.data?.message || 'Failed to save prescription'
            } finally {
                loading.value = false
            }
}
</script>

<template>
    <div>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-6">{{ isEdit ? 'Edit Prescription' : 'Create Prescription' }}</h1>
        <form @submit.prevent="handleSubmit" class="max-w-3xl space-y-6">
            <div v-if="error" class="p-3 rounded-md bg-red-50 dark:bg-red-900/30 text-red-700 dark:text-red-400 text-sm">{{ error }}</div>

            <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-lg p-6 space-y-4">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">References</h2>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Patient *</label>
                        <select v-model="form.patient_id" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-md bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100">
                            <option value="">Select patient</option>
                            <option v-for="p in patients" :key="p.id" :value="p.id">{{ p.first_name }} {{ p.last_name1 }}</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Room</label>
                        <select v-model="form.room_id" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-md bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100">
                            <option value="">Select room</option>
                            <option v-for="r in rooms" :key="r.id" :value="r.id">{{ r.name }}</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Specialty</label>
                        <select v-model="form.specialty_id" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-md bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100">
                            <option value="">Select specialty</option>
                            <option v-for="s in specialties" :key="s.id" :value="s.id">{{ s.name }}</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-lg p-6 space-y-4">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Vital Signs</h2>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Temp</label>
                        <input v-model="form.temp" type="number" step="0.1" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-md bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Weight</label>
                        <input v-model="form.weight" type="number" step="0.1" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-md bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Height</label>
                        <input v-model="form.height" type="number" step="0.1" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-md bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Pressure</label>
                        <input v-model="form.pressure" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-md bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Saturation</label>
                        <input v-model="form.saturation" type="number" step="0.1" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-md bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">PPM</label>
                        <input v-model="form.ppm" type="number" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-md bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100" />
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-lg p-6 space-y-4">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Diagnosis & Treatment</h2>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Allergy</label>
                    <input v-model="form.allergy" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-md bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Diagnostic *</label>
                    <textarea v-model="form.diagnostic" required rows="2" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-md bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100"></textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Diet</label>
                    <textarea v-model="form.diet" rows="2" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-md bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100"></textarea>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-lg p-6 space-y-4">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Additional</h2>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Comments</label>
                    <textarea v-model="form.comments" rows="2" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-md bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100"></textarea>
                </div>
            </div>

            <div class="flex gap-3">
                <button type="submit" :disabled="loading" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-md transition-colors disabled:opacity-50">
                    {{ loading ? 'Saving...' : 'Save' }}
                </button>
                <router-link :to="{ name: 'prescriptions.index' }" class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-md hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors">
                    Cancel
                </router-link>
            </div>
        </form>
    </div>
</template>
