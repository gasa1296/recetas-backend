import type { AxiosResponse } from 'axios'
import api from '../services/axios'
import type { ApiResponse, Gender, Paginated, QueryParams, University } from '../types'

export function listGenders(): Promise<AxiosResponse<ApiResponse<Gender[]>>> {
  return api.get('/genders')
}

export function listUniversities(
  params?: QueryParams,
): Promise<AxiosResponse<ApiResponse<Paginated<University>>>> {
  return api.get('/universities', { params })
}
