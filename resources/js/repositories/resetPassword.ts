import type { AxiosResponse } from 'axios'
import api from '../services/axios'
import type { ApiResponse } from '../types'

export interface ResetPasswordPayload {
  email: string
  token?: string
  password?: string
  password_confirmation?: string
}

export function requestPassword(
  payload: { email: string },
): Promise<AxiosResponse<ApiResponse<unknown>>> {
  return api.post('/password/request', payload)
}

export function resetPassword(
  payload: ResetPasswordPayload,
): Promise<AxiosResponse<ApiResponse<unknown>>> {
  return api.post('/password/reset', payload)
}
