<script setup lang="ts">
import { ref, onMounted, computed } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import type { Prescription } from '../../types'
import { usePrescriptionsStore } from '../../stores/prescriptions'

const { loading, loadPrescription, activePrescription } = usePrescriptionsStore()
const router = useRouter()
const route = useRoute()
const prescription = ref<Prescription | null>(null)
const error = ref('')
const signature = ref<string | null>(null)
const canvasRef = ref<HTMLCanvasElement | null>(null)
const isDrawing = ref(false)

const isDraft = computed(() => prescription.value?.status === 0)

function initCanvas() {
  const canvas = canvasRef.value
  if (!canvas) return
  const ctx = canvas.getContext('2d')
  if (!ctx) return
  ctx.strokeStyle = '#1e293b'
  ctx.lineWidth = 2
  ctx.lineCap = 'round'
  ctx.lineJoin = 'round'
}

function getPosition(e: MouseEvent | Touch) {
  const canvas = canvasRef.value
  if (!canvas) return { x: 0, y: 0 }
  const rect = canvas.getBoundingClientRect()
  return {
    x: (e.clientX - rect.left) * (canvas.width / rect.width),
    y: (e.clientY - rect.top) * (canvas.height / rect.height),
  }
}

function startDrawing(e: MouseEvent | TouchEvent) {
  isDrawing.value = true
  const pos = getPosition('touches' in e ? e.touches[0] : e)
  const ctx = canvasRef.value?.getContext('2d')
  if (ctx) {
    ctx.beginPath()
    ctx.moveTo(pos.x, pos.y)
  }
}

function draw(e: MouseEvent | TouchEvent) {
  if (!isDrawing.value) return
  const pos = getPosition('touches' in e ? e.touches[0] : e)
  const ctx = canvasRef.value?.getContext('2d')
  if (ctx) {
    ctx.lineTo(pos.x, pos.y)
    ctx.stroke()
  }
}

function stopDrawing() {
  isDrawing.value = false
  const ctx = canvasRef.value?.getContext('2d')
  if (ctx) ctx.closePath()
}

function saveSignature() {
  const canvas = canvasRef.value
  if (!canvas) return
  signature.value = canvas.toDataURL('image/png').split(',')[1]
}

function clearSignature() {
  const canvas = canvasRef.value
  if (!canvas) return
  const ctx = canvas.getContext('2d')
  if (ctx) ctx.clearRect(0, 0, canvas.width, canvas.height)
  signature.value = null
}

