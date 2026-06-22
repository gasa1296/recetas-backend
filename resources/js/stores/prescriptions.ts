import { defineStore } from 'pinia'
import { ref } from 'vue'
import { getPrescription, createPrescription, updatePrescription, listPrescriptions, deletePrescription, finishPrescription } from '../repositories/prescription'
import { Prescription } from '../types/index'

export const usePrescriptionsStore = defineStore('prescriptions', () => {
  const items = ref<Prescription[]>([])
  const loading = ref(false)

  async function fetchPrescriptions() {
    loading.value = true
    try {
      const { data } = await listPrescriptions()
      items.value = data.data
    } finally {
      loading.value = false
    }
  }
  function getPrescriptions() {
    return items
  }

  async function loadPrescription(id: number) {
    let item = items.value.find((p) => p.id === id)
    if (!item) {
      loading.value = true
      try {
        const { data } = await getPrescription(id)
        item = data.data
      } finally {
        loading.value = false
      }
    }
    return item
  }

  async function savePrescription(
    id: number | undefined,
    payload: Prescription,
  ): Promise<Prescription> {
    let item: any = {}
    loading.value = true
    try {
      if (id) {
        const { data } = await updatePrescription(id, payload)
        item = data.data
      } else {
        const { data } = await createPrescription(payload)
        item = data.data
      }
    } finally {
      loading.value = false
    }
    return item
  }

  async function removePrescription(id: number) {
    loading.value = true
    try {
      await deletePrescription(id)
      items.value = items.value.filter((prescription) => prescription.id !== id)
    } finally {
      loading.value = false
    }
  }

  async function activePrescription(id: number) {
    loading.value = true
    try {
      await finishPrescription(id)
    } finally {
      loading.value = false
    }
  }

  return { loading, getPrescriptions, fetchPrescriptions, loadPrescription, savePrescription, removePrescription, activePrescription }
})
