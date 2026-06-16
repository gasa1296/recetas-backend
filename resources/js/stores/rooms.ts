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
    return getRoom(id)
  }

  async function saveRoom(
    id: number | undefined,
    payload: Room,
  ) {
    if (id) {
      return updateRoom(id, payload)
    }

    return createRoom(payload)
  }

  async function removeRoom(id: number) {
    await deleteRoom(id)
    items.value = items.value.filter((room) => room.id !== id)
  }

  return { items, loading, fetchRooms, loadRoom, saveRoom, removeRoom }
})
