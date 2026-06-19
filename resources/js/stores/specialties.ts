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

  function getSpecialties() {
    return items
  }

  async function loadSpecialty(id: number) {
    let item = items.value.find((s) => s.id === id)
    if (!item) {
      loading.value = true
      try {
        const { data } = await getSpecialty(id)
        item = data.data
      } finally {
        loading.value = false
      }
    }
    return item
  }

  async function saveSpecialty(
    id: number | undefined,
    payload: Specialty,
  ) {
    loading.value = true
    try {
      if (id) {
        await updateSpecialty(id, payload)
      } else {
        await createSpecialty(payload)
      }
    } finally {
      loading.value = false
    }
  }

  async function removeSpecialty(id: number) {
    loading.value = true
    try {
      await deleteSpecialty(id)
      items.value = items.value.filter((specialty) => specialty.id !== id)
    } finally {
      loading.value = false
    }
  }

  return { getSpecialties, loading, fetchSpecialties, loadSpecialty, saveSpecialty, removeSpecialty }
})
