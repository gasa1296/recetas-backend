import { defineStore } from 'pinia'
import { ref } from 'vue'
import {
  createSpecialty,
  deleteSpecialty,
  getSpecialty,
  listSpecialties,
  updateSpecialty,
} from '../repositories/specialty'
import type { Specialty } from '../types'

export const useSpecialtiesStore = defineStore('specialties', () => {
  const items = ref<Specialty[]>([])
  const loading = ref(false)

  async function fetchSpecialties() {
    loading.value = true
    try {
      const { data } = await listSpecialties()
      items.value = data.data
    } finally {
      loading.value = false
    }
  }

  async function loadSpecialty(id: number) {
    return getSpecialty(id)
  }

  async function saveSpecialty(
    id: number | undefined,
    payload: Specialty,
  ) {
    if (id) {
      return updateSpecialty(id, payload)
    }

    return createSpecialty(payload)
  }

  async function removeSpecialty(id: number) {
    await deleteSpecialty(id)
    items.value = items.value.filter((specialty) => specialty.id !== id)
  }

  return { items, loading, fetchSpecialties, loadSpecialty, saveSpecialty, removeSpecialty }
})
