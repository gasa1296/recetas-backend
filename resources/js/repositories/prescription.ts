import type { AxiosResponse } from 'axios'
import api from '../services/axios'
import type {
  ApiResponse,
  Prescription,
  QueryParams,
} from '../types'

export function listPrescriptions(
  params?: QueryParams,
): Promise<AxiosResponse<ApiResponse<Prescription[]>>> {
  return api.get('/prescriptions', { params })
}

export function getPrescription(
  id: number | string,
): Promise<AxiosResponse<ApiResponse<Prescription>>> {
  return api.get(`/prescriptions/${id}`)
}

export function createPrescription(
  payload: Prescription,
): Promise<AxiosResponse<ApiResponse<Prescription>>> {
  return api.post('/prescriptions', payload)
}

export function updatePrescription(
  id: number | string,
  payload: Prescription,
): Promise<AxiosResponse<ApiResponse<Prescription>>> {
  return api.put(`/prescriptions/${id}`, payload)
}

export function deletePrescription(
  id: number | string,
): Promise<AxiosResponse<ApiResponse<unknown>>> {
  return api.delete(`/prescriptions/${id}`)
}

export function finishPrescription(
  id: number | string,
): Promise<AxiosResponse<ApiResponse<Prescription>>> {
  return api.post(`/prescriptions/${id}/finish`)
}
