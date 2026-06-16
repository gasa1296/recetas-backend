import type { AxiosResponse } from 'axios'
import api from '../services/axios'
import type {
  ApiResponse,
  Patient,
  QueryParams,
} from '../types'

export function listPatients(
  params?: QueryParams,
): Promise<AxiosResponse<ApiResponse<Patient[]>>> {
  return api.get('/patients', { params })
}

export function getPatient(
  id: number | string,
): Promise<AxiosResponse<ApiResponse<Patient>>> {
  return api.get(`/patients/${id}`)
}

export function createPatient(
  payload: Patient,
): Promise<AxiosResponse<ApiResponse<Patient>>> {
  return api.post('/patients', payload)
}

export function updatePatient(
  id: number | string,
  payload: Patient,
): Promise<AxiosResponse<ApiResponse<Patient>>> {
  return api.put(`/patients/${id}`, payload)
}

export function deletePatient(
  id: number | string,
): Promise<AxiosResponse<ApiResponse<unknown>>> {
  return api.delete(`/patients/${id}`)
}
