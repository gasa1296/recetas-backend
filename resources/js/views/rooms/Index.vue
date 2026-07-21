<script setup lang="ts">
import { onMounted } from 'vue'
import { useRoomsStore } from '../../stores/rooms'

const { loading, getRooms, fetchRooms, removeRoom } = useRoomsStore()
const items = getRooms()
async function deleteRoom(id: number) {
    if (!confirm('¿Estás seguro?')) return
    await removeRoom(id)
}

onMounted(fetchRooms)
</script>

<template>
    <div>
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold text-brand-primary">Consultorios</h1>
            <router-link :to="{ name: 'rooms.create' }" class="px-4 py-2 bg-brand-primary hover:bg-slate-800 text-white font-medium rounded-md transition-colors">
                Crear consultorio
            </router-link>
        </div>
        <div v-if="loading" class="text-center py-12 ">Cargando...</div>
        <div v-else-if="items.length === 0" class="text-center py-12 ">No se encontraron consultorios.</div>
        <div v-else class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            <div v-for="room in items" :key="room.id" class="bg-white border border-slate-200 rounded-xl p-5 shadow-sm">
                <h3 class="font-semibold text-brand-primary text-lg">{{ room.name }}</h3>
                <p class="text-sm text-gray-500">{{ room.identification }}</p>
                <p v-if="room.zip" class="text-sm   mt-1">{{ room.zip }}</p>
                <p v-if="room.address" class="text-sm   mt-1">{{ room.address }}</p>
                <p v-if="room.phone" class="text-sm">{{ room.phone.join(', ') }}</p>
                <div class="flex gap-2 mt-4">
                    <router-link :to="{ name: 'rooms.edit', params: { id: room.id } }" class="text-sm text-brand-primary hover:underline">Editar</router-link>
                    <button @click="deleteRoom(room.id ?? 0)" class="text-sm text-red-600  hover:underline">Eliminar</button>
                </div>
            </div>
        </div>
    </div>
</template>
