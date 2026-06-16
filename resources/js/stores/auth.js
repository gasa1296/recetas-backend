import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import api from '../api/axios'

export const useAuthStore = defineStore('auth', () => {
    const token = ref(localStorage.getItem('auth_token'))
    const user = ref(JSON.parse(localStorage.getItem('auth_user') || 'null'))

    const isAuthenticated = computed(() => !!token.value)

    async function login(credentials) {
        const { data } = await api.post('/auth/login', credentials)
        token.value = data.data.token
        user.value = data.data.profile
        localStorage.setItem('auth_token', data.data.token)
        localStorage.setItem('auth_user', JSON.stringify(data.data.profile))
    }

    async function logout() {
        try {
            await api.post('/auth/logout')
        } finally {
            token.value = null
            user.value = null
            localStorage.removeItem('auth_token')
            localStorage.removeItem('auth_user')
        }
    }

    async function fetchProfile() {
        const { data } = await api.get('/profile')
        user.value = data.data
        localStorage.setItem('auth_user', JSON.stringify(data.data))
    }

    async function updateProfile(profileData) {
        const { data } = await api.put('/profile', profileData)
        user.value = data.data
        localStorage.setItem('auth_user', JSON.stringify(data.data))
    }

    return { token, user, isAuthenticated, login, logout, fetchProfile, updateProfile }
})
