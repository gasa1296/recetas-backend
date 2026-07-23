import type { AxiosResponse } from 'axios'
import api from '../services/axios'
import type { ApiResponse, QueryParams, Specialty } from '../types'

export function getSpecialty(
  params?: QueryParams,
): Promise<AxiosResponse<ApiResponse<Specialty>>> {
  return api.get('/specialty', { params })
}

export function createSpecialty(
  payload: Specialty,
): Promise<AxiosResponse<ApiResponse<Specialty>>> {
  return api.post('/specialty', payload)
}

export function updateSpecialty(
  payload: Specialty,
): Promise<AxiosResponse<ApiResponse<Specialty>>> {
  return api.put(`/specialty`, payload)
}
