import { defineStore } from 'pinia'
import { ref } from 'vue'
import { listPatients, getPatient, createPatient, updatePatient, deletePatient } from '../repositories/patient'
import type { Patient } from '../types/index'

export const usePatientsStore = defineStore('patients', () => {
  const items = ref<Patient[]>([])
  const loading = ref(false)

  async function fetchPatients() {
    loading.value = true
    try {
      const { data } = await listPatients()
      items.value = data.data
    } finally {
      loading.value = false
    }
  }
  function getPatients() {
    return items.value
  }

  async function loadPatient(id: number) {
    let item = items.value.find((p) => p.id === id)
    if (!item) {
      loading.value = true
      try {
        const { data } = await getPatient(id)
        item = data.data
      } finally {
        loading.value = false
      }
    }
    return item
  }

  async function savePatient(
    id: number | undefined,
    payload: Patient,
  ) {
    loading.value = true
    try {
      if (id) {
        await updatePatient(id, payload)
      } else {
        await createPatient(payload)
      }
    } finally {
      loading.value = false
    }
  }

  async function removePatient(id: number) {
    loading.value = true
    try {
      await deletePatient(id)
      items.value = items.value.filter((p) => p.id !== id)
    } finally {
      loading.value = false
    }
  }

  return { getPatients, loading, fetchPatients, loadPatient, savePatient, removePatient }
})