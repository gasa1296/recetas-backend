import type { AxiosResponse } from 'axios'
import api from '../services/axios'
import type { ApiResponse, Gender } from '../types'

export function listGenders(): Promise<AxiosResponse<ApiResponse<Object>>> {
  return api.get('/genders')
}

export function listPrescriptionStatuses(): Promise<AxiosResponse<ApiResponse<Object>>> {
  return api.get('/prescription-statuses')
}
