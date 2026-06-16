import type { AxiosResponse } from 'axios'
import api from '../services/axios'
import type { ApiResponse, Profile, ProfilePayload } from '../types'

export function getProfile(): Promise<AxiosResponse<ApiResponse<Profile>>> {
  return api.get('/profile')
}

export function updateProfile(
  payload: ProfilePayload,
): Promise<AxiosResponse<ApiResponse<Profile>>> {
  return api.put('/profile', payload)
}

export function deleteProfile(): Promise<AxiosResponse<ApiResponse<unknown>>> {
  return api.delete('/profile')
}
