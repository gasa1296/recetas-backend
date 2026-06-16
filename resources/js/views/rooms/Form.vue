<script setup lang="ts">
import type { Room, RoomPayload } from '../../types'
import type { RoomForm } from '../../types'
import { ref, onMounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { getRoom, createRoom, updateRoom } from '../../repositories/rooms'

const router = useRouter()
const route = useRoute()
const isEdit = !!route.params.id

const form = ref<RoomForm>({
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
const loading = ref(false)
const error = ref('')

onMounted(async () => {
    if (isEdit) {
        const id = parseInt(route.params.id as string)
        const { data } = await getRoom(id)
        const room = { ...data.data }
        room.phone = Array.isArray(room.phone) ? room.phone : (room.phone ? [room.phone] : [])
        form.value = room
    }
})

function addPhone() {
    form.value.phone.push('')
}

function removePhone(index: number) {
    form.value.phone.splice(index, 1)
}

async function handleSubmit() {
    loading.value = true
    error.value = ''
    try {
        if (isEdit) {
            const id = parseInt(route.params.id as string)
            await updateRoom(id, form.value)
        } else {
            await createRoom(form.value)
        }
        router.push({ name: 'rooms.index' })
    } catch (err) {
        error.value =(err as any).response?.data?.message || 'Failed to save room'
    } finally {
        loading.value = false
    }
}
</script>

<template>
    <div>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-6">{{ isEdit ? 'Edit Room' : 'Create Room' }}</h1>
        <form @submit.prevent="handleSubmit" class="max-w-2xl bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-lg p-6 space-y-4">
            <div v-if="error" class="p-3 rounded-md bg-red-50 dark:bg-red-900/30 text-red-700 dark:text-red-400 text-sm">{{ error }}</div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Name *</label>
                <input v-model="form.name" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-md bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100" />
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Street</label>
                    <input v-model="form.street" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-md bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Colony</label>
                    <input v-model="form.colony" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-md bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100" />
                </div>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">State</label>
                    <input v-model="form.state" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-md bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Delegation</label>
                    <input v-model="form.delegation" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-md bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100" />
                </div>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Zip</label>
                    <input v-model="form.zip" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-md bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Phone</label>
                    <div class="space-y-2">
                        <div v-for="(p, i) in form.phone" :key="i" class="flex gap-2">
                            <input v-model="form.phone[i]" class="flex-1 px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-md bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100" />
                            <button type="button" @click="removePhone(i)" class="px-2 text-red-500 hover:text-red-700">✕</button>
                        </div>
                        <button type="button" @click="addPhone" class="text-sm text-indigo-600 dark:text-indigo-400 hover:underline">+ Add phone</button>
                    </div>
                </div>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Ext. Number</label>
                    <input v-model="form.n_exterior" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-md bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Int. Number</label>
                    <input v-model="form.n_interior" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-md bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100" />
                </div>
            </div>
            <div class="flex items-center gap-6">
                <label class="flex items-center gap-2">
                    <input v-model="form.fav" type="checkbox" class="rounded border-gray-300 dark:border-gray-700" />
                    <span class="text-sm text-gray-700 dark:text-gray-300">Favorite</span>
                </label>
                <label class="flex items-center gap-2">
                    <input v-model="form.auto_email" type="checkbox" class="rounded border-gray-300 dark:border-gray-700" />
                    <span class="text-sm text-gray-700 dark:text-gray-300">Auto email</span>
                </label>
                <label class="flex items-center gap-2">
                    <input v-model="form.auto_whatsapp" type="checkbox" class="rounded border-gray-300 dark:border-gray-700" />
                    <span class="text-sm text-gray-700 dark:text-gray-300">Auto WhatsApp</span>
                </label>
            </div>
            <div class="pt-4 flex gap-3">
                <button type="submit" :disabled="loading" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-md transition-colors disabled:opacity-50">
                    {{ loading ? 'Saving...' : 'Save' }}
                </button>
                <router-link :to="{ name: 'rooms.index' }" class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-md hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors">
                    Cancel
                </router-link>
            </div>
        </form>
    </div>
</template>