async function handleActivePrescription() {
  if (!prescription.value?.id) return
  if (!confirm('Are you sure?')) return
  error.value = ''
  try {
    saveSignature()
    await activePrescription(prescription.value?.id, signature.value ?? undefined)
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
  <div class="max-w-5xl mx-auto px-4 py-6 text-brand-primary-hover">
    <header class="mb-8 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
          <p class="text-xs uppercase tracking-[0.24em] text-brand-secondary">Prescription Information</p>
          <h1 class="mt-2 text-3xl font-semibold text-brand-primary">Prescription Details</h1>
        </div>
        <div class="flex items-center gap-3">
          <span v-if="prescription"
            class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium"
            :class="prescription.status === 1 ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'">
            {{ prescription.pretty_status || 'Draft' }}
          </span>
          <router-link :to="{ name: 'prescriptions.index' }"
            class="px-5 py-2.5 rounded-xl bg-slate-100 text-slate-700 font-semibold hover:bg-slate-200 transition">
            Back to List
          </router-link>
        </div>
      </div>
    </header>

    <div v-if="loading" class="text-center py-12">Loading...</div>
    <div v-else-if="error" class="rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-700">{{ error }}</div>
    <div v-else-if="prescription" class="space-y-2">
      <!-- Patient & Clinical Context -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
        <!-- Patient Card -->
        <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
          <h2 class="mb-4 flex items-center gap-3 text-lg font-semibold text-brand-primary">
            <span class="h-6 w-1 rounded-full bg-brand-primary"></span>
            Patient Info
          </h2>
          <div class="space-y-3">
            <div>
              <span class="text-xs font-semibold text-brand-primary uppercase tracking-wider block">Full Name</span>
              <span class="text-base text-brand-primary-hover font-medium">{{ prescription.patient?.first_name }} {{ prescription.patient?.last_name }}</span>
            </div>
            <div>
              <span class="text-xs font-semibold text-brand-primary uppercase tracking-wider block">Identification</span>
              <span class="text-base text-slate-950 font-mono">{{ prescription.patient?.identification }}</span>
            </div>
            <div v-if="prescription.patient?.email">
              <span class="text-xs font-semibold text-brand-primary uppercase tracking-wider block">Email</span>
              <span class="text-base text-brand-primary-hover">{{ prescription.patient?.email }}</span>
            </div>
            <div v-if="prescription.patient?.gender">
              <span class="text-xs font-semibold text-brand-primary uppercase tracking-wider block">Gender</span>
              <span class="text-base text-brand-primary-hover">{{ prescription.patient?.gender === 'M' ? 'Male' : prescription.patient?.gender === 'F' ? 'Female' : 'Other' }}</span>
            </div>
            <div v-if="prescription.patient?.birth_date">
              <span class="text-xs font-semibold text-brand-primary uppercase tracking-wider block">Birth Date</span>
              <span class="text-base text-brand-primary-hover">{{ prescription.patient?.birth_date }}</span>
            </div>
            <div v-if="prescription.patient?.phone && prescription.patient.phone.length > 0">
              <span class="text-xs font-semibold text-brand-primary uppercase tracking-wider block">Phone Numbers</span>
              <div class="space-y-1">
                <span v-for="(phone, index) in prescription.patient.phone" :key="index" class="block text-base text-brand-primary-hover">{{ phone }}</span>
              </div>
            </div>
          </div>
        </section>

        <!-- Prescriber Card -->
        <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm flex flex-col justify-between">
          <div>
            <h2 class="mb-4 flex items-center gap-3 text-lg font-semibold text-brand-primary">
              <span class="h-6 w-1 rounded-full bg-brand-primary"></span>
              Prescriber Info
            </h2>
            <div class="space-y-3">
              <div>
                <span class="text-xs font-semibold text-brand-primary uppercase tracking-wider block">Doctor</span>
                <span class="text-base text-brand-primary-hover font-medium">{{ prescription.user?.name || (prescription.user?.first_name + ' ' + prescription.user?.last_name) }}</span>
              </div>
              <div v-if="prescription.room">
                <span class="text-xs font-semibold text-brand-primary uppercase tracking-wider block">Room</span>
                <span class="text-base text-brand-primary-hover">{{ prescription.room.name }}</span>
              </div>
              <div v-if="prescription.specialty">
                <span class="text-xs font-semibold text-brand-primary uppercase tracking-wider block">Specialization</span>
                <span class="text-base text-brand-primary-hover">{{ prescription.specialty.name }}</span>
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
            <span class="text-xs font-semibold text-brand-primary block">Temp</span>
            <span class="text-lg font-semibold text-brand-primary-hover">{{ prescription.temp ? prescription.temp + ' °C' : '-' }}</span>
          </div>
          <div class="bg-slate-50 p-3 rounded-xl border border-slate-100">
            <span class="text-xs font-semibold text-brand-primary block">Weight</span>
            <span class="text-lg font-semibold text-brand-primary-hover">{{ prescription.weight ? prescription.weight + ' kg' : '-' }}</span>
          </div>
          <div class="bg-slate-50 p-3 rounded-xl border border-slate-100">
            <span class="text-xs font-semibold text-brand-primary block">Height</span>
            <span class="text-lg font-semibold text-brand-primary-hover">{{ prescription.height ? prescription.height + ' cm' : '-' }}</span>
          </div>
          <div class="bg-slate-50 p-3 rounded-xl border border-slate-100">
            <span class="text-xs font-semibold text-brand-primary block">Blood Pressure</span>
            <span class="text-lg font-semibold text-brand-primary-hover">{{ prescription.pressure ? prescription.pressure : '-' }}</span>
          </div>
          <div class="bg-slate-50 p-3 rounded-xl border border-slate-100">
            <span class="text-xs font-semibold text-brand-primary block">Oxygen Sat.</span>
            <span class="text-lg font-semibold text-brand-primary-hover">{{ prescription.saturation ? prescription.saturation + ' %' : '-' }}</span>
          </div>
          <div class="bg-slate-50 p-3 rounded-xl border border-slate-100">
            <span class="text-xs font-semibold text-brand-primary block">Pulse (PPM)</span>
            <span class="text-lg font-semibold text-brand-primary-hover">{{ prescription.ppm ? prescription.ppm : '-' }}</span>
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
            <span class="text-xs font-semibold text-brand-primary uppercase tracking-wider block">Allergies</span>
            <p class="text-base text-red-600 font-medium">{{ prescription.allergy }}</p>
          </div>
          <div>
            <span class="text-xs font-semibold text-brand-primary uppercase tracking-wider block">Diagnostic</span>
            <p class="text-base text-brand-primary-hover whitespace-pre-wrap">{{ prescription.diagnostic || '-' }}</p>
          </div>
          <div v-if="prescription.diet">
            <span class="text-xs font-semibold text-brand-primary uppercase tracking-wider block">Diet</span>
            <p class="text-base text-brand-primary-hover whitespace-pre-wrap">{{ prescription.diet }}</p>
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
              <h3 class="text-lg font-bold text-brand-primary-hover">{{ med.active_ingredient }}</h3>
              <p class="text-sm text-brand-primary">{{ med.type }} - {{ med.group }}</p>
            </div>
            <div class="flex flex-wrap gap-4 text-sm">
              <div class="min-w-25">
                <span class="text-xs font-semibold text-brand-primary block uppercase">Dosage</span>
                <span class="text-slate-800 font-medium">{{ med.dosage }}</span>
              </div>
              <div class="min-w-25">
                <span class="text-xs font-semibold text-brand-primary block uppercase">Frequency</span>
                <span class="text-slate-800 font-medium">{{ med.frequency }}</span>
              </div>
              <div class="min-w-25">
                <span class="text-xs font-semibold text-brand-primary block uppercase">Duration</span>
                <span class="text-slate-800 font-medium">{{ med.duration }}</span>
              </div>
              <div class="min-w-25" v-if="med.medicament_quantity">
                <span class="text-xs font-semibold text-brand-primary block uppercase">Quantity</span>
                <span class="text-slate-800 font-medium">{{ med.medicament_quantity }}</span>
              </div>
            </div>
          </div>
        </div>
        <div v-else class="text-brand-primary">No medications ordered.</div>
      </section>

      <!-- Additional Notes -->
      <section v-if="prescription.comments" class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        <h2 class="mb-4 flex items-center gap-3 text-lg font-semibold text-brand-primary">
          <span class="h-6 w-1 rounded-full bg-brand-primary"></span>
          Additional Notes
        </h2>
        <p class="text-base text-brand-primary-hover whitespace-pre-wrap">{{ prescription.comments }}</p>
      </section>

      <!-- Signature & Activation -->
      <section v-if="isDraft" class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        <h2 class="mb-4 flex items-center gap-3 text-lg font-semibold text-brand-primary">
          <span class="h-6 w-1 rounded-full bg-brand-primary"></span>
          Digital Signature
        </h2>
        <p class="text-sm text-brand-primary mb-4">Sign below to activate the prescription.</p>
        <div class="flex flex-col items-start gap-4">
          <canvas
            ref="canvasRef"
            width="500"
            height="200"
            class="w-full max-w-lg h-40 border-2 border-dashed border-slate-300 rounded-xl bg-white cursor-crosshair touch-none"
            @mousedown="startDrawing"
            @mousemove="draw"
            @mouseup="stopDrawing"
            @mouseleave="stopDrawing"
            @touchstart.prevent="startDrawing"
            @touchmove.prevent="draw"
            @touchend="stopDrawing"
          ></canvas>
          <div class="flex gap-3">
            <button
              @click="clearSignature"
              class="px-4 py-2 rounded-xl border border-slate-300 text-slate-600 font-semibold hover:bg-slate-100 transition text-sm">
              Clear
            </button>
            <button
              @click="handleActivePrescription"
              class="px-5 py-2 rounded-xl bg-brand-primary text-white font-semibold hover:brightness-110 transition text-sm">
              Activate Prescription
            </button>
          </div>
        </div>
      </section>
    </div>
  </div>
</template>
