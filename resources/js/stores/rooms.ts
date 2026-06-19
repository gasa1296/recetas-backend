import { defineStore } from 'pinia'
import { ref } from 'vue'
import {
  createRoom,
  deleteRoom,
  getRoom,
  listRooms,
  updateRoom,
} from '../repositories/rooms'
import type { Room } from '../types'

export const useRoomsStore = defineStore('rooms', () => {
  const items = ref<Room[]>([])
  const loading = ref(false)

  async function fetchRooms() {
    loading.value = true
    try {
      const { data } = await listRooms()
      items.value = data.data
    } finally {
      loading.value = false
    }
  }

  async function loadRoom(id: number) {
    let item = items.value.find((p) => p.id === id)
    if (!item) {
      loading.value = true
      try {
        const { data } = await getRoom(id)
        item = data.data
      } finally {
        loading.value = false
      }
    }
    return item
  }

  async function saveRoom(
    id: number | undefined,
    payload: Room,
  ) {
    loading.value = true
    try {
      if (id) {
        await updateRoom(id, payload)
      } else {
        await createRoom(payload)
      }
    } finally {
      loading.value = false
    }
  }

  async function removeRoom(id: number) {
    loading.value = true
    try {
      await deleteRoom(id)
      items.value = items.value.filter((room) => room.id !== id)
    } finally {
      loading.value = false
    }
  }

  return { items, loading, fetchRooms, loadRoom, saveRoom, removeRoom }
})
