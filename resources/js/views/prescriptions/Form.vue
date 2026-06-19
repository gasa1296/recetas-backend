<script setup lang="ts">
import type { Medicament, Patient, Room, Specialty, Prescription } from '../../types'
import { ref, onMounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { listPatients, createPatient, updatePatient } from '../../repositories/patient'
import { listRooms } from '../../repositories/rooms'
import { listMedicaments } from '../../repositories/medicaments'
import { getPrescription, createPrescription, updatePrescription } from '../../repositories/prescription'
import { listGenders } from '../../repositories/general'
import { usePrescriptionsStore } from '../../stores/prescriptions'

const router = useRouter()
const route = useRoute()
const { loadPrescription, savePrescription, activePrescription } = usePrescriptionsStore()
const isEdit = !!route.params.id

const form = ref<Prescription>({
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
    medicaments: [],
})

const patientForm = ref<Patient>({
    first_name: '',
    last_name: '',
    identification: '',
    email: '',
    phone: [],
    gender: '',
    birth_date: null,
})

const rooms = ref<Room[]>([])
const specialties = ref<Specialty[]>([])
const genders = ref<any>({})
const loading = ref(false)
const error = ref('')
const medicamentSearch = ref('')
const showPatientDropdown = ref(false)
const showMedicamentDropdown = ref(false)
const patientResults = ref<Patient[]>([])
const medicamentResults = ref<Medicament[]>([])

async function onPatientInput() {
    if (patientForm.value.id) {
        patientForm.value = {
            identification: patientForm.value.identification,
            first_name: '',
            last_name: '',
            email: '',
            phone: [],
            gender: '',
            birth_date: null,
        }
        form.value.patient_id = undefined
    }

    if (!patientForm.value.identification) {
        patientResults.value = []
        return
    }
    const { data } = await listPatients({ search: patientForm.value.identification })
    patientResults.value = data.data
}

async function onMedicamentInput() {
    if (!medicamentSearch.value) {
        medicamentResults.value = []
        return
    }
    const { data } = await listMedicaments({ search: medicamentSearch.value })
    medicamentResults.value = data.data
}

function selectPatient(patient: Patient) {
    patientForm.value = {
        phone: patient.phone ? [...patient.phone] : [],
        ...patient
    }
    form.value.patient_id = patient.id
    showPatientDropdown.value = false
}

function selectSearchMedicament(med: Medicament) {
    form.value.medicaments?.push({ id: med.id, active_ingredient: med.active_ingredient, type: med.type, group: med.group, dosage: '', frequency: '', duration: '' })
    medicamentSearch.value = ''
    showMedicamentDropdown.value = false
}

function addPatientPhone() {
    if (!patientForm.value.phone) {
        patientForm.value.phone = []
    }
    patientForm.value.phone.push('')
}

function removePatientPhone(index: number) {
    patientForm.value.phone?.splice(index, 1)
}

onMounted(async () => {
    const [specRes, genderRes] = await Promise.all([
        listRooms(),
        listGenders(),
    ])
    specialties.value = specRes.data.data
    genders.value = genderRes.data.data

    if (isEdit) {
        const data = await loadPrescription(Number(route.params.id))
        form.value = { ...data }
        if (data.patient) {
            patientForm.value = {
                phone: data.patient.phone ? [...data.patient.phone] : [],
                ...data.patient
            }
        }
    }
})

function removeMedicament(index: number) {
    form.value.medicaments?.splice(index, 1)
}

async function handleSubmit() {
    loading.value = true
    error.value = ''
    try {
        if (!form.value.patient_id) {
            const { data } = await createPatient(patientForm.value)
            form.value.patient_id = data.data.id
        } else if (patientForm.value.id) {
            await updatePatient(patientForm.value.id, patientForm.value)
        }
        await savePrescription(Number(route.params.id), form.value)

        router.push({ name: 'prescriptions.index' })
    } catch (err) {
        error.value = (err as any).response?.data?.message || 'Failed to save prescription'
    } finally {
        loading.value = false
    }
}

async function handleActivePrescription() {
    if (!route.params.id) return
    loading.value = true
    error.value = ''
    try {
        const id = Number(route.params.id)
        await activePrescription(id)
        router.push({ name: 'prescriptions.index' })
    } catch (err) {
        error.value = (err as any).response?.data?.message || 'Failed to activate prescription'
    } finally {
        loading.value = false
    }
}
</script>

<template>
    <div class="max-w-5xl mx-auto px-4 py-6 text-slate-900">
        <header class="mb-8 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <p class="text-xs uppercase tracking-[0.24em] text-brand-secondary">Clinical interface</p>
            <h1 class="mt-2 text-3xl font-semibold text-brand-primary">{{ isEdit ? 'Edit Prescription' : 'Create Prescription' }}</h1>
            <p class="mt-2 text-sm text-slate-600">Clear, high-contrast sections for medical staff and interoperable data mapping.</p>
        </header>

        <form @submit.prevent="handleSubmit" class="space-y-6">
            <div v-if="error" class="rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-700">{{ error }}
            </div>

            <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm space-y-4">
                <h2 class="mb-4 flex items-center gap-3 text-lg font-semibold text-brand-primary">
                    <span class="h-6 w-1 rounded-full bg-brand-primary"></span>
                    Patient
                </h2>
                <div class="relative">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Identification (Search/New) *</label>
                    <input v-model="patientForm.identification" @input="onPatientInput" @focus="showPatientDropdown = true"
                        @blur="showPatientDropdown = false" placeholder="Type identification to search or create..."
                        class="w-full px-3 py-2 border border-gray-300 rounded-md bg-white text-gray-900" required />
                    <div v-if="showPatientDropdown && patientForm.identification && patientResults.length > 0"
                        class="absolute z-10 mt-1 w-full bg-white border border-gray-200 rounded-md shadow-lg max-h-60 overflow-y-auto">
                        <div v-for="p in patientResults" :key="p.id" @mousedown="selectPatient(p)"
                            class="px-5 py-4 cursor-pointer hover:bg-indigo-50 text-gray-900">
                            <strong>{{ p.first_name }} {{ p.last_name }}</strong>: {{ p.identification }}
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">First Name *</label>
                        <input v-model="patientForm.first_name" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md bg-white text-gray-900" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Last Name *</label>
                        <input v-model="patientForm.last_name" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md bg-white text-gray-900" />
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                        <input v-model="patientForm.email" type="email"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md bg-white text-gray-900" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Gender *</label>
                        <select v-model="patientForm.gender" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md bg-white text-gray-900">
                            <option value="">Select gender</option>
                            <option v-for="(name, code) in genders" :key="code" :value="code">{{ name }}</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Birth Date</label>
                        <input v-model="patientForm.birth_date" type="date"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md bg-white text-gray-900" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Phones</label>
                        <div class="space-y-2">
                            <div v-for="(phoneVal, idx) in patientForm.phone" :key="idx" class="flex gap-2">
                                <input v-if=(patientForm.phone) v-model="patientForm.phone[idx]"
                                    class="flex-1 px-3 py-2 border border-gray-300 rounded-md bg-white text-gray-900" placeholder="Phone number" />
                                <button type="button" @click="removePatientPhone(idx)" class="px-2 text-red-500 hover:text-red-700">✕</button>
                            </div>
                            <button type="button" @click="addPatientPhone" class="text-sm text-brand-primary hover:underline">+ Add phone</button>
                        </div>
                    </div>
                </div>
            </section>

            <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <h2 class="mb-4 flex items-center gap-3 text-lg font-semibold text-brand-primary">
                    <span class="h-6 w-1 rounded-full bg-brand-primary"></span>
                    Clinical context
                </h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Room</label>
                        <select v-model="form.room_id"
                            class="w-full px-3 py-2 border border-gray-300  rounded-md bg-white  text-gray-900 ">
                            <option value="">Select room</option>
                            <option v-for="r in rooms" :key="r.id" :value="r.id">{{ r.name }}</option>
                        </select>
                    </div>
                </div>
            </section>

            <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <h2 class="mb-4 flex items-center gap-3 text-lg font-semibold text-brand-primary">
                    <span class="h-6 w-1 rounded-full bg-brand-primary"></span>
                    Vital signs
                </h2>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Temp</label>
                        <input v-model="form.temp" type="number" step="0.1"
                            class="w-full px-3 py-2 border border-gray-300  rounded-md bg-white  text-gray-900 " />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Weight</label>
                        <input v-model="form.weight" type="number" step="0.1"
                            class="w-full px-3 py-2 border border-gray-300  rounded-md bg-white  text-gray-900 " />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Height</label>
                        <input v-model="form.height" type="number" step="0.1"
                            class="w-full px-3 py-2 border border-gray-300  rounded-md bg-white  text-gray-900 " />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Pressure</label>
                        <input v-model="form.pressure"
                            class="w-full px-3 py-2 border border-gray-300  rounded-md bg-white  text-gray-900 " />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Saturation</label>
                        <input v-model="form.saturation" type="number" step="0.1"
                            class="w-full px-3 py-2 border border-gray-300  rounded-md bg-white  text-gray-900 " />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">PPM</label>
                        <input v-model="form.ppm" type="number"
                            class="w-full px-3 py-2 border border-gray-300  rounded-md bg-white  text-gray-900 " />
                    </div>
                </div>
            </section>

            <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <h2 class="mb-4 flex items-center gap-3 text-lg font-semibold text-brand-primary">
                    <span class="h-6 w-1 rounded-full bg-brand-primary"></span>
                    Diagnosis & treatment
                </h2>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Allergy</label>
                    <input v-model="form.allergy"
                        class="w-full px-3 py-2 border border-gray-300  rounded-md bg-white  text-gray-900 " />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Diagnostic *</label>
                    <textarea v-model="form.diagnostic" required rows="2"
                        class="w-full px-3 py-2 border border-gray-300  rounded-md bg-white  text-gray-900 "></textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Diet</label>
                    <textarea v-model="form.diet" rows="2"
                        class="w-full px-3 py-2 border border-gray-300  rounded-md bg-white  text-gray-900 "></textarea>
                </div>
            </section>

            <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <h2 class="mb-4 flex items-center gap-3 text-lg font-semibold text-brand-primary">
                    <span class="h-6 w-1 rounded-full bg-brand-primary"></span>
                    Medication orders
                </h2>
                <div class="relative">
                    <input v-model="medicamentSearch" @input="onMedicamentInput" @focus="showMedicamentDropdown = true"
                        @blur="showMedicamentDropdown = false" placeholder="Search medicament to add..."
                        class="w-full px-3 py-2 border border-gray-300  rounded-md bg-white  text-gray-900 " />
                    <div v-if="showMedicamentDropdown && medicamentSearch"
                        class="absolute z-10 mt-1 w-full bg-white  border border-gray-200  rounded-md shadow-lg max-h-60 overflow-y-auto">
                        <div v-for="m in medicamentResults" :key="m.id" @mousedown="selectSearchMedicament(m)"
                            class="px-3 py-2 cursor-pointer hover:bg-indigo-50 text-gray-900 ">
                            {{ m.active_ingredient }}
                        </div>
                        <div v-if="medicamentResults.length === 0" class="px-3 py-2 text-gray-400">No medicaments found
                        </div>
                    </div>
                </div>
                <div v-for="(med, i) in form.medicaments" :key="i"
                    class="flex items-end gap-3 border-b border-gray-100 pb-4 last:border-0">
                    <div class="flex-1">
                        <p class="block text-lg font-bold   mb-1">{{ med.active_ingredient }}</p>
                    </div>
                    <div class="w-24">
                        <label class="block text-xs font-medium   mb-1">Quantity</label>
                        <input v-model="med.medicament_quantity" type="number"
                            class="w-full px-3 py-2 border border-gray-300  rounded-md bg-white  text-gray-900 " />
                    </div>
                    <div class="w-24">
                        <label class="block text-xs font-medium   mb-1">Dosage</label>
                        <input v-model="med.dosage"
                            class="w-full px-3 py-2 border border-gray-300  rounded-md bg-white  text-gray-900 " />
                    </div>
                    <div class="w-28">
                        <label class="block text-xs font-medium   mb-1">Frequency</label>
                        <input v-model="med.frequency"
                            class="w-full px-3 py-2 border border-gray-300  rounded-md bg-white  text-gray-900 " />
                    </div>
                    <div class="w-24">
                        <label class="block text-xs font-medium   mb-1">Duration</label>
                        <input v-model="med.duration"
                            class="w-full px-3 py-2 border border-gray-300  rounded-md bg-white  text-gray-900 " />
                    </div>
                    <button type="button" @click="removeMedicament(i)" class="p-2 text-red-500 hover:text-red-700">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </section>

            <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <h2 class="mb-4 flex items-center gap-3 text-lg font-semibold text-brand-primary">
                    <span class="h-6 w-1 rounded-full bg-brand-primary"></span>
                    Additional notes
                </h2>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Comments</label>
                    <textarea v-model="form.comments" rows="2"
                        class="w-full px-3 py-2 border border-gray-300  rounded-md bg-white  text-gray-900 "></textarea>
                </div>
            </section>

            <div class="flex gap-3 border-t border-slate-200 pt-4">
                <button type="submit" :disabled="loading"
                    class="px-5 py-3 rounded-xl bg-brand-primary text-white font-semibold shadow-sm hover:bg-slate-800 transition disabled:opacity-50">
                    {{ loading ? 'Saving...' : 'Save' }}
                </button>
                <button v-if="isEdit" type="button" :disabled="loading" @click="handleActivePrescription"
                    class="px-5 py-3 rounded-xl bg-green-600 text-white font-semibold shadow-sm hover:bg-green-700 transition disabled:opacity-50">
                    {{ loading ? 'Activating...' : 'Activate' }}
                </button>
                <router-link :to="{ name: 'prescriptions.index' }"
                    class="px-5 py-3 rounded-xl bg-slate-100 text-slate-700 font-semibold hover:bg-slate-200 transition">
                    Cancel
                </router-link>
            </div>
        </form>
    </div>
</template>
