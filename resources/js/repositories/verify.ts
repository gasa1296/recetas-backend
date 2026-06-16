import type { AxiosResponse } from 'axios'
import api from '../services/axios'
import type { ApiResponse, QueryParams } from '../types'

export function verify(
  params?: QueryParams,
): Promise<AxiosResponse<ApiResponse<unknown>>> {
  return api.get('/verification/verify', { params })
}

export function resend(): Promise<AxiosResponse<ApiResponse<unknown>>> {
  return api.post('/verification/resend')
}
