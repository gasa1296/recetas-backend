import type { AxiosResponse } from 'axios'
import api from '../services/axios'
import type { ApiResponse, LoginResponse } from '../types'

interface Credentials {
  email: string
  password: string
}

export function login(
  credentials: Credentials,
): Promise<AxiosResponse<ApiResponse<LoginResponse>>> {
  return api.post('/auth/login', credentials)
}

export function logout(): Promise<AxiosResponse<ApiResponse<unknown>>> {
  return api.post('/auth/logout')
}
