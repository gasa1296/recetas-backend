<script setup lang="ts">
import type { Room } from '../../types'
import { ref, onMounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { useRoomsStore } from '../../stores/rooms'

const { loading, loadRoom, saveRoom } = useRoomsStore()

const router = useRouter()
const route = useRoute()
const isEdit = !!route.params.id

const form = ref<Room>({
    name: '',
    zip: '',
    street: '',
    colony: '',
    state: '',
    delegation: '',
    n_exterior: '',
    n_interior: '',
    phone: [],
    fav: false,
    auto_email: false,
    auto_whatsapp: false,
})
const error = ref('')

onMounted(async () => {
    if (isEdit) {
        const id = parseInt(route.params.id as string)
        const { data } = await loadRoom(id)
        form.value = { ...data.data }
    }
})

function addPhone() {
    form.value.phone?.push('')
}

function removePhone(index: number) {
    form.value.phone?.splice(index, 1)
}

async function handleSubmit() {
    error.value = ''
    const id = route.params.id ? parseInt(route.params.id as string) : undefined; 
    
    try {
        await saveRoom(id, form.value)
        router.push({ name: 'rooms.index' })
    } catch (err) {
        error.value =(err as any).response?.data?.message || 'Failed to save room'
    }
}
</script>

<template>
    <div>
        <h1 class="text-2xl font-bold text-brand-primary mb-6">{{ isEdit ? 'Edit Room' : 'Create Room' }}</h1>
        <form @submit.prevent="handleSubmit" class="max-w-2xl bg-white border border-slate-200 rounded-2xl p-6 space-y-4 shadow-sm">
            <div v-if="error" class="p-3 rounded-md bg-red-50  text-red-700  text-sm">{{ error }}</div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Name *</label>
                <input v-model="form.name" required class="w-full px-3 py-2 border border-gray-300  rounded-md bg-white  text-gray-900 " />
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Street</label>
                    <input v-model="form.street" class="w-full px-3 py-2 border border-gray-300  rounded-md bg-white  text-gray-900 " />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Colony</label>
                    <input v-model="form.colony" class="w-full px-3 py-2 border border-gray-300  rounded-md bg-white  text-gray-900 " />
                </div>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">State</label>
                    <input v-model="form.state" class="w-full px-3 py-2 border border-gray-300  rounded-md bg-white  text-gray-900 " />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Delegation</label>
                    <input v-model="form.delegation" class="w-full px-3 py-2 border border-gray-300  rounded-md bg-white  text-gray-900 " />
                </div>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Zip</label>
                    <input v-model="form.zip" class="w-full px-3 py-2 border border-gray-300  rounded-md bg-white  text-gray-900 " />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                    <div class="space-y-2">
                        <div v-for="(p, i) in form.phone" :key="i" class="flex gap-2">
                            <input v-model="form.phone[i]" class="flex-1 px-3 py-2 border border-gray-300  rounded-md bg-white  text-gray-900 " />
                            <button type="button" @click="removePhone(i)" class="px-2 text-red-500 hover:text-red-700">✕</button>
                        </div>
                        <button type="button" @click="addPhone" class="text-sm text-brand-primary hover:underline">+ Add phone</button>
                    </div>
                </div>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Ext. Number</label>
                    <input v-model="form.n_exterior" class="w-full px-3 py-2 border border-gray-300  rounded-md bg-white  text-gray-900 " />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Int. Number</label>
                    <input v-model="form.n_interior" class="w-full px-3 py-2 border border-gray-300  rounded-md bg-white  text-gray-900 " />
                </div>
            </div>
            <div class="flex items-center gap-6">
                <label class="flex items-center gap-2">
                    <input v-model="form.fav" type="checkbox" class="rounded border-gray-300 " />
                    <span class="text-sm text-gray-700">Favorite</span>
                </label>
                <label class="flex items-center gap-2">
                    <input v-model="form.auto_email" type="checkbox" class="rounded border-gray-300 " />
                    <span class="text-sm text-gray-700">Auto email</span>
                </label>
                <label class="flex items-center gap-2">
                    <input v-model="form.auto_whatsapp" type="checkbox" class="rounded border-gray-300 " />
                    <span class="text-sm text-gray-700">Auto WhatsApp</span>
                </label>
            </div>
            <div class="pt-4 flex gap-3">
                <button type="submit" :disabled="loading" class="px-4 py-2 bg-brand-primary hover:bg-slate-800 text-white font-medium rounded-md transition-colors disabled:opacity-50">
                    {{ loading ? 'Saving...' : 'Save' }}
                </button>
                <router-link :to="{ name: 'rooms.index' }" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 transition-colors">
                    Cancel
                </router-link>
            </div>
        </form>
    </div>
</template>
