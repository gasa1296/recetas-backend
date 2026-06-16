import type { AxiosResponse } from 'axios'
import api from '../services/axios'
import type { ApiResponse, Profile } from '../types'

export function getProfile(): Promise<AxiosResponse<ApiResponse<Profile>>> {
  return api.get('/profile')
}

export function updateProfile(
  payload: Profile,
): Promise<AxiosResponse<ApiResponse<Profile>>> {
  return api.put('/profile', payload)
}

export function deleteProfile(): Promise<AxiosResponse<ApiResponse<unknown>>> {
  return api.delete('/profile')
}
