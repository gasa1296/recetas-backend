import type { Credentials, Profile } from '../types'
import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import { login as loginRequest, logout as logoutRequest } from '../repositories/auth'
import { getProfile, updateProfile as updateProfileRequest } from '../repositories/profile'

export const useAuthStore = defineStore('auth', () => {
    const token = ref<string | null>(localStorage.getItem('auth_token'))
    const user = ref<Profile | null>(JSON.parse(localStorage.getItem('auth_user') || 'null'))

    const isAuthenticated = computed(() => !!token.value)

    async function login(credentials: Credentials) {
        const { data } = await loginRequest(credentials)
        token.value = data.data.token
        user.value = data.data.profile
        localStorage.setItem('auth_token', data.data.token)
        localStorage.setItem('auth_user', JSON.stringify(data.data.profile))
    }

    async function logout() {
        try {
            await logoutRequest()
        } finally {
            token.value = null
            user.value = null
            localStorage.removeItem('auth_token')
            localStorage.removeItem('auth_user')
        }
    }

    async function fetchProfile() {
        const { data } = await getProfile()
        user.value = data.data
        localStorage.setItem('auth_user', JSON.stringify(data.data))
    }

    async function updateProfile(profileData: Profile) {
        const { data } = await updateProfileRequest(profileData)
        user.value = data.data
        localStorage.setItem('auth_user', JSON.stringify(data.data))
    }

    return { token, user, isAuthenticated, login, logout, fetchProfile, updateProfile }
})
