<script setup>
import { ref, onMounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import api from '../../api/axios'

const router = useRouter()
const route = useRoute()
const isEdit = !!route.params.id

const form = ref({
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
    patient_id: '',
    room_id: '',
    specialty_id: '',
    medicaments: [],
})

const patients = ref([])
const rooms = ref([])
const specialties = ref([])
const medicaments = ref([])
const loading = ref(false)
const error = ref('')

onMounted(async () => {
    const [patRes, roomRes, specRes, medRes] = await Promise.all([
        api.get('/patients'),
        api.get('/rooms'),
        api.get('/specialties'),
        api.get('/medicaments'),
    ])
    patients.value = patRes.data.data.data
    rooms.value = roomRes.data.data.data
    specialties.value = specRes.data.data.data
    medicaments.value = medRes.data.data.data

    if (route.query.patient_id) {
        form.value.patient_id = route.query.patient_id
    }

    if (isEdit) {
        const { data } = await api.get(`/prescriptions/${route.params.id}`)
        form.value = {
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
            medicaments: data.data.medicaments?.map((m) => ({
                medicament_id: m.id || m.medicament_id,
                dosage: m.pivot?.dosage ?? '',
                frequency: m.pivot?.frequency ?? '',
                duration: m.pivot?.duration ?? '',
            })) || [],
        }
    }
})

function addMedicament() {
    form.value.medicaments.push({ medicament_id: '', dosage: '', frequency: '', duration: '' })
}

function removeMedicament(index) {
    form.value.medicaments.splice(index, 1)
}

async function handleSubmit() {
    loading.value = true
    error.value = ''
    try {
        const payload = {
            ...form.value,
            medicament_data: form.value.medicaments.map((m) => ({
                id: m.medicament_id,
                dosage: m.dosage,
                frequency: m.frequency,
                duration: m.duration,
            })),
        }
        delete payload.medicaments
        if (isEdit) {
            await api.put(`/prescriptions/${route.params.id}`, payload)
        } else {
            await api.post('/prescriptions', payload)
        }
        router.push({ name: 'prescriptions.index' })
    } catch (err) {
        error.value = err.response?.data?.message || 'Failed to save prescription'
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
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Medicaments</h2>
                    <button type="button" @click="addMedicament" class="text-sm px-3 py-1 bg-indigo-50 dark:bg-indigo-900/50 text-indigo-700 dark:text-indigo-300 rounded-md hover:bg-indigo-100 dark:hover:bg-indigo-900">
                        + Add medicament
                    </button>
                </div>
                <div v-for="(med, i) in form.medicaments" :key="i" class="flex items-end gap-3 border-b border-gray-100 dark:border-gray-800 pb-4 last:border-0">
                    <div class="flex-1">
                        <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Medicament</label>
                        <select v-model="med.medicament_id" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-md bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100">
                            <option value="">Select</option>
                            <option v-for="m in medicaments" :key="m.id" :value="m.id">{{ m.name }}</option>
                        </select>
                    </div>
                    <div class="w-24">
                        <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Dosage</label>
                        <input v-model="med.dosage" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-md bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100" />
                    </div>
                    <div class="w-28">
                        <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Frequency</label>
                        <input v-model="med.frequency" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-md bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100" />
                    </div>
                    <div class="w-24">
                        <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Duration</label>
                        <input v-model="med.duration" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-md bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100" />
                    </div>
                    <button type="button" @click="removeMedicament(i)" class="p-2 text-red-500 hover:text-red-700 dark:hover:text-red-400">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
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
