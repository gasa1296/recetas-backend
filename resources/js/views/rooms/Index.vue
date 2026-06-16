<script setup lang="ts">
import type { Room } from '../../types'
import { ref, onMounted } from 'vue'
import { listRooms, deleteRoom as deleteRoomRequest } from '../../repositories/rooms'

const rooms = ref<Room[]>([])
const loading = ref(true)

async function fetchRooms() {
    try {
        const { data } = await listRooms()
        rooms.value = data.data
    } finally {
        loading.value = false
    }
}

async function deleteRoom(id: number) {
    if (!confirm('Are you sure?')) return
    await deleteRoomRequest(id)
    rooms.value = rooms.value.filter((r) => r.id !== id)
}

onMounted(fetchRooms)
</script>

<template>
    <div>
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold text-brand-primary">Rooms</h1>
            <router-link :to="{ name: 'rooms.create' }" class="px-4 py-2 bg-brand-primary hover:bg-slate-800 text-white font-medium rounded-md transition-colors">
                Create Room
            </router-link>
        </div>
        <div v-if="loading" class="text-center py-12 ">Loading...</div>
        <div v-else-if="rooms.length === 0" class="text-center py-12 ">No rooms found.</div>
        <div v-else class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            <div v-for="room in rooms" :key="room.id" class="bg-white border border-slate-200 rounded-xl p-5 shadow-sm">
                <h3 class="font-semibold text-brand-primary text-lg">{{ room.name }}</h3>
                <p v-if="room.address" class="text-sm   mt-1">{{ room.address }}</p>
                <p v-if="room.phone" class="text-sm">{{ Array.isArray(room.phone) ? room.phone.join(', ') : room.phone }}</p>
                <div class="flex gap-2 mt-4">
                    <router-link :to="{ name: 'rooms.edit', params: { id: room.id } }" class="text-sm text-brand-primary hover:underline">Edit</router-link>
                    <button @click="deleteRoom(room.id ?? 0)" class="text-sm text-red-600  hover:underline">Delete</button>
                </div>
            </div>
        </div>
    </div>
</template>
