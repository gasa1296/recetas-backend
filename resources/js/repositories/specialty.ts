import type { AxiosResponse } from 'axios'
import api from '../services/axios'
import type { ApiResponse, Paginated, QueryParams, Specialty, SpecialtyPayload } from '../types'

export function listSpecialties(
  params?: QueryParams,
): Promise<AxiosResponse<ApiResponse<Paginated<Specialty>>>> {
  return api.get('/specialties', { params })
}

export function getSpecialty(
  id: number | string,
): Promise<AxiosResponse<ApiResponse<Specialty>>> {
  return api.get(`/specialties/${id}`)
}

export function createSpecialty(
  payload: SpecialtyPayload,
): Promise<AxiosResponse<ApiResponse<Specialty>>> {
  return api.post('/specialties', payload)
}

export function updateSpecialty(
  id: number | string,
  payload: SpecialtyPayload,
): Promise<AxiosResponse<ApiResponse<Specialty>>> {
  return api.put(`/specialties/${id}`, payload)
}

export function deleteSpecialty(
  id: number | string,
): Promise<AxiosResponse<ApiResponse<unknown>>> {
  return api.delete(`/specialties/${id}`)
}
