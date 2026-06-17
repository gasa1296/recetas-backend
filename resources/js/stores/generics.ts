import { defineStore } from 'pinia'
import { ref } from 'vue'
import { listGenders, listPrescriptionStatuses } from '../repositories/general'

export const useGenericStore = defineStore('generics', () => {
  const genders = ref<Object>()
  const prescriptionStatuses = ref<Object>()
  const loading = ref(false)

  async function fetchGenders() {
    loading.value = true
    try {
      const { data } = await listGenders()
      genders.value = data.data
    } finally {
      loading.value = false
    }
  }
  async function fetchPrescriptionStatuses() {
    loading.value = true
    try {
      const { data } = await listPrescriptionStatuses()
      prescriptionStatuses.value = data.data
    } finally {
      loading.value = false
    }
  }

  return { genders, prescriptionStatuses, loading, fetchGenders, fetchPrescriptionStatuses }
})
