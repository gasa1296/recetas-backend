import { defineStore } from 'pinia'
import { ref } from 'vue'
import {
  createSpecialty,
  deleteSpecialty,
  getSpecialty,
  listSpecialties,
  updateSpecialty,
} from '../repositories/specialty'
import type { Specialty, SpecialtyPayload } from '../types'

export const useSpecialtiesStore = defineStore('specialties', () => {
  const items = ref<Specialty[]>([])
  const loading = ref(false)

  async function fetchSpecialties() {
    loading.value = true
    try {
      const { data } = await listSpecialties()
      items.value = data.data.data
    } finally {
      loading.value = false
    }
  }

  async function loadSpecialty(id: string | number) {
    return getSpecialty(id)
  }

  async function saveSpecialty(
    id: string | number | undefined,
    payload: SpecialtyPayload,
  ) {
    if (id) {
      return updateSpecialty(id, payload)
    }

    return createSpecialty(payload)
  }

  async function removeSpecialty(id: string | number) {
    await deleteSpecialty(id)
    items.value = items.value.filter((specialty) => specialty.id !== id)
  }

  return { items, loading, fetchSpecialties, loadSpecialty, saveSpecialty, removeSpecialty }
})
