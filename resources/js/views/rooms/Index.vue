<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import api from '../../api/axios'

const router = useRouter()
const rooms = ref([])
const loading = ref(true)

async function fetchRooms() {
    try {
        const { data } = await api.get('/rooms')
        rooms.value = data.data
        console.log(rooms.value)
    } finally {
        loading.value = false
    }
}

async function deleteRoom(id) {
    if (!confirm('Are you sure?')) return
    await api.delete(`/rooms/${id}`)
    rooms.value = rooms.value.filter((r) => r.id !== id)
}

onMounted(fetchRooms)
</script>

<template>
    <div>
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Rooms</h1>
            <router-link :to="{ name: 'rooms.create' }" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-md transition-colors">
                Create Room
            </router-link>
        </div>
        <div v-if="loading" class="text-center py-12 text-gray-500">Loading...</div>
        <div v-else-if="rooms.length === 0" class="text-center py-12 text-gray-500">No rooms found.</div>
        <div v-else class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            <div v-for="room in rooms" :key="room.id" class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-lg p-5">
                <h3 class="font-semibold text-gray-900 dark:text-gray-100 text-lg">{{ room.name }}</h3>
                <p v-if="room.address" class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ room.address }}</p>
                <p v-if="room.phone?.length" class="text-sm text-gray-500 dark:text-gray-400">{{ room.phone.join(', ') }}</p>
                <div class="flex gap-2 mt-4">
                    <router-link :to="{ name: 'rooms.edit', params: { id: room.id } }" class="text-sm text-indigo-600 dark:text-indigo-400 hover:underline">Edit</router-link>
                    <button @click="deleteRoom(room.id)" class="text-sm text-red-600 dark:text-red-400 hover:underline">Delete</button>
                </div>
            </div>
        </div>
    </div>
</template>
